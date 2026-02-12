<?php

use PHPUnit\Framework\TestCase;
use \TnFAT\Planner\CsvParser;
use \TnFAT\Planner\CsvParseException;

class CsvParserTest extends TestCase {

    const EXPECTED_FIELDS = [['feld1', 'feld2', 'feld3']];

    public function testParsingWrongHeaderFails() {
        $parser = new CsvParser();
        $lines =
            ['Dies ist die völlig nutzlose Kopfzeile,tut, als wären es drei Felder',
            'Und eine Zeile, mit zwei Feldern',
            'Und noch eine Zeile, mit zwei, nein drei Feldern'];
        $this->expectException(CsvParseException::class);
        $parser->parseTables($lines, self::EXPECTED_FIELDS, ['DummyTable']);
    }

    public function testMissingFieldsAreIgnored() {
        $parser = new CsvParser();
        $lines =
            ['Feld1,Feld2,Feld3',
            'Wert1.1, Wert1.2'];
        $data = $parser->parseTables($lines, self::EXPECTED_FIELDS, ['DummyTable']);
        $this->assertEquals( count($data), 1, 'Expected parsing into one table');
        $this->assertEquals( count($data[0]), 1, 'Expected parsing into one row');
        $this->assertEquals( count($data[0][0]), 3, 'Expected first row to have three fields');
        $this->assertEquals( $data[0][0]['feld2'], 'Wert1.2');
    }

    public function testQuotesAreIgnored() {
        $parser = new CsvParser();
        $lines =
            ['Feld1,Feld2,Feld3',
            'Wert1.1, Wert1.2, "Wert1.3"'];
        $data = $parser->parseTables($lines, self::EXPECTED_FIELDS, ['DummyTable']);
        $this->assertEquals( count($data), 1, 'Expected parsing into one table');
        $this->assertEquals( count($data[0]), 1, 'Expected parsing into one row');
        $this->assertEquals( $data[0][0], ['feld1' => 'Wert1.1', 'feld2' => 'Wert1.2', 'feld3' => 'Wert1.3']);
    }

    public function testArrayColumn() {
        $parser = new CsvParser();
        $lines =
            ['Feld1,Feld2,Feld3[]',
            'Wert1.1, Wert1.2, Wert1.3',
            'Wert2.1, Wert2.2,',
            'Wert3.1, Wert3.2, Wert3.3 1:Wert3.3 2:Wert3.3 3',
            ];
        $data = $parser->parseTables($lines, [['feld1', 'feld2', 'feld3[]']], ['DummyTable']);
        $this->assertEquals( count($data), 1, 'Expected parsing into one table');
        $this->assertEquals( count($data[0]), 3, 'Expected parsing into three rows');
        $this->assertEquals( $data[0][0], ['feld1' => 'Wert1.1', 'feld2' => 'Wert1.2', 'feld3' => ['Wert1.3']]);
        $this->assertEquals( $data[0][1], ['feld1' => 'Wert2.1', 'feld2' => 'Wert2.2', 'feld3' => [ 0 => '']]);
        $this->assertEquals( $data[0][2], ['feld1' => 'Wert3.1', 'feld2' => 'Wert3.2', 'feld3' => ['Wert3.3 1', 'Wert3.3 2', 'Wert3.3 3']]);
    }    

    public function testCustomSeparator() {
        $parser = new CsvParser(';');
        $lines =
            ['Feld1; Feld2; Feld3',
            'Wert1.1; Wert1.2; Wert1.3'];
        $data = $parser->parseTables($lines, self::EXPECTED_FIELDS, ['DummyTable']);
        $this->assertEquals( $data[0][0]['feld1'], 'Wert1.1');
        $this->assertEquals( $data[0][0]['feld2'], 'Wert1.2');
        $this->assertEquals( $data[0][0]['feld3'], 'Wert1.3');
    }

    public function testTwoTables() {
        $parser = new CsvParser();
        $lines =
            ['Feld1,Feld2,Feld3',
            'T1-Wert1.1, T1-Wert1.2, T1-Wert1.3',
            'T1-Wert2.1, T1-Wert2.2, T1-Wert2.3',
            '',
            'T2Feld1, T2Feld2, T2Feld3, T2Feld4',
            'T2-Wert1.1, T2-Wert1.2, T2-Wert1.3, T2-Wert1.4',
            'T2-Wert2.1, T2-Wert2.2, T2-Wert2.3,'
            ];
        $data = $parser->parseTables($lines, [['feld1','feld2','feld3'],['t2feld1','t2feld2','t2feld3','t2feld4']], ['DummyTable1','DummyTable2']);
        $this->assertEquals( count($data), 2);
        $this->assertEquals( $data[0][0], ['feld1' => 'T1-Wert1.1','feld2' => 'T1-Wert1.2', 'feld3' => 'T1-Wert1.3']);
        $this->assertEquals( $data[0][1], ['feld1' => 'T1-Wert2.1','feld2' => 'T1-Wert2.2', 'feld3' => 'T1-Wert2.3']);
        $this->assertEquals( count($data[0]), 2);
        $this->assertEquals( $data[1][0], ['t2feld1' => 'T2-Wert1.1','t2feld2' => 'T2-Wert1.2', 't2feld3' => 'T2-Wert1.3', 't2feld4' => 'T2-Wert1.4']);
        $this->assertEquals( $data[1][1], ['t2feld1' => 'T2-Wert2.1','t2feld2' => 'T2-Wert2.2', 't2feld3' => 'T2-Wert2.3', 't2feld4' => '']);
        $this->assertEquals( count($data[1]), 2);
    }     
}