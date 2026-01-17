<?php
namespace LaPlanner;
  
require 'db_common.php';

use \TnFAT\Planner\Exercise\DatabaseReader;

class DisciplineReader extends AbstractTableReader {
    use \TnFAT\Planner\DisciplineTable;

    protected function deserialize(): string {
        return json_encode($this->data[self::HEADER_TABLE]);
    }
}

class FavoriteReader extends AbstractTableReader {
    use \TnFAT\Planner\FavoriteTable;

    protected function deserialize(): string {
        foreach ($this->data[self::HEADER_TABLE] as &$favorite) {
            $favorite['disciplines'] = [];
            foreach ($this->data[self::LINK_DISCIPLINES_TABLE] as $favoriteDiscipline) {
                if ($favorite['id'] == $favoriteDiscipline['favorite_id']) {
                    $favorite['disciplines'][] = $favoriteDiscipline['discipline_id'];
                }
            }
        }
        return json_encode(
            ['headers' => $this->data[self::HEADER_TABLE],
            'exerciseMap' => $this->data[self::LINK_EXERCISES_TABLE]]);
    }
}

$reader = null;
switch(strtolower($_GET['entity'])) {
    case 'disciplines':
        $reader = new DisciplineReader();
        break;
    case 'exercises':
        $reader = new DatabaseReader();
        break;
    case 'favorites':
        $reader = new FavoriteReader();
        break;
    default:
        http_response_code(400);
        echo "Unknown entity: " . htmlspecialchars($_GET['entity']);
        exit;
}

$reader->echo();