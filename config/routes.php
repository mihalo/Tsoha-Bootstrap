<?php

function check_logged_in() {
    BaseController::check_logged_in();
}

$routes->get('/', function() {
    UserController::index();
});

$routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
});

$routes->get('/:id/leagues', function($id) {
    LeagueController::leagues($id);
});

$routes->get('/newleague', 'check_logged_in', function() {
    LeagueController::newLeague();
});

$routes->post('/:id/deleteLeague', 'check_logged_in', function($id) {
    LeagueController::destroy($id);
});

$routes->post('/newleague', 'check_logged_in', function() {
    LeagueController::create();
});

$routes->post('/league/:id/addDriver', function($id) {
    LeagueController::addDriver($id);
});

$routes->post('/league/:id/deleteDriver', function() {
    LeagueController::deleteDriver();
});

$routes->post('/league/:id/addRace', function($id) {
    RaceController::addRace($id);
});

$routes->post('/league/:id/deleteRace', function($id) {
    RaceController::deleteRace($id);
});

$routes->get('/login', function() {
    UserController::login();
});

$routes->post('/login', function() {
    UserController::handle_login();
});

$routes->post('/logout', function() {
    UserController::logout();
});

$routes->get('/league/:id/edit', function($id) {
    LeagueController::showEdit($id);
});

$routes->get('/league/:id', function($id) {
    LeagueController::show($id);
});

$routes->get('/league/:league_id/editrules', function($league_id) {
    LeagueController::editRules($league_id);
});

$routes->post('/league/:league_id/saveRules', function($league_id) {
    LeagueController::saveRules($league_id);
});

$routes->get('/league/:league_id/editinfo', function($league_id) {
    LeagueController::editInfo($league_id);
});

$routes->post('/league/:league_id/saveinfo', function($league_id) {
    LeagueController::saveInfo($league_id);
});


$routes->post('/league/:league_id/race/:id/results', function($league_id, $id) {
    ResultController::updateResult($league_id, $id);
});

$routes->get('/league/:league_id/race/:id/results', function($league_id, $id) {
    ResultController::show($id, $league_id);
});

$routes->get('/league/:league_id/race/:id/results/edit', function($league_id, $id) {
    ResultController::showEdit($id, $league_id);
});
