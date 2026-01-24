<?php
namespace TnFAT\Planner\Exercise;

class ToCsvFormatter extends \TnFAT\Planner\AbstractEntityToCsvFormatter {

    protected $fileName = 'Exercises.csv';
   
    public function __construct() {
        $this->reader = new DatabaseTable();
    }
    
    public function format(): string {
        // Remove the "Auslaufen" exercise from the CSV to prevent it from being modified by mistake
        $this->data[DatabaseTable::HEADER_TABLE] =
            array_filter($this->data[DatabaseTable::HEADER_TABLE], function($exercise) {
            return $exercise['name'] !== 'Auslaufen';
        });
        foreach($this->data[DatabaseTable::HEADER_TABLE] as &$exercise) {
            $exercise['warmup'] = $exercise['warmup']==1 ? "true" : "false";
            $exercise['runabc'] = $exercise['runabc']==1 ? "true" : "false";
            $exercise['mainex'] = $exercise['mainex']==1 ? "true" : "false";
            $exercise['ending'] = $exercise['ending']==1 ? "true" : "false";
            $exercise['durationmin'] = (int)$exercise['durationmin'];
            $exercise['durationmax'] = (int)$exercise['durationmax'];
            $exercise['Disciplines[]'] = array();
            foreach($this->data[DatabaseTable::LINK_DISCIPLINES_TABLE] as $exerciseDiscipline) {
                if ($exercise['id'] == $exerciseDiscipline['exercise_id']) {
                    $exercise['Disciplines[]'][] = $exerciseDiscipline['discipline_id'];
                }
            }
            $exercise['Disciplines[]'] = implode(":", $exercise['Disciplines[]']);
            unset($exercise['created_at']);         
        }
        $csv_header = array_keys($this->data[DatabaseTable::HEADER_TABLE][0]);
        $details_index = array_search("details", $csv_header);
        $csv_header[$details_index] = "Details[]";
        $contents = implode(";", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data[DatabaseTable::HEADER_TABLE],';');
        return $contents;
    }
}