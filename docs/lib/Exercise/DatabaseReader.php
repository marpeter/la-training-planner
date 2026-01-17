<?php
namespace TnFAT\Planner\Exercise;

class DatabaseReader extends \TnFAT\Planner\AbstractDatabaseReader {
    use DatabaseTable;

    protected function deserialize(): string {
        foreach ($this->data[self::HEADER_TABLE] as &$exercise) {
            $exercise['warmup'] = (bool)$exercise['warmup'];
            $exercise['runabc'] = (bool)$exercise['runabc'];
            $exercise['mainex'] = (bool)$exercise['mainex'];
            $exercise['ending'] = (bool)$exercise['ending'];
            $exercise['durationmin'] = (int)$exercise['durationmin'];
            $exercise['durationmax'] = (int)$exercise['durationmax'];
            $exercise['details'] = explode(":",$exercise['details']);
            $exercise['disciplines'] = [];
            foreach ($this->data[self::LINK_DISCIPLINES_TABLE] as $exerciseDiscipline) {
               if ($exercise['id'] == $exerciseDiscipline['exercise_id']) {
                   $exercise['disciplines'][] = $exerciseDiscipline['discipline_id'];
               }
            }
        }        
        return json_encode($this->data[self::HEADER_TABLE]);
    }
}
