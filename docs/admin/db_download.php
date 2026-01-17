<?php
namespace LaPlanner;

require '../data/db_common.php';

class FavoriteReader extends AbstractTableToCsvReader {
    use \TnFAT\Planner\FavoriteTable;

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

$reader = null;
$entity = strtolower(getQueryString('entity'));
switch($entity) {
    case 'disciplines':
        $reader = new \TnFAT\Planner\Discipline\CsvReader();
        break;
    case 'exercises':
        $reader = new \TnFAT\Planner\Exercise\CsvReader();
        break;
    case 'favorites':
        $reader = new FavoriteReader();
        break;
    default:
        http_response_code(400);
        echo "Unknown entity: " . htmlspecialchars($entity);
        exit;
}

$reader->echo();