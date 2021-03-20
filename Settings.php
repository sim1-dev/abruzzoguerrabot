<?php

require_once("db.php");

class Settings extends Entity {

    public function getSettings() {

        return $this->entity->query("SELECT * FROM settings")
        ->execute()
        ->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getActiveSetting() {

        return $this->entity->query("SELECT 1 FROM settings WHERE active=1")
        ->fetch()[1];
       // ->fetchAll(\PDO::FETCH_ASSOC)[0];

    }

    public function updateSettingRunning($_id, $_value) {
        $result = $this->entity->prepare("UPDATE settings SET app_running=? WHERE id=?");
        $result->bindParam(1, $_value);
        $result->bindParam(2, $_id);
        return $result->execute();

    }

}