<?php
namespace LaPlanner;
include('../data/db_common.php'); 

abstract class DataSaver {
    protected $ENTITY = "";

    abstract protected function createEntity($data, $dbConnection);
    abstract protected function updateEntity($data, $dbConnection);
    abstract protected function deleteEntity($id, $dbConnection);

    public final function create($data) {
        return $this->doAction($data, 'createEntity', 'Anlegen');
    }
    public final function update($data) {
        return $this->doAction($data, 'updateEntity', 'Ändern');
    }
    public final function delete($id) {
        return $this->doAction($id, 'deleteEntity', 'Löschen');
    }
    private function doAction($data, $action, $actionName) {
        $result = "";
        try {
            $dbConnection = connectDB();
            $dbConnection->autocommit(false);
            $this->$action($data, $dbConnection);
            $dbConnection->commit();
            $result =  [
                'success' => true,
                'message' => $data,
            ];
        } catch (mysqli_sql_exception $ex) {
            $dbConnection->rollback();
            return [
                'success' => false,
                'message' => "Fehler beim {$actionName} der {$ENTITY}: " . $ex->getMessage(),
            ];
        } finally {
            $dbConnection->close();
        }
        return $result;
    }
}

class ExerciseSaver extends DataSaver {
    protected $ENTITY = "Übung";

    protected function createEntity($exercise, $dbConnection) {
        $this->convertPhaseFlags($exercise);
        $stmt = $dbConnection->prepare(
            'INSERT INTO EXERCISES (id, name, warmup, runabc, mainex, ending, durationmin, durationmax, material, repeats, details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssiiiiiisss',  $exercise['id'], $exercise['name'], $exercise['warmup'], $exercise['runabc'], $exercise['mainex'],
            $exercise['ending'], $exercise['durationmin'], $exercise['durationmax'], $exercise['material'], $exercise['repeats'],
            $exercise['details']);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Erstellen der Übung: ' . $stmt->error);
        }
        $stmt->close();
        $this->insertDependants($exercise,$dbConnection);
    }
    protected function updateEntity($exercise, $dbConnection) {
        $this->convertPhaseFlags($exercise);
        $stmt = $dbConnection->prepare(
            'UPDATE EXERCISES SET name=?, warmup=?, runabc=?, mainex=?, ending=?, durationmin=?, durationmax=?, material=?, repeats=?, details=? WHERE id = ?');
        $stmt->bind_param('siiiiiissss', $exercise['name'], $exercise['warmup'], $exercise['runabc'], $exercise['mainex'],
            $exercise['ending'], $exercise['durationmin'], $exercise['durationmax'], $exercise['material'], $exercise['repeats'],
            $exercise['details'], $exercise['id']);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Ändern der Übung: ' . $stmt->error);
        }
        $stmt->close();
        $this->deleteDependants($exercise,$dbConnection);
        $this->insertDependants($exercise,$dbConnection);
    }
    protected function deleteEntity($exerciseId, $dbConnection) {
        $stmt = $dbConnection->prepare('DELETE FROM EXERCISES WHERE id = ?');
        $stmt->bind_param('s', $exerciseId);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Löschen der Übung: ' . $stmt->error);
        }
        $stmt->close();
        $this->deleteDependants($exercise,$dbConnection);
        $stmt = $dbConnection->prepare('DELETE FROM FAVORITE_EXERCISES WHERE exercise_id=?');
        $stmt->bind_param('s', $exerciseId);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Löschen der Übung: ' . $stmt->error);
        }
        $stmt->close();
    }

    private function convertPhaseFlags(&$exercise) {
        $exercise['warmup'] = $exercise['warmup']=="true" ? 1 : 0;
        $exercise['runabc'] = $exercise['runabc']=="true" ? 1 : 0;
        $exercise['mainex'] = $exercise['mainex']=="true" ? 1 : 0;
        $exercise['ending'] = $exercise['ending']=="true" ? 1 : 0;
    }
    private function deleteDependants($exercise, $dbConnection) {
        $stmt = $dbConnection->prepare('DELETE FROM EXERCISES_DISCIPLINES WHERE exercise_id=?');
        $stmt->bind_param('s', $exercise['id']);
        $stmt->execute();
        $stmt->close();
        // note that FAVORITE_EXERCISES are NOT deleted to prevent them from being delete in the update case
        //      because the exercise data would not include update information for FAVORITE_EXERCISES
    }
    private function insertDependants($exercise, $dbConnection) {
        $stmt = $dbConnection->prepare('INSERT INTO EXERCISES_DISCIPLINES (exercise_id, discipline_id) VALUES (?, ?)');
        $stmt->bind_param('ss', $exercise['id'], $discipline_id);
        foreach($exercise['disciplines'] as $discipline_id){
            if(!$stmt->execute()) {
                  throw new mysqli_sql_exception('Fehler beim Erstellen der Disziplinen der Übung: ' . $stmt->error);
            }
        }
        $stmt->close();
    }
}

