<?php

class RaceController extends BaseController {

    public static function addRace($id) {

        $params = $_POST;

        $race = new Race(array(
            'league_id' => $params['league_id'],
            'track' => $params['track'],
            'raceday' => $params['raceday'],
            'laps' => $params['laps'],
            'winner' => 'N/A',
            'result_id' => $params['result_id']
        ));

        $race->save();

        Rslt::checkResults($race->id, $id);

        Redirect::to('/league/' . $id);
    }

    public static function deleteRace($id) {
        $params = $_POST;


        $race = new Race(array('id' => $params['id']));

        $race->destroy();


        $results = Rslt::findAll($params['id']);

        foreach ($results as $result) {
            $result->destroy();
        }

        Redirect::to('/league/' . $id);
    }

}
