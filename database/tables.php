<?php
namespace Database;

require_once(__DIR__.'\database.php');

use Database\Database;
use PDO;

class Tables
{
    public $db_info;
    public $db_vars;
    public $db_conn;

    public function __construct ()
    {
        $this->db_info = new Database;
        $this->db_vars = $this->db_info->db_vars;
        $this->db_conn = $this->db_info->db_connect();
    }

    public function create_tables ()
    {
        $sql = '';

        //  drop tables
        try {
            $sql_drop_tbl = 'DROP TABLE IF EXISTS locations, materials, categories, materials_locations';
            $this->db_conn->exec($sql_drop_tbl);
        } catch(PDOException $e) {
            die($e->getMessage());
        }

        //  locations table
        $sql .= "
            CREATE TABLE locations (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(10) NOT NULL,
                name VARCHAR(100) NOT NULL,
                status BOOLEAN NOT NULL DEFAULT 0,
                date_log DATETIME NOT NULL,
                update_log DATETIME NULL,
                UNIQUE (code)
            );
        ";

        //  materials table
        $sql .= "
            CREATE TABLE materials (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(10) NOT NULL,
                name VARCHAR(100) NOT NULL,
                status BOOLEAN NOT NULL DEFAULT 0,
                category_code VARCHAR(10) NOT NULL,
                date_log DATETIME NOT NULL,
                update_log DATETIME NULL,
                UNIQUE (code)
            );
        ";

        // categories table
        $sql .= "
            CREATE TABLE categories (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(10) NOT NULL,
                name VARCHAR(100) NOT NULL,
                status BOOLEAN NOT NULL DEFAULT 0,
                date_log DATETIME NOT NULL,
                update_log DATETIME NULL,
                UNIQUE (code)
            );
        ";

        // materials_locations table
        $sql .= "
            CREATE TABLE materials_locations (
                id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                material_code VARCHAR(10) NOT NULL,
                location_code VARCHAR(10) NOT NULL,
                price INT NOT NULL DEFAULT 0,
                availability BOOLEAN NOT NULL DEFAULT 0,
                status BOOLEAN NOT NULL DEFAULT 0,
                date_log DATETIME NOT NULL,
                update_log DATETIME NULL,
                UNIQUE KEY materials_locations_unique_key (material_code, location_code)
            );
        ";

        try {
            $this->db_conn->exec($sql);
            echo 'Tables created successfully.'.PHP_EOL;
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    public function populate_tables ()
    {
        $sql = '';

        //  locations insert
        $sql .= "TRUNCATE locations;";
        $sql .= "INSERT INTO locations (id, code, name, status, date_log, update_log)
            VALUES(null, 'NORTH', 'North', 1, '2026-03-13', null);";
        $sql .= "INSERT INTO locations (id, code, name, status, date_log, update_log)
            VALUES(null, 'EAST', 'East', 1, '2026-03-13', null);";
        $sql .= "INSERT INTO locations (id, code, name, status, date_log, update_log)
            VALUES(null, 'SOUTH', 'South', 1, '2026-03-13', null);";
        $sql .= "INSERT INTO locations (id, code, name, status, date_log, update_log)
            VALUES(null, 'WEST', 'West', 1, '2026-03-13', null);";

        try {
            $this->db_conn->exec($sql);
            echo 'Tables populated successfully.'.PHP_EOL;
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }
}