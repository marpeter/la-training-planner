<?php
namespace TnFAT\Planner;

trait DisciplineTable {
    const HEADER_TABLE = 'disciplines';
    public function getTableNames(): array {
        return [self::HEADER_TABLE];
    }
}