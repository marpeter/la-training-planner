<?php
namespace TnFAT\Planner\Exercise;

class ToJsonFormatter extends \TnFAT\Planner\AbstractEntityToJsonFormatter {

    public function __construct() {
        $this->reader = new DatabaseTable();
    }

    public function format(): string {
        foreach ($this->data[DatabaseTable::HEADER_TABLE] as &$exercise) {
            $exercise['warmup'] = (bool)$exercise['warmup'];
            $exercise['runabc'] = (bool)$exercise['runabc'];
            $exercise['mainex'] = (bool)$exercise['mainex'];
            $exercise['ending'] = (bool)$exercise['ending'];
            $exercise['durationmin'] = (int)$exercise['durationmin'];
            $exercise['durationmax'] = (int)$exercise['durationmax'];
            $exercise['details'] = explode(":",$exercise['details']);
            $exercise['disciplines'] = [];
            foreach ($this->data[DatabaseTable::LINK_DISCIPLINES_TABLE] as $exerciseDiscipline) {
               if ($exercise['id'] == $exerciseDiscipline['exercise_id']) {
                   $exercise['disciplines'][] = $exerciseDiscipline['discipline_id'];
               }
            }
        }        
        return json_encode($this->data[DatabaseTable::HEADER_TABLE]);
    }
}