class FavoriteSaver extends DataSaver {
    protected $ENTITY = "Favoriten";

    protected function createEntity($favorite, $dbConnection) {
        $stmt = $dbConnection->prepare('INSERT INTO FAVORITE_HEADERS (id, created_by, description) VALUES (?, ?, ?)');
        $stmt->bind_param('iss', $favorite['id'], $favorite['created_by'], $favorite['description']);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Erstellen des Favoriten: ' . $stmt->error);
        }
        $stmt->close();
        $this->insertDependants($favorite, $dbConnection);
    }

    protected function updateEntity($favorite, $dbConnection) {
        $stmt = $dbConnection->prepare('UPDATE FAVORITE_HEADERS SET description = ? WHERE id = ?');
        $stmt->bind_param('si', $favorite['description'], $favorite['id']);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Ändern des Favoriten: ' . $stmt->error);
        }
        $stmt->close();
        $this->deleteDependants($favorite['id'], $dbConnection);
        $this->insertDependants($favorite, $dbConnection);
    }

    protected function deleteEntity($favoriteId, $dbConnection) {
        $stmt = $dbConnection->prepare('DELETE FROM FAVORITE_HEADERS WHERE id = ?');
        $stmt->bind_param('i', $favoriteId);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Löschen des Favoriten: ' . $stmt->error);
        }
        $stmt->close();
        $this->deleteDependants($favoriteId, $dbConnection);
    }

    private function insertDependants($favorite, $dbConnection) {
        $stmt = $dbConnection->prepare('INSERT INTO FAVORITE_DISCIPLINES (favorite_id, discipline_id) VALUES (?, ?)');
        $stmt->bind_param('is', $favorite['id'], $discipline_id);
        foreach($favorite['disciplines'] as $discipline) {
            $discipline_id = $discipline['id'];
            if(!$stmt->execute()) {
                throw new mysqli_sql_exception('Fehler beim Erstellen der Disziplinen des Favoriten: ' . $stmt->error);
            }
        }
        $stmt->close();
        $stmt = $dbConnection->prepare('INSERT INTO FAVORITE_EXERCISES (favorite_id, exercise_id, phase, position, duration) VALUES (?, ?, ?, ?, ?)');
        foreach(['warmup', 'mainex', 'ending'] as $phase) {
            $position = 1;
            foreach($favorite[$phase] as $exercise) {
                $stmt->bind_param('issii', $favorite['id'], $exercise['id'], $phase, $position, $exercise['duration']);
                if(!$stmt->execute()) {
                    throw new mysqli_sql_exception('Fehler beim Erstellen der Übungen im Favoriten: ' . $stmt->error);
                }
                $position++;
            }
        }
        $stmt->close();
    }

    private function deleteDependants($favoriteId, $dbConnection) {
        $stmt = $dbConnection->prepare('DELETE FROM FAVORITE_DISCIPLINES WHERE favorite_id = ?');
        $stmt->bind_param('i', $favoriteId);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Löschen der Disziplinen des Favoriten: ' . $stmt->error);
        }
        $stmt->close();
        $stmt = $dbConnection->prepare('DELETE FROM FAVORITE_EXERCISES WHERE favorite_id = ?');
        $stmt->bind_param('i', $favoriteId);
        if(!$stmt->execute()) {
            throw new mysqli_sql_exception('Fehler beim Löschen der Übungen im Favoriten: ' . $stmt->error);
        }
        $stmt->close();
    }
}

$SAVERS = ['exercise' => 'LaPlanner\ExerciseSaver', 'favorite' => 'LaPLanner\FavoriteSaver'];
$VERBS = ['create', 'update', 'delete'];

if(!(isset($_POST['entity']) && isset($SAVERS[$_POST['entity']]))) {
    echo json_encode([
        'success' => false,
        'message' => 'You must specify a valid entity',
    ]);
    exit;
} else {
    $saver = new $SAVERS[$_POST['entity']]();
}
if(!(isset($_POST['verb']) && in_array($_POST['verb'],$VERBS))) {
   echo json_encode([
        'success' => false,
        'message' => 'You must specify a valid verb (create, update or delete): ' . $_POST['verb'],
    ]);
    exit;
}
if(!isset($_POST['data'])) {
   echo json_encode([
        'success' => false,
        'message' => 'You must specify data',
    ]);
    exit;
}

$action = $_POST['verb'];
echo json_encode(
    $saver->$action(
        json_decode($_POST['data'],true)
));

?>