<?php
namespace TnFAT\Planner\Discipline;

class ToJsonReader extends \TnFAT\Planner\AbstractEntityReader {
    use DatabaseTable;

    protected function format(): string {
        return json_encode($this->data[self::HEADER_TABLE]);
    }
}