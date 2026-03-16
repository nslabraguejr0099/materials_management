<?php

namespace Database;

use PDO;

class Database
{
    public $db_vars;
    public function __construct()
    {
        $this->db_vars = (object)[
            'servername' => 'localhost',
            'username'   => 'root',
            'password'   => '',
            'dbname'     => 'materials_management_db'
        ];
    }

    public function db_create ()
    {
        try {
            $db_vars = $this->db_vars;

            $db_conn = new PDO("mysql:host=$db_vars->servername;", $db_vars->username, $db_vars->password);
            $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "CREATE DATABASE IF NOT EXISTS ".$db_vars->dbname;
            $db_conn->exec($sql);

            echo 'Database created successfully.'.PHP_EOL;
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    public function db_connect ()
    {
        try {
            $db_vars = $this->db_vars;
            $db_conn = new PDO("mysql:host=$db_vars->servername;dbname=$db_vars->dbname", $db_vars->username, $db_vars->password);
            $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db_conn;
        } catch(PDOException $e) {
            die("Could not connect. " . $e->getMessage());
        }
    }
}