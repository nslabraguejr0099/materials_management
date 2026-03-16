<?php
require_once(__DIR__.'\database\database.php');
require_once(__DIR__.'\database\tables.php');

use Database\Database;
use Database\Tables;

$db = new Database;
$db->db_create();

$tables = new Tables;
$tables->create_tables();
$tables->populate_tables();