<?php
namespace TnFAT\Planner\Setup;

use \TnFAT\Planner\Utils;

class DataLoader {

    public const ALL = [
        ['Discipline', 'Disciplines.csv', __DIR__ . '/../../data/Disciplines.csv'],
        ['Exercise', 'Exercises.csv', __DIR__ . '/../../data/Exercises.csv'],
        ['Favorite', 'Favorites.csv', __DIR__ . '/../../data/Favorites.csv']];

    private const ENTITIES = ['Discipline', 'Exercise', 'Favorite'];

    private array $workItems;
    private array $messages = [];

    public function __construct(array $workItems) {
        $this->workItems = $workItems;
    }

    public function load(): int {
        $uploadedFiles = 0;
        foreach($this->workItems as [$entity, $logicalFileName, $realFileName]) {
            if(in_array($entity, self::ENTITIES)) {
                $loaderClass = '\TnFAT\Planner\\'  . $entity . '\\CsvLoader';
                $loader = new $loaderClass();
                $content = Utils::readFromFile($realFileName, $logicalFileName, $this->messages);
                $uploadedFiles += $loader->load($content, $this->messages); 
            } else {
                $this->messages[] = "Unbekannte Art von Daten $entity soll geladen werden.";
            }
        }
        if( $uploadedFiles === 0 ) {
            $this->messages[] = "Es wurden keine (gültigen) Daten hochgeladen.";
        } else {
            $this->messages[] = "Es wurden $uploadedFiles gültige Daten hochgeladen.";
        }
        return $uploadedFiles;
    }

    public function getMessages(): array {
        return $this->messages;
    }
}