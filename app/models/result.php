<?php

class Rslt extends BaseModel {

    public $id, $league_id, $race_id, $driver_id, $quali_time, $race_time, $quali_extra, $race_extra, $laps;

    public function save() {
        $query = DB::connection()->prepare(
                'INSERT INTO Rslt (league_id, race_id, driver_id, quali_time, race_time, quali_extra, race_extra, laps)'
                . ' VALUES (:league_id, :race_id, :driver_id, :quali_time, :race_time, :quali_extra, :race_extra, :laps) RETURNING id');
        $query->execute(array('league_id' => $this->league_id, 'race_id' => $this->race_id, 'driver_id' => $this->driver_id, 'quali_time' => $this->quali_time, 'race_time' => $this->race_time, 'quali_extra' => $this->quali_extra, 'race_extra' => $this->race_extra, 'laps' => $this->laps));

        $row = $query->fetch();

        $this->id = $row['id'];
    }

    public static function checkResults($race_id, $league_id) {
        $query = DB::connection()->prepare(
                'SELECT id, league_id FROM Driver WHERE league_id = :league_id AND id NOT IN (SELECT driver_id FROM Rslt WHERE race_id = :race_id);'
        );
        $query->execute(array('race_id' => $race_id, 'league_id' => $league_id));

        $query->execute();

        $rows = $query->fetchAll();

        foreach ($rows as $row) {
            $result = new Rslt(array(
                'league_id' => $row['league_id'],
                'race_id' => $race_id,
                'driver_id' => $row['id'],
                'quali_time' => 0,
                'race_time' => 0,
                'quali_extra' => "DNS",
                'race_extra' => 'DNS',
                'laps' => 0
            ));

            $result->save();
        }

        $q = DB::connection()->
                prepare('SELECT id,driver_id, race_id FROM Rslt WHERE driver_id NOT IN (SELECT id FROM Driver WHERE league_id = :league_id) AND race_id = :race_id;');
        $q->execute(array('race_id' => $race_id, 'league_id' => $league_id));

        $r = $q->fetchAll();

        foreach ($r as $row) {
            ResultController::delete($row['id']);
        }
    }

