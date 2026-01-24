<?php namespace TnFAT\Planner\Favorite;

class CsvLoader extends \TnFAT\Planner\AbstractCsvLoader {
    protected $headerFields = [['id', 'created_by', 'created_at', 'description', 'disciplines[]'],
                               ['favorite_id', 'phase', 'position', 'exercise_id', 'duration']];
    protected $entityNames = ['Favorit', 'FavoritenÜbungen'];

    public function __construct() {
        $this->csvParser = new \TnFAT\Planner\CsvParser();
        $this->saver = new DatabaseTable();
    }
}