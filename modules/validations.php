<?php

namespace Modules;

require_once(__DIR__.'\..\database\database.php');
require_once(__DIR__.'\helpers.php');

use PDO;
use Exception;
use Database\Database;
use Modules\Helpers;

class Validations
{
    public static function required ( $data, $desc )
    {
        if ( Helpers::checkIfEmpty($data) ) {
            return $desc.' is required.';
        }
        return true;
    }

    public function unique ( $table_col, $data, $desc, $where = null )
    {
        $table_col = explode('.', $table_col);
        $table = $table_col[0];
        $col = $table_col[1];
        $sql_where = '';
        $sql_prep = [$data];

        if ( Helpers::checkIfEmpty($table) ) {
            throw new Exception('Table is required.');
        }
        if ( Helpers::checkIfEmpty($col) ) {
            throw new Exception('Column is required.');
        }

        if ( !is_null($where) && is_array($where) ) {
            $str = 0;
            $arr = 0;
            foreach ( $where as $item ) {
                if ( is_array($item) )
                    $arr++;
                else
                    $str++;
            }

            if ( $str > 0 && $arr > 0 )
                throw new Exception('Invalid where parameter.');

            if ( $str > 0 ) {
                $sql_where .= $where[0].' '.$where[1].' ?';
                $sql_prep[] = $where[2];
            }
            if ( $arr > 0 ) {
                foreach ( $where as $item ) {
                    $sql_where .= $item[0].' '.$item[1].' ? AND ';
                    $sql_prep[] = $item[2];
                }
                $sql_where = substr($sql_where, 0, -4);
            }
        }

        try {
            $db = new Database;
            $db_conn = $db->db_connect();

            $sql = "SELECT COUNT(`".$col."`) FROM `".$table."` WHERE `".$col."` = ? ";
            if ( !empty($sql_where) )
                $sql .= 'AND '.$sql_where;

            $count = $db_conn->prepare($sql);
            $count->execute($sql_prep);
            $count = $count->fetchColumn();
            if ( $count >= 1 )
                return $desc.' already exists.';
        } catch(PDOException $e) {
            die($e);
        }

        return true;
    }

    public function exists ( $table_col, $data, $desc, $where = null )
    {
        $table_col = explode('.', $table_col);
        $table = $table_col[0];
        $col = $table_col[1];
        $sql_where = '';
        $sql_prep = [$data];

        if ( Helpers::checkIfEmpty($table) ) {
            throw new Exception('Table is required.');
        }
        if ( Helpers::checkIfEmpty($col) ) {
            throw new Exception('Column is required.');
        }

        if ( !is_null($where) && is_array($where) ) {
            $str = 0;
            $arr = 0;
            foreach ( $where as $item ) {
                if ( is_array($item) )
                    $arr++;
                else
                    $str++;
            }

            if ( $str > 0 && $arr > 0 )
                throw new Exception('Invalid where parameter.');

            if ( $str > 0 ) {
                $sql_where .= $where[0].' '.$where[1].' ?';
                $sql_prep[] = $where[2];
            }
            if ( $arr > 0 ) {
                foreach ( $where as $item ) {
                    $sql_where .= $item[0].' '.$item[1].' ? AND ';
                    $sql_prep[] = $item[2];
                }
                $sql_where = substr($sql_where, 0, -4);
            }
        }

        try {
            $db = new Database;
            $db_conn = $db->db_connect();

            $sql = "SELECT COUNT(`".$col."`) FROM `".$table."` WHERE `".$col."` = ?";
            if ( !empty($sql_where) )
                $sql .= 'AND '.$sql_where;

            $count = $db_conn->prepare($sql);
            $count->execute($sql_prep);
            $count = $count->fetchColumn();
            if ( $count == 0 )
                return $desc.' does not exists.';
        } catch(PDOException $e) {
            die($e);
        }

        return true;
    }
}