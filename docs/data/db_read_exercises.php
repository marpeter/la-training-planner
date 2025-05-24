<?php
  namespace LaPlanner;
  
  include('db_connect.php');

  class ExerciseReader extends AbstractTableReader {
    protected $tableNames = ['EXERCISES', 'EXERCISES_DISCIPLINES'];

    protected function convert() {
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

  class ExerciseReaderCSV extends ExerciseReader {
    use TableReaderCSV;
  
    protected function setHeader() {
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="Exercises.csv"');
    }

    public function convert() {
      // TODO: remove the "Auslaufen" exercise from the CSV to prevent it from being modified by mistake
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

  if(isset($_GET['format']) && $_GET['format'] == 'csv') {
    $reader = new ExerciseReaderCSV();
    $reader->echo();
  } else {
    $reader = new ExerciseReader();
    $reader->echo();
  }

?>