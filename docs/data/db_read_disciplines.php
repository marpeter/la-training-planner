<?php
  namespace LaPlanner;
  
  include('db_connect.php');

  class DisciplineReader extends AbstractTableReader {
      protected $tableNames = ['DISCIPLINES'];
      protected function convert() {
        return json_encode($this->data['DISCIPLINES']);
      }
  }

  class DisciplineReaderCSV extends DisciplineReader {

    use TableReaderCSV;

    protected function setHeader() {
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="Disciplines.csv"');
    }

    public function convert() {
      foreach($this->data['DISCIPLINES'] as &$discipline) {
        unset($discipline['created_at']);
      }
      $csv_header = array_keys($this->data['DISCIPLINES'][0]);
      $contents = implode(",", $csv_header) . "\n";
      $contents .= $this->convertToCsv($this->data['DISCIPLINES']);
      return $contents;
    }
  }

  if(isset($_GET['format']) && $_GET['format'] == 'csv') {
    $reader = new DisciplineReaderCSV();
    $reader->echo();
  } else {
    $reader = new DisciplineReader();
    $reader->echo();
  }
?>