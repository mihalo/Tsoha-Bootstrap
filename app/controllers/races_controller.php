<?php

class RaceController extends BaseController {

    public static function addRace($id) {
        $params = $_POST;
        $race = new Race(array(
            'league_id' => $params['league_id'],
            'track' => $params['track'],
            'raceday' => $params['raceday'],
            'laps' => $params['laps'],
            'result_id' => $params['result_id']
        ));

        $v = new Valitron\Validator($_POST);
        $v->rule('required', 'track');
        $v->rule('lengthMax', 'track', 50);
        $v->rule('required', 'raceday');
        $v->rule('dateFormat', 'raceday', 'd/m/Y');
        $v->rule('required', 'laps');
        $v->rule('numeric', 'laps');

        if ($v->validate()) {
            $race->save();
            Rslt::checkResults($race->id, $id);
            Redirect::to('/league/' . $id . '/edit');
        } else {
            $league = League::findOne($id);
            $drivers = Driver::findAll($id);
            $drvs = Driver::findAllArray($id);
            $races = Race::findAll($id);
            $results = Rslt::getResults($id);
            $admin = User::find($league->user_id)->name;
            View::make('league/edit/league_show.html', array('raceErrors' => $v->errors(), 'race' => $race, 'admin' => $admin, 'drvs' => $drvs, 'league' => $league, 'drivers' => $drivers, 'races' => $races, 'results' => $results));
        }
    }

    public static function deleteRace($id) {
        $params = $_POST;


        $race = new Race(array('id' => $params['id']));

        $race->destroy();


        $results = Rslt::findAll($params['id']);

        foreach ($results as $result) {
            $result->destroy();
        }

        Redirect::to('/league/' . $id . '/edit');
    }

}
