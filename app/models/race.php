<?php

class Race extends BaseModel {

    public $id, $league_id, $track, $raceday, $laps;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public function save() {

        $query = DB::connection()->prepare('INSERT INTO Race (league_id, track, raceday, laps) VALUES (:league_id, :track, :raceday, :laps) RETURNING id');

        $query->execute(array('league_id' => $this->league_id, 'track' => $this->track, 'raceday' => $this->raceday, 'laps' => $this->laps));

        $row = $query->fetch();

        $this->id = $row['id'];
    }

    public static function findOne($id) {
        $query = DB::connection()->prepare('SELECT * FROM Race WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $race = new Race(array(
                'id' => $row['id'],
                'track' => $row['track'],
                'laps' => $row['laps'],
                'league_id' => $row['league_id'],
                'raceday' => $row['raceday']
            ));

            return $race;
        }

        return null;
    }

    public static function findAll($league_id) {
        $query = DB::connection()->prepare('SELECT * FROM Race WHERE league_id = :league_id ORDER BY raceday');
        $query->execute(array('league_id' => $league_id));

        $rows = $query->fetchAll();
        $races = array();

        foreach ($rows as $row) {
            $races[] = new Race(array(
                'id' => $row['id'],
                'league_id' => $row['league_id'],
                'track' => $row['track'],
                'raceday' => $row['raceday'],
                'laps' => $row['laps']
            ));
        }

        return $races;
    }

    public function destroy() {
        $query = DB::connection()->prepare('DELETE FROM Race WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }

}
