<?php
namespace TnFAT\Planner\Discipline;

trait DatabaseTable {
    const HEADER_TABLE = 'disciplines';
    public function getTableNames(): array {
        return [self::HEADER_TABLE];
    }
}