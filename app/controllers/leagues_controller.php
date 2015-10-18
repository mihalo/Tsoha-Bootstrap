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

        $v = new Valitron\Validator($_POST);
        $v->rule('required', 'name');
        $v->rule('lengthMax', 'name', 50);
        $v->rule('required', 'game');
        $v->rule('lengthMax', 'game', 50);
        if ($v->validate()) {
            $league->save();
            Redirect::to('/league/' . $league->id);
        } else {
//            Kint::dump($v->errors());
            View::make('league/league_edit.html', array('errors' => $v->errors(), 'league' => $league));
        }
    }

    public static function addDriver($id) {
        $params = $_POST;
        $driver = new Driver(array(
            'league_id' => $id,
            'name' => $params['name'],
            'car' => $params['car']
        ));

        $v = new Valitron\Validator($_POST);
        $v->rule('required', 'name');
        $v->rule('required', 'car');
        $v->rule('lengthMax', 'name', 50);
        $v->rule('lengthMax', 'car', 50);

        if ($v->validate()) {
            $driver->save();
            $races = Race::findAll($id);
            foreach ($races as $race) {
                Rslt::checkResults($race->id, $id);
            }
            Redirect::to('/league/' . $id . '/edit');
        } else {
            $league = League::findOne($id);
            $drivers = Driver::findAll($id);
            $drvs = Driver::findAllArray($id);
            $races = Race::findAll($id);
            $results = Rslt::getResults($id);
            $admin = User::find($league->user_id)->name;
            View::make('league/edit/league_show.html', array('errors' => $v->errors(), 'driver' => $driver, 'admin' => $admin, 'drvs' => $drvs, 'league' => $league, 'drivers' => $drivers, 'races' => $races, 'results' => $results));
        }
    }

    public static function destroy($id) {
        $user_logged_in = self::get_user_logged_in();
        $league = new League(array('id' => $id));
        $league->destroy();

        Redirect::to('/' . $user_logged_in->id . '/leagues');
    }

    public static function deleteDriver() {
        $params = $_POST;

        $driver = new Driver(array('id' => $params['id']));
        $driver->destroy();

        $races = Race::findAll($params['league_id']);
        foreach ($races as $race) {
            Rslt::checkResults($race->id, $params['league_id']);
        }


        Redirect::to('/league/' . $params['league_id'] . '/edit');
    }

    public static function show($id) {
        $league = League::findOne($id);
        $drivers = Driver::findAll($id);
        $drvs = Driver::findAllArray($id);
        $races = Race::findAll($id);
//        $results = Rslt::findAll($id);
        $results = Rslt::getResults($id);
        $admin = User::find($league->user_id)->name;
        View::make('league/league_show.html', array('admin' => $admin, 'drvs' => $drvs, 'league' => $league, 'drivers' => $drivers, 'races' => $races, 'results' => $results));
    }

    public static function showEdit($id) {
//        $league = League::findOne($id);
//        View::make('league/edit/league_details_edit.html', array('league' => $league));
        $league = League::findOne($id);
        $user_logged_in = self::get_user_logged_in();

        if ($user_logged_in === null) {
            Redirect::to('/login');
        }
        if ($user_logged_in->id !== $league->user_id) {
            Redirect::to('/login');
        }

        $drivers = Driver::findAll($id);
        $drvs = Driver::findAllArray($id);
        $races = Race::findAll($id);
//        $results = Rslt::findAll($id);
        $results = Rslt::getResults($id);
        $admin = User::find($league->user_id)->name;
        View::make('league/edit/league_show.html', array('admin' => $admin, 'drvs' => $drvs, 'league' => $league, 'drivers' => $drivers, 'races' => $races, 'results' => $results));
    }

    public static function editRules($id) {
        $league = League::findOne($id);
        $user_logged_in = self::get_user_logged_in();
        if ($user_logged_in === null) {
            Redirect::to('/login');
        }
        if ($user_logged_in->id !== $league->user_id) {
            Redirect::to('/login');
        }

        View::make('league/edit/league_rules_edit.html', array('league' => $league));
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

    public static function editInfo($id) {
        $league = League::findOne($id);
        $user_logged_in = self::get_user_logged_in();
        if ($user_logged_in === null) {
            Redirect::to('/login');
        }
        if ($user_logged_in->id !== $league->user_id) {
            Redirect::to('/login');
        }
        View::make('league/edit/league_info_edit.html', array('league' => $league));
    }

    public static function saveInfo($id) {
        $params = $_POST;

        $attributes = array(
            'id' => $params['id'],
            'info' => $params['info']
        );

        $league = new League($attributes);
        $league->updateInfo();


        Redirect::to('/league/' . $id . '/edit');
    }

}
