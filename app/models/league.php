<?php

class League extends BaseModel {

    public $id, $user_id, $name, $game, $platform, $rules, $info;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM League');

        $query->execute();

        $rows = $query->fetchAll();
        $leagues = array();

        foreach ($rows as $row) {
            $leagues[] = new League(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'game' => $row['game'],
                'platform' => $row['platform'],
                'user_id' => $row['user_id']
            ));
        }

        return $leagues;
    }

    public static function find($user_id) {
        $query = DB::connection()->prepare('SELECT * FROM League WHERE user_id = :user_id');
        $query->execute(array('user_id' => $user_id));

        $rows = $query->fetchAll();
        $leagues = array();

        foreach ($rows as $row) {
            $leagues[] = new League(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'game' => $row['game'],
                'platform' => $row['platform'],
                'user_id' => $row['user_id']
            ));
        }

        return $leagues;
    }

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO League (name, game, platform, user_id) VALUES (:name, :game, :platform, :user_id) RETURNING id');

        $query->execute(array('name' => $this->name, 'game' => $this->game, 'platform' => $this->platform,
            'user_id' => $this->user_id));

        $row = $query->fetch();

        $this->id = $row['id'];
    }

    public static function findOne($id) {
        $query = DB::connection()->prepare('SELECT * FROM League WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $league = new League(array(
                'id' => $row['id'],
                'name' => $row['name'],
                'game' => $row['game'],
                'platform' => $row['platform'],
                'user_id' => $row['user_id'],
                'rules' => $row['rules'],
                'info' => $row['info']
            ));

            return $league;
        }

        return null;
    }

    public function updateRules() {
        $query = DB::connection()->prepare('UPDATE League SET rules = :rules WHERE id = :id;');
        $query->execute(array('id' => $this->id, 'rules' => $this->rules));
    }

    public function updateInfo() {
        $query = DB::connection()->prepare('UPDATE League SET info = :info WHERE id = :id;');
        $query->execute(array('id' => $this->id, 'info' => $this->info));
    }

}
