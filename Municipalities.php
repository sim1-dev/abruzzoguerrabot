<?php

require_once("db.php");

class Municipalities extends Entity {

    public function storeMunicipalities() {  //COMMENT AFTER USE
        $test = array("Arielli", "Pescara");
        foreach($test as $municipality) {
            $result = $this->entity->prepare("INSERT INTO municipalities (name) VALUES (?)");
            $result->bindParam(1, $municipality);
            return $result->execute();
        }

    }

    public function getMunicipalities() {

        return $this->entity->query("SELECT * FROM municipalities")
        ->execute()
        ->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getMunicipalityById($_id) {

        $result = $this->entity->prepare("SELECT * FROM municipalities WHERE id = ? LIMIT 1");
        $result->bindParam(1, $_id);
        return $result->execute()->fetch();

    }

    public function getRandomMunicipality() {

        $result = $this->entity->prepare("SELECT * FROM municipalities WHERE weight > 0 ORDER BY RAND() LIMIT 1");
        $result->bindParam(1, $_id);
        return $result->execute()->fetch();

    }

    public function resetGuerra() {
        $result = $this->entity->prepare("UPDATE municipalities SET weight = ? WHERE 1");
        $result->bindParam(1, 1);
        return $result->execute();
    }

    public function updateMunicipalityWeight($_id, $_value) {
        $result = $this->entity->prepare("UPDATE municipalities SET weight = ? WHERE id = ?");
        $result->bindParam(1, $_value);
        $result->bindParam(2, $_id);
        return $result->execute();
    }

}