<?php
namespace TnFAT\Planner\Discipline;

class ToCsvFormatter extends \TnFAT\Planner\AbstractEntityToCsvFormatter {
    protected $fileName = 'Disciplines.csv';

    public function __construct() {
        $this->reader = new DatabaseTable();
    }

    public function format(): string {
        foreach($this->data[DatabaseTable::HEADER_TABLE] as &$discipline) {
            unset($discipline['created_at']);
        }
        $csv_header = array_keys($this->data[DatabaseTable::HEADER_TABLE][0]);
        $contents = implode(",", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data[DatabaseTable::HEADER_TABLE]);
        return $contents;
    }
}