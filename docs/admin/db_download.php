<?php
namespace LaPlanner;

require '../data/db_common.php';

class DisciplineReader extends AbstractTableToCsvReader {
    use DisciplineTable;

    protected $fileName = 'Disciplines.csv';
    public function deserialize() {
        foreach($this->data[self::HEADER_TABLE] as &$discipline) {
            unset($discipline['created_at']);
        }
        $csv_header = array_keys($this->data[self::HEADER_TABLE][0]);
        $contents = implode(",", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data[self::HEADER_TABLE]);
        return $contents;
    }
}

class ExerciseReader extends AbstractTableToCsvReader {
    use ExerciseTable;

    protected $fileName = 'Exercises.csv';
    public function deserialize() {
        // Remove the "Auslaufen" exercise from the CSV to prevent it from being modified by mistake
        $this->data[self::HEADER_TABLE] = array_filter($this->data[self::HEADER_TABLE], function($exercise) {
            return $exercise['name'] !== 'Auslaufen';
        });
        foreach($this->data[self::HEADER_TABLE] as &$exercise) {
            $exercise['warmup'] = $exercise['warmup']==1 ? "true" : "false";
            $exercise['runabc'] = $exercise['runabc']==1 ? "true" : "false";
            $exercise['mainex'] = $exercise['mainex']==1 ? "true" : "false";
            $exercise['ending'] = $exercise['ending']==1 ? "true" : "false";
            $exercise['durationmin'] = (int)$exercise['durationmin'];
            $exercise['durationmax'] = (int)$exercise['durationmax'];
            $exercise['Disciplines[]'] = array();
            foreach($this->data[self::LINK_DISCIPLINES_TABLE] as $exerciseDiscipline) {
                if ($exercise['id'] == $exerciseDiscipline['exercise_id']) {
                    $exercise['Disciplines[]'][] = $exerciseDiscipline['discipline_id'];
                }
            }
            $exercise['Disciplines[]'] = implode(":", $exercise['Disciplines[]']);
            unset($exercise['created_at']);         
        }
        $csv_header = array_keys($this->data[self::HEADER_TABLE][0]);
        $details_index = array_search("details", $csv_header);
        $csv_header[$details_index] = "Details[]";
        $contents = implode(";", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data[self::HEADER_TABLE],';');
        return $contents;
    }
}

class FavoriteReader extends AbstractTableToCsvReader {
    use FavoriteTable;

    protected $fileName = 'Favorites.csv';
    public function deserialize() {
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
switch(strtolower($_GET['entity'])) {
    case 'disciplines':
        $reader = new DisciplineReader();
        break;
    case 'exercises':
        $reader = new ExerciseReader();
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