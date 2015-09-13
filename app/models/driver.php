<?php

class Driver extends BaseModel {

    public $id, $league_id, $name, $car;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public static function findAll($league_id) {
        $query = DB::connection()->prepare('SELECT * FROM Driver WHERE league_id = :league_id');
        $query->execute(array('league_id' => $league_id));

        $rows = $query->fetchAll();
        $drivers = array();

        foreach ($rows as $row) {
            $drivers[] = new Driver(array(
                'id' => $row['id'],
                'league_id' => $row['league_id'],
                'name' => $row['name'],
                'car' => $row['car']
            ));
        }

        return $drivers;
    }

    public static function findAllArray($league_id) {
        $query = DB::connection()->prepare('SELECT * FROM Driver WHERE league_id = :league_id');
        $query->execute(array('league_id' => $league_id));

        $rows = $query->fetchAll();
        $drivers = array();

        foreach ($rows as $row) {
            $drivers[$row['id']] = $row['name'];
        }

        return $drivers;
    }

    public static function findName($id) {
        $query = DB::connection()->prepare('SELECT * FROM Driver WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $driver = new Driver(array(
                'id' => $row['id'],
                'league_id' => $row['league_id'],
                'name' => $row['name'],
                'car' => $row['car']
            ));

            return $driver->name;
        }

        return null;
    }

    public static function find($id) {
        $query = DB::connection()->prepare('SELECT * FROM Driver WHERE id = :id LIMIT 1');
        $query->execute(array('id' => $id));
        $row = $query->fetch();

        if ($row) {
            $driver = new Driver(array(
                'id' => $row['id'],
                'league_id' => $row['league_id'],
                'name' => $row['name'],
                'car' => $row['car']
            ));

            return $driver;
        }

        return null;
    }

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Driver (league_id, name, car) VALUES (:league_id, :name, :car) RETURNING id');

        $query->execute(array('league_id' => $this->league_id, 'name' => $this->name, 'car' => $this->car));

        $row = $query->fetch();

        $this->id = $row['id'];
    }

    public function destroy() {
        $query = DB::connection()->prepare('DELETE FROM Driver WHERE id = :id');
        $query->execute(array('id' => $this->id));
    }

}
