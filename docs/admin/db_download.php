<?php
namespace LaPlanner;

include('../data/db_common.php');

class DisciplineReader extends AbstractTableToCsvReader {
    protected $tableNames = ['DISCIPLINES'];
    protected $fileName = 'Disciplines.csv';
    public function deserialize() {
        foreach($this->data['DISCIPLINES'] as &$discipline) {
            unset($discipline['created_at']);
        }
        $csv_header = array_keys($this->data['DISCIPLINES'][0]);
        $contents = implode(",", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data['DISCIPLINES']);
        return $contents;
    }
}

class ExerciseReader extends AbstractTableToCsvReader {
    protected $tableNames = ['EXERCISES', 'EXERCISES_DISCIPLINES'];
    protected $fileName = 'Exercises.csv';
    public function deserialize() {
        // Remove the "Auslaufen" exercise from the CSV to prevent it from being modified by mistake
        $this->data['EXERCISES'] = array_filter($this->data['EXERCISES'], function($exercise) {
            return $exercise['name'] !== 'Auslaufen';
        });
        foreach($this->data['EXERCISES'] as &$exercise) {
            $exercise['warmup'] = $exercise['warmup']==1 ? "true" : "false";
            $exercise['runabc'] = $exercise['runabc']==1 ? "true" : "false";
            $exercise['mainex'] = $exercise['mainex']==1 ? "true" : "false";
            $exercise['ending'] = $exercise['ending']==1 ? "true" : "false";
            $exercise['durationmin'] = (int)$exercise['durationmin'];
            $exercise['durationmax'] = (int)$exercise['durationmax'];
            $exercise['Disciplines[]'] = array();
            foreach($this->data['EXERCISES_DISCIPLINES'] as $exerciseDiscipline) {
                if ($exercise['id'] == $exerciseDiscipline['exercise_id']) {
                    $exercise['Disciplines[]'][] = $exerciseDiscipline['discipline_id'];
                }
            }
            $exercise['Disciplines[]'] = implode(":", $exercise['Disciplines[]']);
            unset($exercise['created_at']);         
        }
        $csv_header = array_keys($this->data['EXERCISES'][0]);
        $details_index = array_search("details", $csv_header);
        $csv_header[$details_index] = "Details[]";
        $contents = implode(";", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data['EXERCISES'],';');
        return $contents;
    }
}

class FavoriteReader extends AbstractTableToCsvReader {
    protected $tableNames = ['FAVORITE_HEADERS', 'FAVORITE_DISCIPLINES', 'FAVORITE_EXERCISES'];
    protected $fileName = 'Favorites.csv';
    public function deserialize() {
        foreach ($this->data['FAVORITE_HEADERS'] as &$favorite) {
            $favorite['Disciplines[]'] = array();
            foreach ($this->data['FAVORITE_DISCIPLINES'] as $favoriteDiscipline) {
                if ($favorite['id'] == $favoriteDiscipline['favorite_id']) {
                    $favorite['Disciplines[]'][] = $favoriteDiscipline['discipline_id'];
                }
            }
            $favorite['Disciplines[]'] = implode(":", $favorite['Disciplines[]']);
            //unset($favorite['created_at']);
        }
        $csv_header = array_keys($this->data['FAVORITE_HEADERS'][0]);
        $contents = implode(",", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data['FAVORITE_HEADERS']);
        $contents .= "\n";
        $csv_header = array_keys($this->data['FAVORITE_EXERCISES'][0]);
        $contents .= implode(",", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data['FAVORITE_EXERCISES']);
        
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

?>