<?php
namespace TnFAT\Planner\Discipline;

class CsvLoader extends \TnFAT\Planner\AbstractCsvLoader {
    protected $headerFields = [['id', 'name', 'image']];
    protected $entityNames = ['Disziplin'];

    public function __construct() {
        $this->csvParser = new \TnFAT\Planner\CsvParser();
        $this->saver = new DatabaseWriter();
    }
}