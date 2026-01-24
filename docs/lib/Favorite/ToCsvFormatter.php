<?php
namespace TnFAT\Planner\Favorite;

class ToCsvFormatter extends \TnFAT\Planner\AbstractEntityToCsvFormatter {

    protected $fileName = 'Favorites.csv';

    public function __construct() {
        $this->reader = new DatabaseTable();
    }

    public function format(): string {
        foreach ($this->data[DatabaseTable::HEADER_TABLE] as &$favorite) {
            $favorite['Disciplines[]'] = array();
            foreach ($this->data[DatabaseTable::LINK_DISCIPLINES_TABLE] as $favoriteDiscipline) {
                if ($favorite['id'] == $favoriteDiscipline['favorite_id']) {
                    $favorite['Disciplines[]'][] = $favoriteDiscipline['discipline_id'];
                }
            }
            $favorite['Disciplines[]'] = implode(":", $favorite['Disciplines[]']);
            //unset($favorite['created_at']);
        }
        $csv_header = array_keys($this->data[DatabaseTable::HEADER_TABLE][0]);
        $contents = implode(",", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data[DatabaseTable::HEADER_TABLE]);
        $contents .= "\n";
        $csv_header = array_keys($this->data[DatabaseTable::LINK_EXERCISES_TABLE][0]);
        $contents .= implode(",", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data[DatabaseTable::LINK_EXERCISES_TABLE]);
        
        return $contents;
    }
}