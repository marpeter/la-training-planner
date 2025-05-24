<?php
  namespace LaPlanner;

  include('db_connect.php');
     
  class FavoriteReader extends AbstractTableReader {
      protected $tableNames = ['FAVORITE_HEADERS', 'FAVORITE_DISCIPLINES', 'FAVORITE_EXERCISES'];

      protected function convert() {
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

class FavoriteReaderCSV extends FavoriteReader {
    use TableReaderCSV;

    protected function setHeader() {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Favorites.csv"');
    }

    public function convert() {
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
 
  if(isset($_GET['format']) && $_GET['format'] == 'csv') {
        $reader = new FavoriteReaderCSV();
        $reader->echo();
  } else {
        $reader = new FavoriteReader();
        $reader->echo();
  }

?>