    public function destroy() {
        $query = DB::connection()->prepare('DELETE FROM Rslt WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }

    public function updateQualificationTime() {
        $query = DB::connection()->prepare('UPDATE Rslt SET quali_time = :quali_time, quali_extra = :quali_extra WHERE id = :id;');
        $query->execute(array('id' => $this->id, 'quali_time' => $this->quali_time, 'quali_extra' => $this->quali_extra));
    }

    public function updateRaceTime() {
        $query = DB::connection()->prepare('UPDATE Rslt SET race_time = :race_time, laps = :laps, race_extra = :race_extra WHERE id = :id;');
        $query->execute(array('id' => $this->id, 'race_time' => $this->race_time, 'laps' => $this->laps, 'race_extra' => $this->race_extra));
    }

    public static function findAllQualification($race_id) {
        $query = DB::connection()->prepare('SELECT * FROM Rslt WHERE race_id = :race_id ORDER BY quali_extra, quali_time');
        $query->execute(array('race_id' => $race_id));

        $rows = $query->fetchAll();
        $results = array();

        foreach ($rows as $row) {
            $quali_time = ResultController::msToTime($row['quali_time']);

            $results[] = new Rslt(array(
                'id' => $row['id'],
                'race_id' => $row['race_id'],
                'league_id' => $row['league_id'],
                'driver_id' => $row['driver_id'],
                'quali_time' => $quali_time,
                'race_time' => $row['race_time'],
                'laps' => $row['laps'],
                'quali_extra' => $row['quali_extra'],
                'race_extra' => $row['race_extra']
            ));
        }

        return $results;
    }

    public static function points($pos) {
        $points = array(
            1 => 25,
            2 => 18,
            3 => 15,
            4 => 12,
            5 => 10,
            6 => 9,
            7 => 8,
            8 => 7,
            9 => 6,
            10 => 5,
            11 => 4,
            12 => 3,
            13 => 2,
            14 => 1
        );
        if ($pos > 14) {
            return 0;
        } else {
            return $points[$pos];
        }
    }

    public static function timeToMs($value) {
        sscanf($value, "%d:%d:%d.%d", $hours, $minutes, $seconds, $milliseconds);

        $hMs = $hours * 60 * 60 * 1000;
        $mMs = $minutes * 60 * 1000;
        $sMs = $seconds * 1000;

        $ms = $hMs + $mMs + $sMs + $milliseconds;

        return $ms;
    }

    public static function getResults($league_id) {
        $races = Race::findAll($league_id);
        $results = array();

        foreach ($races as $race) {
            $rslts = Rslt::findAllPoints($race->id);
            $pos = 1;
            foreach ($rslts as $result) {
                if (!array_key_exists($result->driver_id, $results)) {
                    $results[$result->driver_id] = array();
                    $results[$result->driver_id]['total'] = 0;
                    $results[$result->driver_id]['driver_id'] = $result->driver_id;
                }
                if ($result->race_extra === '-' || Rslt::timeToMs($result->race_time) > 0) {
                    $results[$result->driver_id][$race->id] = Rslt::points($pos);
                    $results[$result->driver_id]['total'] = $results[$result->driver_id]['total'] + Rslt::points($pos);
                } else {
//                    if ($result->race_time > 0) {
//                        $results[$result->driver_id][$race->id] = Rslt::points($pos);
//                        $results[$result->driver_id]['total'] = $results[$result->driver_id]['total'] + Rslt::points($pos);
//                    } 
                    $results[$result->driver_id][$race->id] = $result->race_extra;
                }
                $pos++;
            }
            $pos = 0;
        }

        return Rslt::sortArray($results, 'total');
    }

    function sortArray($data, $field) {
        $field = (array) $field;
        uasort($data, function($b, $a) use($field) {
            $retval = 0;
            foreach ($field as $fieldname) {
                if ($retval == 0)
                    $retval = strnatcmp($a[$fieldname], $b[$fieldname]);
            }
            return $retval;
        });
        return $data;
    }

    public static function findAllPoints($race_id) {
        $query = DB::connection()->prepare('SELECT * FROM Rslt WHERE race_id  = :race_id ORDER BY laps DESC, race_time DESC;');
        $query->execute(array('race_id' => $race_id));

        $rows = $query->fetchAll();
        $results = array();

        foreach ($rows as $row) {
            $race_time = ResultController::msToTime($row['race_time']);

            $results[] = new Rslt(array(
                'id' => $row['id'],
                'race_id' => $row['race_id'],
                'league_id' => $row['league_id'],
                'driver_id' => $row['driver_id'],
                'quali_time' => $row['quali_time'],
                'race_time' => $race_time,
                'laps' => $row['laps'],
                'quali_extra' => $row['quali_extra'],
                'race_extra' => $row['race_extra']
            ));
        }

        return $results;
    }

    public static function findAllRace($race_id) {
        $query = DB::connection()->prepare('SELECT * FROM Rslt WHERE race_id  = :race_id ORDER BY laps DESC, race_time DESC;');
        $query->execute(array('race_id' => $race_id));

        $rows = $query->fetchAll();
        $results = array();

        foreach ($rows as $row) {
            $race_time = ResultController::msToTime($row['race_time']);

            $results[] = new Rslt(array(
                'id' => $row['id'],
                'race_id' => $row['race_id'],
                'league_id' => $row['league_id'],
                'driver_id' => $row['driver_id'],
                'quali_time' => $row['quali_time'],
                'race_time' => $race_time,
                'laps' => $row['laps'],
                'quali_extra' => $row['quali_extra'],
                'race_extra' => $row['race_extra']
            ));
        }

        return $results;
    }

    public static function findAll($race_id) {
        $query = DB::connection()->prepare('SELECT * FROM Rslt WHERE race_id = :race_id');
        $query->execute(array('race_id' => $race_id));

        $rows = $query->fetchAll();
        $results = array();

        foreach ($rows as $row) {
            $driver_name = Driver::findName($row['driver_id']);

            $results[] = new Rslt(array(
                'id' => $row['id'],
                'race_id' => $row['race_id'],
                'league_id' => $row['league_id'],
                'driver_id' => $row['driver_id'],
                'quali_time' => $row['quali_time'],
                'race_time' => $row['race_time'],
                'driver_name' => $driver_name,
                'laps' => $row['laps'],
                'quali_extra' => $row['quali_extra'],
                'race_extra' => $row['race_extra']
            ));
        }

        return $results;
    }

}
