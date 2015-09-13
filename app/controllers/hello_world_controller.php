<?php

class HelloWorldController extends BaseController {

    public static function index() {
        View::make('user/user_list.html');
    }

    public static function sandbox() {
       
    }

    public static function league_list($name) {
        View::make('league/league_list.html');
    }

    public static function league_show() {
        View::make('league/league_show.html');
    }

    public static function league_edit() {
        View::make('league/league_edit.html');
    }

}
