<?php

class User extends BaseModel {

    public $id, $name, $password;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }
    
    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Users WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $user = new User(array(
                'id' => $row['id'],
                'name' => $row['name']
            ));

            return $user;
        }

        return null;
    }

    public static function all() {
        $query = DB::connection()->prepare('SELECT * FROM Users');

        $query->execute();

        $rows = $query->fetchAll();
        $users = array();

        foreach ($rows as $row) {
            $users[] = new User(array(
                'id' => $row['id'],
                'name' => $row['name'],
            ));
        }

        return $users;
    }

    public static function authenticate($name, $password) {
        $query = DB::connection()->prepare('SELECT * FROM Users WHERE name = :name AND password = :password LIMIT 1');
        $query->execute(array('name' => $name, 'password' => $password));
        $row = $query->fetch();
        if ($row) {

            if ($row) {
                $user = new User(array(
                    'id' => $row['id'],
                    'name' => $row['name']
                ));

                return $user;
            } else {
                return null;
            }
        }
    }

}
