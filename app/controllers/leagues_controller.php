<?php

class LeagueController extends BaseController {

    public static function leagues($id) {
        $leagues = League::find($id);
        $drvs = array();
        foreach ($leagues as $league) {
            $drvs[$league->id] = count(Driver::findAll($league->id));
        }
        View::make('league/league_list.html', array('leagues' => $leagues, 'drvs' => $drvs));
    }

    public static function newLeague() {
        View::make('league/league_edit.html');
    }

    public static function create() {
        $user_logged_in = self::get_user_logged_in();
        $params = $_POST;

        $league = new League(array(
            'name' => $params['name'],
            'game' => $params['game'],
            'platform' => $params['platform'],
            'user_id' => $user_logged_in->id
        ));

        $league->save();

        Redirect::to('/league/' . $league->id);
    }

    public static function addDriver($id) {

        $params = $_POST;

        $driver = new Driver(array(
            'league_id' => $id,
            'name' => $params['name'],
            'car' => $params['car']
        ));

        $driver->save();

        $races = Race::findAll($id);
        foreach ($races as $race) {
            Rslt::checkResults($race->id, $id);
        }


        Redirect::to('/league/' . $id);
    }

    public static function deleteDriver() {
        $params = $_POST;

        $driver = new Driver(array('id' => $params['id']));
        $driver->destroy();

        $races = Race::findAll($params['league_id']);
        foreach ($races as $race) {
            Rslt::checkResults($race->id, $params['league_id']);
        }


        Redirect::to('/league/' . $params['league_id']);
    }

    public static function show($id) {
        $league = League::findOne($id);
        $drivers = Driver::findAll($id);
        $drvs = Driver::findAllArray($id);
        $races = Race::findAll($id);
//        $results = Rslt::findAll($id);
        $results = Rslt::getResults($id);
        $admin = User::find($league->id)->name;
        View::make('league/league_show.html', array('admin' => $admin,'drvs' => $drvs, 'league' => $league, 'drivers' => $drivers, 'races' => $races, 'results' => $results));
    }

    public static function showEdit($id) {
        $league = League::findOne($id);
        View::make('league/edit/league_details_edit.html', array('league' => $league));
    }

    public static function saveRules($id) {
        $params = $_POST;

        $attributes = array(
            'id' => $params['id'],
            'rules' => $params['rules']
        );

        $league = new League($attributes);
        $league->updateRules();


        Redirect::to('/league/' . $id . '/edit');
    }

}
