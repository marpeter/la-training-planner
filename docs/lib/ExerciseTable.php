<?php
namespace TnFAT\Planner;

trait ExerciseTable {
    const HEADER_TABLE = 'exercises';
    const LINK_DISCIPLINES_TABLE = 'exercises_disciplines';
    public function getTableNames(): array {
        return [self::HEADER_TABLE, self::LINK_DISCIPLINES_TABLE];
    }
}