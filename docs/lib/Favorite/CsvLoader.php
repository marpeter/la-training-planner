<?php namespace TnFAT\Planner\Favorite;

class CsvLoader extends \LaPlanner\DataLoader {
    protected $headerFields = [['id', 'created_by', 'created_at', 'description', 'disciplines[]'],
                               ['favorite_id', 'phase', 'position', 'exercise_id', 'duration']];
    protected $entityNames = ['Favorit', 'FavoritenÜbungen'];

    public function __construct() {
        $this->csvParser = new \LaPlanner\CsvParser();
        $this->saver = new DatabaseWriter();
    }
}