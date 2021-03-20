<?php

class DB {

    protected $db_driver, $db_host, $db_username, $db_password, $db_name, $entity;

    public function __construct() {

        $this->db_driver = getenv("DB_DRIVER");
        $this->db_host = getenv("DB_HOST");
        $this->db_username = getenv("DB_USER");
        $this->db_password = getenv("DB_PASSWORD");
        $this->db_name = getenv("DB_NAME");
        $this->db_charset = 'UTF8';

        $dsn = "$this->db_driver:host=$this->db_host;dbname=$this->db_name;port=$this->db_port;charset=$this->db_charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
    
        try {
            $this->entity = new PDO($dsn, $this->db_user, $this->db_password, $this->options);
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