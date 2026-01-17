<?php
namespace TnFAT\Planner\Favorite;

trait DatabaseTable {
    const HEADER_TABLE = 'favorite_headers';
    const LINK_DISCIPLINES_TABLE = 'favorite_disciplines';
    const LINK_EXERCISES_TABLE = 'favorite_exercises';
    public function getTableNames(): array {
        return [self::HEADER_TABLE, self::LINK_DISCIPLINES_TABLE, self::LINK_EXERCISES_TABLE];
    }
}