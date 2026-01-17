<?php
namespace TnFAT\Planner\Discipline;

class CsvReader extends \TnFAT\Planner\AbstractDatabaseToCsvConverter {
    use DatabaseTable;

    protected $fileName = 'Disciplines.csv';
    public function deserialize(): string {
        foreach($this->data[self::HEADER_TABLE] as &$discipline) {
            unset($discipline['created_at']);
        }
        $csv_header = array_keys($this->data[self::HEADER_TABLE][0]);
        $contents = implode(",", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data[self::HEADER_TABLE]);
        return $contents;
    }
}