<?php
namespace TnFAT\Planner\Favorite;

class CsvReader extends \LaPlanner\AbstractTableToCsvReader {
    use DatabaseTable;

    protected $fileName = 'Favorites.csv';
    public function deserialize(): string {
        foreach ($this->data[self::HEADER_TABLE] as &$favorite) {
            $favorite['Disciplines[]'] = array();
            foreach ($this->data[self::LINK_DISCIPLINES_TABLE] as $favoriteDiscipline) {
                if ($favorite['id'] == $favoriteDiscipline['favorite_id']) {
                    $favorite['Disciplines[]'][] = $favoriteDiscipline['discipline_id'];
                }
            }
            $favorite['Disciplines[]'] = implode(":", $favorite['Disciplines[]']);
            //unset($favorite['created_at']);
        }
        $csv_header = array_keys($this->data[self::HEADER_TABLE][0]);
        $contents = implode(",", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data[self::HEADER_TABLE]);
        $contents .= "\n";
        $csv_header = array_keys($this->data[self::LINK_EXERCISES_TABLE][0]);
        $contents .= implode(",", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data[self::LINK_EXERCISES_TABLE]);
        
        return $contents;
    }
}