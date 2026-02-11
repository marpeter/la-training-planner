<?php

use PHPUnit\Framework\TestCase;
use \TnFAT\Planner\Discipline\CsvLoader;

class DisciplineCsvLoaderTest extends TestCase {

    public function testLoadingWithWrongHeaderFails() {
        $loader = new CsvLoader();
        $content =
            ['Dies ist die völlig nutzlose Kopfzeile,tut, als wären es drei Felder',
            'Und eine Zeile, mit zwei Feldern',
            'Und noch eine Zeile, mit zwei, nein drei Feldern'];
        $messages = [];
        $this->assertEquals($loader->load($content, $messages), 0);
        $this->assertNotEmpty($messages);

        $content =
            ["';SELECT * FROM users;,Name,Image",
            '1,Eine Disziplin,EinBild.png',
            '2,Zweite Disziplib,NochEinBild.png'];
        $messages = [];
        $this->assertEquals($loader->load($content, $messages), 0);
        $this->assertNotEmpty($messages);
    }
}