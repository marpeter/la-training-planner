<?php
namespace TnFAT\Planner\Favorite;

class ToJsonReader extends \TnFAT\Planner\AbstractEntityReader {
    use DatabaseTable;

    protected function format(): string {
        foreach ($this->data[self::HEADER_TABLE] as &$favorite) {
            $favorite['disciplines'] = [];
            foreach ($this->data[self::LINK_DISCIPLINES_TABLE] as $favoriteDiscipline) {
                if ($favorite['id'] == $favoriteDiscipline['favorite_id']) {
                    $favorite['disciplines'][] = $favoriteDiscipline['discipline_id'];
                }
            }
        }
        return json_encode(
            ['headers' => $this->data[self::HEADER_TABLE],
             'exerciseMap' => $this->data[self::LINK_EXERCISES_TABLE]]
        );
    }
}
