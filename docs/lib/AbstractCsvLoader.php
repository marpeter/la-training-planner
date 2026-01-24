<?php
namespace TnFAT\Planner;

abstract class AbstractCsvLoader {
    protected $messages = [];
    protected $dbConnection = null;
    // override these properties in each concrete class:
    protected $headerFields = [];
    protected $entityNames = [];
    // initialize these properties in the __construct method of each concrete class:
    protected ?CsvParser $csvParser = null;
    protected ?AbstractDatabaseTable $saver = null;

    public function load($data, &$messages): int {
        $this->messages = &$messages;
        try {
            $tableContent = $this->csvParser->parseTables($data,
                $this->headerFields, $this->getTableNames());
        } catch(CsvParseException $ex) {
            $this->messages[] = $ex->getMessage();
            return 0;
        }
        // process the data
        $this->dbConnection = \LaPlanner\connectDB();
        $this->dbConnection->beginTransaction();
        // 1. remove current database entries
        $okay = $this->clearCurrentEntries();
        // 2. insert the uploaded disciplines
        $okay = $this->insertEntries($tableContent);
        // 3. commit or rollback
        if($okay) {
            $this->dbConnection->commit();
            $c1 = count($tableContent[0]);
            $messages[] = "Es wurden $c1 {$this->entityNames[0]}en geladen.";
            return 1;
        } else {
            $this->dbConnection->rollBack();
            $messages[] = "Es wurden keine {$this->entityNames[0]}en geladen.";
            return 0;
        }
    }

    protected function getTableNames(): array {
        return $this->saver->getTableNames();
    }

    protected function clearCurrentEntries(): bool {
        try {
            foreach($this->getTableNames() as $tableName) {
                $result = $this->dbConnection->exec("DELETE FROM $tableName");
            }
            return true;
        } catch( \PDOException $ex) {
            $error = $ex->getMessage();
            $this->messages[] = "Löschen der vorhanden {$this->entityNames[0]} Daten fehlgeschlagen, Fehler: $error.";
            return false;
        }      
    }

    protected function insertEntries($tableEntries): bool {
        $new_messages = $this->saver->createEntityBulk($tableEntries, $this->dbConnection);
        $this->messages = array_merge($this->messages,$new_messages);
        return !$new_messages; // if there are messages, something went wrong
    }
}