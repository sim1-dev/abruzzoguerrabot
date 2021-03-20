<?php

class Entity {

    private $db_driver;
    private $db_host;
    private $db_port;
    private $db_username;
    private $db_password;
    private $db_name;
    protected $entity;

    public function __construct($_driver, $_host, $_port, $_user, $_password, $_name) {

        $this->db_driver = $_driver;
        $this->db_host = $_host;
        $this->db_port = $_port;
        $this->db_user = $_user;
        $this->db_password = $_password;
        $this->db_name = $_name;
        $db_charset = 'UTF8';

        $dsn = "$this->db_driver:host=$this->db_host;dbname=$this->db_name;port=$this->db_port";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
    
        try {
            $this->entity = new PDO($dsn, $this->db_user, $this->db_password, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }

    }

    public function getSettings() {

        return $this->entity->query("SELECT * FROM settings")
        ->execute()
        ->fetchAll();

    }

    public function getActiveSetting() {

        return $this->entity->query("SELECT 1 FROM settings WHERE active=1")
        ->fetch();

    }

    public function updateSettingField($_id, $_field, $_value) {

        return $this->entity->prepare("UPDATE settings SET :field = :value WHERE id = :id")
        ->bindParam(':field', $_field)
        ->bindParam(':value', $_value)
        ->bindParam(':id', $_id)
        ->execute();

    }

    public function updateSettingRunning($_id, $_value) {
        return $this->entity->prepare("UPDATE settings SET app_running = ? WHERE id = ?")
        ->bindParam(1, $_value)
        ->bindParam(2, $_id)
        ->execute();

    }

}