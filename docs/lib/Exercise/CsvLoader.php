<?php
namespace TnFAT\Planner\Exercise;

class CsvLoader extends \LaPlanner\DataLoader {
    protected $headerFields = [ ['id', 'name', 'warmup', 'runabc', 'mainex', 'ending', 
        'material', 'durationmin', 'durationmax', 'repeats', 'disciplines[]', 'details[]']];
    protected $entityNames = ['Übung'];

    public function __construct() {
        $this->csvParser = new \LaPlanner\CsvParser(';');
        $this->saver = new DatabaseWriter();
    }

    // override to exclude "Auslaufen" exercises
    protected function getTableNames(): array {
        $tableNames = $this->saver->getTableNames();
        $tableNames[0] = $tableNames[0] . ' WHERE NOT id="Auslaufen"';
        return $tableNames;
    }
}