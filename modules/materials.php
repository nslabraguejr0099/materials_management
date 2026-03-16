<?php

namespace Modules;

require_once(__DIR__.'\..\database\database.php');
require_once(__DIR__.'\validations.php');

use PDO;
use Exceptions;
use Database\Database;
use Modules\Validations;

class MaterialsModule 
{
    public function index ( $data )
    {
        $data['filters'] = isset($data['filters']) && is_array($data['filters']) ? $data['filters'] : [];
        $search = $data['search'];
        $filters_search = $data['filters'];

        /** Query */
            $sql_categories = 'SELECT categories.name FROM categories WHERE categories.code = materials.code';
            $sql = 'SELECT *, ('.$sql_categories.') as categories_name FROM materials ';
            $sql_prep = [];
            $sql_where = '';

            //  search where clause
            $col_search = ['code', 'name', 'category_code'];
            if ( !is_null($search) && $search != '' ) {
                $sql_col_search = '';
                foreach ( $col_search as $item ) {
                    $sql_col_search .= '`'.$item.'` LIKE ? OR ';
                    $sql_prep[] = '%'.$search.'%';
                }
                $sql_col_search = substr($sql_col_search, 0, -3);

                $sql .= "WHERE (".$sql_col_search.") ";
            }

            //  where clause for filters
            foreach ( $filters_search as $k => $filter ) {
                if ( is_null($filter) || $filter == '' ) {
                    continue;
                }
                else {
                    $sql_where .= $k." = ? AND ";
                    $sql_prep[] = $filter;
                }
            }
            if ( !empty($sql_where) ) {
                $sql_where = substr($sql_where, 0, -4);

                if ( is_null($search) || $search == '' )
                    $sql_where ='WHERE '.$sql_where;

                $sql .= $sql_where;
            }

            //  query
            $db = new Database;
            $db_conn = $db->db_connect();
            $tbl_load = $db_conn->prepare($sql);
            if ( empty($sql_prep) ) {
                $tbl_load->execute();
            } else {
                $tbl_load->execute($sql_prep);
            }
            $tbl_load = $tbl_load->fetchAll(PDO::FETCH_ASSOC);
        /** */

        /** Filter */
            $filters = [];
            $filters_search['status'] = isset($filters_search['status']) && $filters_search['status'] != '' ? $filters_search['status'] : '';

            $filters['status'] = [
                'title' => 'Status',
                'data' => [
                    ['title' => 'All', 'value' => ''],
                    ['title' => 'Active', 'value' => '1'],
                    ['title' => 'Inactive', 'value' => '0']
                ],
                'value' => $filters_search['status'],
                'create_blank' => false
            ];
        /** */

        return [
            'data' => $tbl_load,
            'filters' => $filters
        ];
    }

