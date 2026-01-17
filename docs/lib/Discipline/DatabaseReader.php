<?php
namespace TnFAT\Planner\Discipline;

class DatabaseReader extends \TnFAT\Planner\AbstractDatabaseReader {
    use DatabaseTable;

    protected function deserialize(): string {
        return json_encode($this->data[self::HEADER_TABLE]);
    }
}