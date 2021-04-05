<?php

class Entity {

    private $db_driver;
    private $db_host;
    private $db_port;
    private $db_user;
    private $db_password;
    private $db_name;
    protected $entity;

    public function __construct($db_driver = "", $db_host = "", $db_port = "", $db_user = "", $db_password="", $db_name = "") {

        $this->db_driver = !empty($db_driver) ? $db_driver : getenv("DB_DRIVER");
        $this->db_host = !empty($db_host) ? $db_host : getenv("DB_HOST");
        $this->db_port = !empty($db_port) ? $db_port : getenv("DB_PORT");
        $this->db_user = !empty($db_user) ? $db_user : getenv("DB_USER");
        $this->db_password = !empty($db_password) ? $db_password : getenv("DB_PASSWORD");
        $this->db_name = !empty($db_name) ? $db_name : getenv("DB_NAME");
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

    public function selectAll(string $_name = "") {
        if($_name == "") {
            $_name = get_class($this);
        }
        return $this->entity->query("SELECT * FROM $_name")
        ->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function count(string $_name = "") {
        if($_name == "") {
            $_name = get_class($this);
        }
        return $this->entity->query("SELECT COUNT(*) FROM $_name")
        ->fetchColumn();
    }
}