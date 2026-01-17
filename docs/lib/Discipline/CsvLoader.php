<?php
namespace TnFAT\Planner\Discipline;

class CsvLoader extends \LaPlanner\DataLoader {
    protected $headerFields = [['id', 'name', 'image']];
    protected $entityNames = ['Disziplin'];

    public function __construct() {
        $this->csvParser = new \LaPlanner\CsvParser();
        $this->saver = new DatabaseWriter();
    }
}