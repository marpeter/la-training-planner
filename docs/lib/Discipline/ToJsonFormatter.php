<?php

namespace TnFAT\Planner\Discipline;

class ToJsonFormatter extends \TnFAT\Planner\AbstractEntityToJsonFormatter {

    public function __construct() {
        $this->reader = new DatabaseTable();
    }

    public function format(): string {
        return json_encode($this->data[DatabaseTable::HEADER_TABLE]);
    }
}