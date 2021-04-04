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
        $result = $this->entity->prepare("INSERT INTO muntest (name, lat, long, prov, owner) VALUES $string");
        return $result->execute();

    }

    public function getDeadMunicipalities() { //test

        return $this->entity->query("SELECT * FROM muntest WHERE alive = 0")
        ->fetchAll(\PDO::FETCH_ASSOC);

    }


    public function getMunicipalities() {

        return $this->entity->query("SELECT * FROM muntest")
        ->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getAliveMunicipalities() {

        return $this->entity->query("SELECT * FROM muntest WHERE alive > 0")
        ->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getMunicipalityById($_id) {

        $result = $this->entity->prepare("SELECT * FROM muntest WHERE id = ? LIMIT 1");
        $result->bindParam(1, $_id);
        return $result->execute()->fetch();

    }

    public function getMunicipalityByName($_name) {

        $result = $this->entity->prepare("SELECT * FROM muntest WHERE name = ? LIMIT 1");
        $result->bindParam(1, $_name);
        return $result->execute()->fetch();

    }

    public function getKillsByName($_name) {

        $result = $this->entity->prepare("SELECT kills FROM muntest WHERE name = ? LIMIT 1");
        $result->bindParam(1, $_name);
        return $result->fetch();

    }

    public function getWeightByName($_name) {

        $result = $this->entity->prepare("SELECT COUNT(*) FROM muntest WHERE owner = ?");
        $result->bindParam(1, $_name);
        return $result->fetch();

    }

    public function getRandomMunicipality() {

        return $this->entity->query("SELECT * FROM muntest WHERE alive > 0 ORDER BY random() LIMIT 1") //change to RAND() FOR MYSQL
        ->fetch();

    }

    public function getAnyRandomMunicipality() {

        return $this->entity->query("SELECT * FROM muntest ORDER BY random() LIMIT 1") //change to RAND() FOR MYSQL
        ->fetch();

    }

    public function getKillsHighscore() {

        return $this->entity->query("SELECT * FROM muntest ORDER BY kills DESC LIMIT 5")
        ->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getWeightsHighscore() {

        return $this->entity->query("SELECT owner, COUNT(*) as weight FROM muntest WHERE owner IS NOT NULL GROUP BY owner ORDER BY COUNT(*) DESC LIMIT 3")
        ->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function setOwner($_id, $_name) {
        $result = $this->entity->prepare("UPDATE muntest SET owner = ? WHERE id = ?");
        $result->bindParam(1, $_name);
        $result->bindParam(2, $_id);
        return $result->execute();
    }

    public function resetGuerra() {
        $result = $this->entity->query("UPDATE muntest SET alive = 1 WHERE true");
        $result->execute();
        $result = $this->entity->query("UPDATE muntest SET kills = 0 WHERE true");
        $result->execute();
        $result = $this->entity->query("UPDATE muntest SET owner = name WHERE true");
        return $result->execute();
    }

    public function updateMunicipalityWeight($_id, $_value) {
        $result = $this->entity->prepare("UPDATE muntest SET alive = ? WHERE id = ?");
        $result->bindParam(1, $_value);
        $result->bindParam(2, $_id);
        return $result->execute();
    }

    public function addKill($_id) {
        $result = $this->entity->prepare("UPDATE muntest SET kills = kills+1 WHERE id = ?");
        $result->bindParam(1, $_id);
        return $result->execute();
    }

    public function kill($_id) {
        $result = $this->entity->prepare("UPDATE muntest SET alive = 0 WHERE id = ?");
        $result->bindParam(1, $_id);
        return $result->execute();
    }


}