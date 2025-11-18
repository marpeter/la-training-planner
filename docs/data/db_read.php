<?php
namespace LaPlanner;
  
include('db_common.php');

class DisciplineReader extends AbstractTableReader {
    protected $tableNames = ['DISCIPLINES'];
    protected function deserialize() {
        return json_encode($this->data['DISCIPLINES']);
    }
}

class ExerciseReader extends AbstractTableReader {
    protected $tableNames = ['EXERCISES', 'EXERCISES_DISCIPLINES'];

    protected function deserialize() {
        foreach ($this->data['EXERCISES'] as &$exercise) {
            $exercise['warmup'] = (bool)$exercise['warmup'];
            $exercise['runabc'] = (bool)$exercise['runabc'];
            $exercise['mainex'] = (bool)$exercise['mainex'];
            $exercise['ending'] = (bool)$exercise['ending'];
            $exercise['durationmin'] = (int)$exercise['durationmin'];
            $exercise['durationmax'] = (int)$exercise['durationmax'];
            $exercise['details'] = explode(":",$exercise['details']);
            $exercise['disciplines'] = [];
            foreach ($this->data['EXERCISES_DISCIPLINES'] as $exerciseDiscipline) {
               if ($exercise['id'] == $exerciseDiscipline['exercise_id']) {
                   $exercise['disciplines'][] = $exerciseDiscipline['discipline_id'];
               }
            }
        }        
        return json_encode($this->data['EXERCISES']);
    }
}

class FavoriteReader extends AbstractTableReader {
    protected $tableNames = ['FAVORITE_HEADERS', 'FAVORITE_DISCIPLINES', 'FAVORITE_EXERCISES'];

    protected function deserialize() {
        foreach ($this->data['FAVORITE_HEADERS'] as &$favorite) {
            $favorite['disciplines'] = [];
            foreach ($this->data['FAVORITE_DISCIPLINES'] as $favoriteDiscipline) {
                if ($favorite['id'] == $favoriteDiscipline['favorite_id']) {
                    $favorite['disciplines'][] = $favoriteDiscipline['discipline_id'];
                }
            }
        }
        return json_encode(['headers' => $this->data['FAVORITE_HEADERS'], 'exerciseMap' => $this->data['FAVORITE_EXERCISES']]);
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