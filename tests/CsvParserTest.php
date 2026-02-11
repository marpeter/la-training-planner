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
        // $this->expectException(CsvParseException::class);
        $data = $parser->parseTables($lines, self::EXPECTED_FIELDS, ['DummyTable']);
        $this->assertEquals( count($data), 1, 'Expected parsing into one table');
        $this->assertEquals( count($data[0]), 1, 'Expected parsing into one row');
        $this->assertEquals( count($data[0][0]), 3, 'Expected first row to have three fields');
        $this->assertEquals( $data[0][0]['feld2'], 'Wert1.2');
    }

    public function testLeadingTrailingBlanksAreIgnored() {
        $parser = new CsvParser();
        $lines =
            ['Feld1,Feld2,Feld3',
            'Wert1.1 , Wert1.2,  Wert1.3  '];
        // $this->expectException(CsvParseException::class);
        $data = $parser->parseTables($lines, self::EXPECTED_FIELDS, ['DummyTable']);
        $this->assertEquals( $data[0][0]['feld1'], 'Wert1.1');
        $this->assertEquals( $data[0][0]['feld2'], 'Wert1.2');
        $this->assertEquals( $data[0][0]['feld3'], 'Wert1.3');
    }  
}