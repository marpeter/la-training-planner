<?php

  function explode_line($line, $separator=',') {
    $data = explode($separator,$line);
    return $data;
  }

  function fields_from_line($line, $fieldNames, $fieldMap, $separator=',') {
    $values = explode_line($line, $separator);
    $fields = [];
    foreach($fieldNames as $field) {
      if(str_ends_with($field,'[]')) {
        $fields[$field] = explode(':', trim($values[$fieldMap[$field]]));
      } else {
        $fields[$field] = trim($values[$fieldMap[$field]]);
      }
    }
    return $fields;
  }

  function checkHeader($data, $expected, $dataName, &$messages, $separator=',') {
    // preg_replace removes a potential \xEFBBBF UTF8 "ZERO WIDTH NO-BREAK SPACE" character at the beginning
    $header = explode_line(preg_replace("/\xEF\xBB\xBF/", "", $data), $separator);
    $expectedNum = count($expected);
    if(count($header)!=$expectedNum) {
      $messages[] = "Die Kopfzeile der $dataName-Daten $data enthält nicht die erwartete Anzahl an $expectedNum Spalten.";
      return false;
    }
    // Convert field names to lower case
    foreach($header as &$field) { $field = strtolower(trim($field)); }
    // Check that only valid field names are used
    if(count(array_diff($header,$expected))>0) {
      $expectedStr = implode(',',$expected);
      $actualStr = implode(',', array_diff($header,$expected));
      $messages[] = "Die Kopfzeile der $dataName Daten $data enthält nicht die erwarteten Spaltennamen $expectedStr, der Unterschied ist $actualStr.";
      print_r(mb_ord($header[0]));
      return false;
    }
    // "pivot" the header
    $fieldMap = [];
    foreach($expected as $field) {
      $fieldMap[$field] = array_search($field, $header);
    }
    return $fieldMap;
  }

  function loadDisciplines($data, &$messages) {

    $headerFields = array('id','name','image');
    $header = checkHeader( $data[0], $headerFields, 'Disziplin', $messages);
    if(!$header) {
      return 0;
    }

    $disciplines = [];
    $max=count($data);
    for($line=1; $line<$max; $line++) {
       $disciplines[] = fields_from_line($data[$line], $headerFields, $header);
    }

    // process the data
    // 1. remove current database entries
    $dbConnection = connectDB();
    $dbConnection->autocommit(false);
    $rollback = false;
    $result = $dbConnection->query('DELETE FROM DISCIPLINES');
    // 2. insert the uploaded disciplines
    $stmt_header = $dbConnection->prepare('INSERT INTO DISCIPLINES (id, name, image) VALUES (?, ?, ?)');
    $stmt_header->bind_param('sss', $id, $name, $image);
    foreach($disciplines as ['id' => $id, 'name' => $name, 'image' => $image]) {
      try {
        if(!$stmt_header->execute()) {
          $messages[] =  'Einfügen der Zeile $id, $name, $image ist fehlgeschlagen.';
          $rollback = true;
        }
      } catch( mysqli_sql_exception $ex) {
        $error = $ex->getMessage();
        $messages[] = "Disziplin $id in die Datenbank laden ist fehlgeschlagen, Fehler: $error.";
        $rollback = true;
      }
    }
    $stmt_header->close();
    // 3. commit or rollback
    if($rollback) {
      $dbConnection->rollback();
      $dbConnection->close();
      $messages[] = "Es wurden keine Disziplinen geladen.";
      return 0;
    } else {
      $dbConnection->commit();
      $dbConnection->close();
  
      $c1 = count($disciplines);
      $messages[] = "Es wurden $c1 Disziplinen geladen.";
      return 1;
    }
  }
?>