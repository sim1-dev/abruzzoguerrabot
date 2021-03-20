<?php

require_once("db.php");

class Settings extends Entity {

    public function getSettings() {

        return $this->entity->query("SELECT * FROM settings")
        ->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function getActiveSetting() {

        return $this->entity->query("SELECT * FROM settings WHERE active = 1 LIMIT 1")
        ->fetch();

    }

    public function updateSettingRunning($_id, $_value) {
        $result = $this->entity->prepare("UPDATE settings SET app_running = ? WHERE id = ?");
        $result->bindParam(1, $_value);
        $result->bindParam(2, $_id);
        return $result->execute();

    }

}