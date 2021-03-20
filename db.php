<?php

class DB {

    protected $db_driver, $db_host, $db_username, $db_password, $db_name, $entity;

    public function __construct() {

        $db_driver = getenv("DB_DRIVER");
        $db_host = getenv("DB_HOST");
        $db_username = getenv("DB_USER");
        $db_password = getenv("DB_PASSWORD");
        $db_name = getenv("DB_NAME");
        $db_charset = 'UTF8';

        $dsn = "$db_driver:host=$db_host;dbname=$db_name;port=$db_port;charset=$db_charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
    
        try {
            $this->entity = new PDO($dsn, $db_user, $db_password, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }

    }

    protected function getSettings() {

        return $this->entity->query("SELECT * FROM settings")->fetchAll();

    }

    protected function getActiveSetting() {

        return $this->entity->query("SELECT 1 FROM settings WHERE active=1")->fetchAll();

    }

    protected function updateSettingField($_id, $_field, $_value) {

        return $this->entity->prepare("UPDATE settings SET $_field =:$_value WHERE id =:$_id")->execute();

    }

}