<?php
namespace TnFAT\Planner\Favorite;

class ToJsonFormatter extends \TnFAT\Planner\AbstractEntityToJsonFormatter {

    public function __construct() {
        $this->reader = new DatabaseTable();
    }

    public function format(): string {
        foreach ($this->data[DatabaseTable::HEADER_TABLE] as &$favorite) {
            $favorite['disciplines'] = [];
            foreach ($this->data[DatabaseTable::LINK_DISCIPLINES_TABLE] as $favoriteDiscipline) {
                if ($favorite['id'] == $favoriteDiscipline['favorite_id']) {
                    $favorite['disciplines'][] = $favoriteDiscipline['discipline_id'];
                }
            }
        }
        return json_encode(
            ['headers' => $this->data[DatabaseTable::HEADER_TABLE],
             'exerciseMap' => $this->data[DatabaseTable::LINK_EXERCISES_TABLE]]
        );
    }
}