    public function save ( $data, $_mode )
    {
        $data['inputs'] = isset($data['inputs']) && is_array($data['inputs']) ? $data['inputs'] : [];
        $inputs = $data['inputs'];
        $skip = false;

        $inputs['code'] = strtoupper($inputs['code']);

        $sql_cols = '';
        $sql_values = '';
        $sql_upd_val = '';
        foreach ( $inputs as $key => $item ) {
            $sql_cols .= '`'.$key.'`,';
            $sql_values .= ":".$key.",";
            $sql_upd_val .= '`'.$key.'` = :'.$key.',';
        }
        $sql_cols = substr($sql_cols, 0, -1);
        $sql_values = substr($sql_values, 0, -1);
        $sql_upd_val = substr($sql_upd_val, 0, -1);

        /** */
            $db = new Database;
            $db_conn = $db->db_connect();
            if ( $_mode == 'edit' ) {
                try {
                    $sql = 'SELECT '.$sql_cols.' FROM materials WHERE id = ?';
                    $record = $db_conn->prepare($sql);
                    $record->execute([$inputs['id']]);
                    $record = $record->fetch(PDO::FETCH_ASSOC);
                    if ( empty($record) ) {
                        return [
                            'status' => 'error',
                            'report' => 'Record does not exist.'
                        ];
                    }

                    $_cc = 0;
                    foreach ( $inputs as $key => $item ) {
                        if ( $record[$key] != $item ) $_cc++;
                    }

                    if ( $_cc == 0 ) {
                        return [
                            'status' => 'info',
                            'report' => 'There are no changes'
                        ];
                    }

                    if ( $inputs['code'] == $record['code'] ) $skip = true;
                }
                catch(PDOException $e) {
                    throw new Exception($e);
                }
            }

        /** */

        $report_msgs = [];
        /** Validations */
            $validations = new Validations;

            //  code validation
            if ( $skip == false ) {
                if ( ($result = $validations->required($inputs['code'], 'Code')) !== true ) {
                    $report_msgs['code'] = $result;
                } elseif ( strlen($inputs['code']) > 10 ) {
                    $report_msgs['code'] = 'Code must not be greater than 10';
                } elseif ( ($result = $validations->unique('materials.code', $inputs['code'], 'Code')) !== true ) {
                    $report_msgs['code'] = $result;
                }
            }

            //  name validation
            if ( ($result = $validations->required($inputs['name'], 'Name')) !== true ) {
                $report_msgs['name'] = $result;
            } elseif ( strlen($inputs['name']) > 100 ) {
                $report_msgs['name'] = 'Name must not be greater than 100';
            }

            //  category_code validation
            if ( ($result = $validations->required($inputs['category_code'], 'Category Code')) !== true ) {
                $report_msgs['category_code'] = $result;
            } elseif ( strlen($inputs['category_code']) > 10 ) {
                    $report_msgs['category_code'] = 'Category Code must not be greater than 10';
            } elseif ( ($result = $validations->exists('categories.code', $inputs['category_code'], 'Category Code', ['status', '=', true])) !== true ) {
                $report_msgs['category_code'] = $result;
            }

            //  status validation
            if ( ($result = $validations->required($inputs['status'], 'Status')) !== true ) {
                $report_msgs['status'] = $result;
            }

            if ( !empty($report_msgs) ) {
                return [
                    'status' => 'error',
                    'report' => $report_msgs
                ];
            }

            if ( $_mode == 'save' ) {
                $inputs['date_log'] = date('Y-m-d H:i:s');
            } else {
                $inputs['update_log'] = date('Y-m-d H:i:s');
            }

        /** */

        /** Save */
            $sql_cols = '';
            $sql_values = '';
            $sql_upd_val = '';
            foreach ( $inputs as $key => $item ) {
                $sql_cols .= '`'.$key.'`,';
                $sql_values .= ":".$key.",";
                $sql_upd_val .= '`'.$key.'` = :'.$key.',';
            }
            $sql_cols = substr($sql_cols, 0, -1);
            $sql_values = substr($sql_values, 0, -1);
            $sql_upd_val = substr($sql_upd_val, 0, -1);

            try {
                if ( $_mode == 'save' ) {
                    $sql = 'INSERT INTO materials ('.$sql_cols.') VALUES ('.$sql_values.')';
                    $save = $db_conn->prepare($sql)->execute($inputs);
                } else {
                    $sql = 'UPDATE materials SET '.$sql_upd_val.' WHERE id = :id';
                    $save = $db_conn->prepare($sql)->execute($inputs);
                }
            } catch(PDOException $e) {
                throw new Exception($e);
            }
        /** */
        
        return [
            'status' => 'success',
            'report' => 'Record(s) has been successfully saved.'
        ];
    }

    public function delete ( $data )
    {
        $data['inputs'] = isset($data['inputs']) && is_array($data['inputs']) ? $data['inputs'] : [];
        $inputs = $data['inputs'];
        $id = $inputs['id'];

        $db = new Database;
        $db_conn = $db->db_connect();
        try {
            $sql = 'SELECT COUNT(`id`) FROM `materials` WHERE `id` = ?';
            $search = $db_conn->prepare($sql);
            $search->execute([$id]);
            $search = $search->fetchColumn();

            if ( $search == 0 ) {
                return [
                    'status' => 'error',
                    'report' => 'Record is not found.'
                ];
            }

            $sql = 'DELETE FROM materials WHERE id = ?';
            $delete = $db_conn->prepare($sql)->execute([$id]);

            return [
                'status' => 'success',
                'report' => 'Record has been deleted.'
            ];
        }
        catch(PDOException $e) {
            throw new Exception($e);
        }
    }
}

header('Content-Type: application/json');

$request_method = $_SERVER['REQUEST_METHOD'];
$request_data = null;

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $request_data = $_POST;
} elseif ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
    $request_data = $_GET;
} elseif ( $_SERVER['REQUEST_METHOD'] == 'PUT' || $_SERVER['REQUEST_METHOD'] == 'DELETE' ) {
    $put_data = file_get_contents('php://input');
    $request_data = [];
    parse_str($put_data, $request_data);
}

$request_data['mode'] = (isset($request_data['mode']) && strlen($request_data['mode']) > 0) ? $request_data['mode'] : null;
$mode = $request_data['mode'];
$_module = new MaterialsModule;

if ( $request_method == 'POST' && $mode == 'tbl_load' )
{
    echo json_encode($_module->index($request_data));
}

if ( ($request_method == 'POST' || $request_method == 'PUT') && $mode == 'save' )
{
    $_mode = '';
    if ( $request_method == 'POST') {
        $_mode = 'save';
    } elseif ( $request_method == 'PUT' ) {
        $_mode = 'edit';
    }
    echo json_encode($_module->save($request_data, $_mode));
}

if ( $request_method == 'DELETE' && $mode == 'delete' )
{
    echo json_encode($_module->delete($request_data));
}