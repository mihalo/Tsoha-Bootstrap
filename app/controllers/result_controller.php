<?php

class ResultController extends BaseController {

    public static function addResult($league_id, $race_id) {

        $params = $_POST;

        $result = new Rslt(array(
            'race_id' => $params['race_id'],
            'league_id' => $params['league_id'],
            'driver_id' => $params['driver_id'],
            'quali_time' => $params['quali_time'],
            'driver_name' => $params['driver_name']
        ));

        $result->save();

        Redirect::to('/league/' . $league_id . '/race/' . $race_id . '/results/edit');
    }

    public static function updateResult($league_id, $race_id) {
        $params = $_POST;

        $attributes = array(
            'id' => $params['id']
        );

        if (isset($params["quali_time"]) && !empty($params["quali_time"])) {
            $time = ResultController::timeToMs($params['quali_time']);
            $attributes['quali_time'] = $time;
            $attributes['quali_extra'] = $params['quali_extra'];
            $result = new Rslt($attributes);
            $result->updateQualificationTime();
            Redirect::to('/league/' . $league_id . '/race/' . $race_id . '/results/edit');
        }

        if (isset($params["race_time"]) && !empty($params["race_time"])) {
            $time = ResultController::timeToMs($params['race_time']);
            $attributes['race_time'] = $time;
            $attributes['race_extra'] = $params['race_extra'];
            $attributes['laps'] = $params['laps'];
            $result = new Rslt($attributes);
            $result->updateRaceTime();
            Redirect::to('/league/' . $league_id . '/race/' . $race_id . '/results/edit');
        }
    }

    public static function msToTime($ms) {
        $remaining = $ms;

        $hours = floor($ms / (1000 * 60 * 60));

        $remaining = $remaining - ($hours * 1000 * 60 * 60);

        $minutes = floor($remaining / (1000 * 60));

        $remaining = $remaining - ($minutes * 1000 * 60);

        $seconds = floor($remaining / 1000);

        $ms = $remaining - $seconds * 1000;

        return sprintf("%02d", $hours) . ":" . sprintf("%02d", $minutes) . ":" . sprintf("%02d", $seconds) . "." . sprintf("%03d", $ms);
    }

    public static function timeToMs($value) {
        sscanf($value, "%d:%d:%d.%d", $hours, $minutes, $seconds, $milliseconds);

        $hMs = $hours * 60 * 60 * 1000;
        $mMs = $minutes * 60 * 1000;
        $sMs = $seconds * 1000;

        $ms = $hMs + $mMs + $sMs + $milliseconds;

        return $ms;
    }

    public static function delete($id) {
        $rslt = new Rslt(array('id' => $id));
        $rslt->destroy();
    }

    public static function show($id, $league_id) {
        Rslt::checkResults($id, $league_id);
        $race = Race::findOne($id);

        $resultsQ = Rslt::findAllQualification($id);
        $resultsR = Rslt::findAllRace($id);

        $drivers = Driver::findAllArray($league_id);
        $league = League::findOne($league_id);

        View::make('league/league_result.html', array('league' => $league, 'race' => $race, 'resultsQ' => $resultsQ,
            'drivers' => $drivers, 'resultsR' => $resultsR));
    }

    public static function showEdit($id, $league_id) {
        Rslt::checkResults($id, $league_id);
        $race = Race::findOne($id);
        $resultsQ = Rslt::findAllQualification($id);
        $resultsR = Rslt::findAllRace($id);

        $drivers = Driver::findAllArray($league_id);
        
        $league = League::findOne($league_id);
        $user_logged_in = self::get_user_logged_in();
        if ($user_logged_in->id !== $league->user_id) {
            Redirect::to('/login');
        }

        View::make('league/edit/league_result.html', array('league' => $league, 'race' => $race, 'resultsQ' => $resultsQ,
            'drivers' => $drivers, 'resultsR' => $resultsR));
    }

}
