<?php

require_once("db.php");

class Municipalities extends Entity {

    public function storeMunicipalities() {  //COMMENT AFTER USE
        $chieti = array("Altino", 
            "Archi", 
            "Ari", 
            "Arielli", 
            "Atessa", 
            "Bomba", 
            "Borrello", 
            "Bucchianico", 
            "Canosa Sannita", 
            "Carpineto Sinello", 
            "Carunchio", 
            "Casacanditella", 
            "Casalanguida", 
            "Casalbordino", 
            "Casalincontrada", 
            "Casoli", 
            "Castel Frentano", 
            "Castelguidone", 
            "Castiglione Messer Marino", 
            "Celenza sul Trigno", 
            "Chieti", 
            "Civitaluparella", 
            "Civitella Messer Raimondo", 
            "Colledimacine", 
            "Colledimezzo", 
            "Crecchio", 
            "Cupello", 
            "Dogliola", 
            "Fallo", 
            "Fara Filiorum Petri", 
            "Fara San Martino", 
            "Filetto", 
            "Fossacesia", 
            "Fraine", 
            "Francavilla al Mare", 
            "Fresagrandinaria", 
            "Frisa", 
            "Furci", 
            "Gamberale", 
            "Gessopalena", 
            "Gissi", 
            "Giuliano Teatino", 
            "Guardiagrele", 
            "Guilmi", 
            "Lama dei Peligni", 
            "Lanciano", 
            "Lentella", 
            "Lettopalena", 
            "Liscia", 
            "Miglianico", 
            "Montazzoli", 
            "Montebello sul Sangro", 
            "Monteferrante", 
            "Montelapiano", 
            "Montenerodomo", 
            "Monteodorisio", 
            "Mozzagrogna", 
            "Orsogna", 
            "Ortona", 
            "Paglieta", 
            "Palena", 
            "Palmoli", 
            "Palombaro", 
            "Pennadomo", 
            "Pennapiedimonte", 
            "Perano", 
            "Pietraferrazzana", 
            "Pizzoferrato", 
            "Poggiofiorito", 
            "Pollutri", 
            "Pretoro", 
            "Quadri", 
            "Rapino", 
            "Ripa Teatina", 
            "Rocca San Giovanni", 
            "Roccamontepiano", 
            "Roccascalegna", 
            "Roccaspinalveti", 
            "Roio del Sangro", 
            "Rosello", 
            "San Buono", 
            "San Giovanni Lipioni", 
            "San Giovanni Teatino", 
            "San Martino sulla Marrucina", 
            "San Salvo", 
            "San Vito Chietino", 
            "Santo Eusanio del Sangro", 
            "Santa Maria Imbaro", 
            "Scerni", 
            "Schiavi di Abruzzo", 
            "Taranta Peligna", 
            "Tollo", 
            "Torino di Sangro", 
            "Tornareccio", 
            "Torrebruna", 
            "Torrevecchia Teatina", 
            "Torricella Peligna", 
            "Treglio", 
            "Tufillo", 
            "Vacri", 
            "Vasto", 
            "Villa Santa Maria", 
            "Villalfonsina", 
            "Villamagna");
        foreach($chieti as $i=>$municipality) {
            $string.= "('".$municipality."')";
            if($i < sizeOf($chieti)-1) {
                $string.= ", ";
            }
        }
        $result = $this->entity->prepare("INSERT INTO municipalities (name) VALUES $string");
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