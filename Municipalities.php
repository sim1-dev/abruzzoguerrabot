<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require_once("db.php");

class Municipalities extends Entity {

    public function storeMunicipalities() {  //COMMENT AFTER USE
        $reader = IOFactory::load("abr_geo.xlsx");
        $data = $reader->getActiveSheet()->toArray(null, true, true, true);
        $string = "";
        foreach($data as $i=>$municipality) {
            $string.= "('".pg_escape_string($municipality["A"])."', '".pg_escape_string($municipality["C"])."', '".pg_escape_string($municipality["B"])."', '".$municipality["D"]."', '".pg_escape_string($municipality["A"])."')";
            if($i < sizeOf($data)) {
                $string.= ", ";
            }
        }
        $result = $this->entity->prepare("INSERT INTO muntest (name, lat, long, provincia, alias) VALUES $string");
        return $result->execute();

    }

    public function getDeadMunicipalities() { //test

        return $this->entity->query("SELECT * FROM municipalities WHERE weight = 0")
        ->fetchAll(\PDO::FETCH_ASSOC);

    }


    public function getMunicipalities() {

        return $this->entity->query("SELECT * FROM municipalities")
        ->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getAliveMunicipalities() {

        return $this->entity->query("SELECT * FROM municipalities WHERE weight > 0")
        ->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getMunicipalityById($_id) {

        $result = $this->entity->prepare("SELECT * FROM municipalities WHERE id = ? LIMIT 1");
        $result->bindParam(1, $_id);
        return $result->execute()->fetch();

    }

    public function getKillsByName($_name) {

        $result = $this->entity->prepare("SELECT kills FROM municipalities WHERE name = ? LIMIT 1");
        $result->bindParam(1, $_name);
        return $result->fetch();

    }

    public function getRandomMunicipality() {

        return $this->entity->query("SELECT * FROM municipalities WHERE weight > 0 ORDER BY random() LIMIT 1") //change to RAND() FOR MYSQL
        ->fetch();

    }

    public function getAnyRandomMunicipality() {

        return $this->entity->query("SELECT * FROM municipalities ORDER BY random() LIMIT 1") //change to RAND() FOR MYSQL
        ->fetch();

    }

    public function getKillsHighscore() {

        return $this->entity->query("SELECT * FROM municipalities ORDER BY kills DESC LIMIT 5")
        ->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getWeightsHighscore() {

        return $this->entity->query("SELECT * FROM municipalities ORDER BY weight DESC LIMIT 5")
        ->fetchAll(\PDO::FETCH_ASSOC);

    }


    public function resetGuerra() {
        $result = $this->entity->query("UPDATE municipalities SET weight = 1 WHERE true");
        $result->execute();
        $result = $this->entity->query("UPDATE municipalities SET kills = 0 WHERE true");
        return $result->execute();
    }

    public function updateMunicipalityWeight($_id, $_value) {
        $result = $this->entity->prepare("UPDATE municipalities SET weight = ? WHERE id = ?");
        $result->bindParam(1, $_value);
        $result->bindParam(2, $_id);
        return $result->execute();
    }

    public function addKill($_id) {
        $result = $this->entity->prepare("UPDATE municipalities SET kills = kills+1 WHERE id = ?");
        $result->bindParam(1, $_id);
        return $result->execute();
    }

}