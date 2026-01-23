<?php
namespace TnFAT\Planner\Exercise;

class ToCsvReader extends \TnFAT\Planner\AbstractEntityToCsvReader {
    use DatabaseTable;

    protected $fileName = 'Exercises.csv';
    public function format(): string {
        // Remove the "Auslaufen" exercise from the CSV to prevent it from being modified by mistake
        $this->data[self::HEADER_TABLE] = array_filter($this->data[self::HEADER_TABLE], function($exercise) {
            return $exercise['name'] !== 'Auslaufen';
        });
        foreach($this->data[self::HEADER_TABLE] as &$exercise) {
            $exercise['warmup'] = $exercise['warmup']==1 ? "true" : "false";
            $exercise['runabc'] = $exercise['runabc']==1 ? "true" : "false";
            $exercise['mainex'] = $exercise['mainex']==1 ? "true" : "false";
            $exercise['ending'] = $exercise['ending']==1 ? "true" : "false";
            $exercise['durationmin'] = (int)$exercise['durationmin'];
            $exercise['durationmax'] = (int)$exercise['durationmax'];
            $exercise['Disciplines[]'] = array();
            foreach($this->data[self::LINK_DISCIPLINES_TABLE] as $exerciseDiscipline) {
                if ($exercise['id'] == $exerciseDiscipline['exercise_id']) {
                    $exercise['Disciplines[]'][] = $exerciseDiscipline['discipline_id'];
                }
            }
            $exercise['Disciplines[]'] = implode(":", $exercise['Disciplines[]']);
            unset($exercise['created_at']);         
        }
        $csv_header = array_keys($this->data[self::HEADER_TABLE][0]);
        $details_index = array_search("details", $csv_header);
        $csv_header[$details_index] = "Details[]";
        $contents = implode(";", $csv_header) . "\n";
        $contents .= $this->convertToCsv($this->data[self::HEADER_TABLE],';');
        return $contents;
    }
}