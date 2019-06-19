<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends My_Model {

    public function get_data_by_id($table_name = '', $column_name = '', $column_value = '', $data = '', $condition_array = array(), $order_by = '', $sort_by = 'ASC', $limit = '', $result = 'array') {
        $this->db->select($data);
        $this->db->from($table_name);
        $this->db->where($column_name, $column_value);
        if (!empty($condition_array)) {
            $this->db->where($condition_array);
        }
        if ($order_by != '' && $sort_by != '') {
            $this->db->order_by($order_by, $sort_by);
        }
        if (!empty($limit))
            $this->db->limit($limit);
        if ($result == 'row')
            return $this->db->get()->row_array();
        else
            return $this->db->get()->result_array();
    }

    public function get_data_by_join($from = '', $data = '', $join = array(), $condition_array = array(), $order_by = '', $sort_by = 'ASC', $limit = '', $group_by = '', $result = 'array') {
        $this->db->select($data);
        $this->db->from($from);
        if (!empty($join)) {
            foreach ($join as $key => $value) {
                $this->db->join($key, $value, 'left');
            }
        }
        if (!empty($condition_array)) {
            $this->db->where($condition_array);
        }
        if ($order_by != '' && $sort_by != '') {
            $this->db->order_by($order_by, $sort_by);
        }
        if (!empty($limit))
            $this->db->limit($limit);
        if (!empty($order_by))
            $this->db->order_by($order_by);
        if ($result == 'row')
            return $this->db->get()->row_array();
        else
            return $this->db->get()->result_array();
    }

    public function insert_data($tabel_name = '', $data = array()) {
        $id = 0;
        if (!empty($tabel_name)) {
            $this->db->insert($tabel_name, $data);
            $id = $this->db->insert_id();
        }
        return $id;
    }

    public function update_data($data = array(), $tabel_name = '', $column_name = '', $column_value = '') {
        $this->db->set($data);
        $this->db->where($column_name, $column_value);
        $this->db->update($tabel_name);
        return TRUE;
    }

    public function delete_data($tabel_name = '', $condition_array = array()) {
        if (!empty($condition_array)) {
            $this->db->where($condition_array);
            $this->db->delete($tabel_name);
            return TRUE;
        }
        return FALSE;
    }

    
    function select_data_by_id($tablename, $columnname, $columnid, $data = '*') {
        if ($data != '*')
            $this->db->select($data);
        $this->db->where($columnname, $columnid);
        $query = $this->db->get($tablename);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }


      // select data using multiple conditions
    function select_data_by_in_condition($tablename, $contition_array = array(), $data = '*', $sortby = '', $orderby = '', $limit = '', $offset = '', $join_str = array(),$groupby = '',$colum,$columnData) {
        // echo pre($wherein); die();
        
        $this->db->select($data);
        if (!empty($join_str)) {
           // pre($join_str);
            foreach ($join_str as $join) {
                if ($join['join_type'] == '') {
                $this->db->join($join['table'], $join['join_table_id'] . '=' . $join['from_table_id']);
                }
                else{
                    $this->db->join($join['table'], $join['join_table_id'] . '=' . $join['from_table_id'], $join['join_type']);
                }
            }
        }
        if(!empty($colum)){
        $this->db->where_in($colum,$columnData);
         }
        $this->db->where($contition_array);
        
        if(!empty($having)){
            $this->db->having($having);
        }
        //Setting Limit for Paging
        if ($limit != '' && $offset == 0) {
            $this->db->limit($limit);
        } else if ($limit != '' && $offset != 0) {
            $this->db->limit($limit, $offset);
        }
        //order by query
        if ($sortby != '' && $orderby != '') {
            $this->db->order_by($sortby, $orderby);
        }
        
        $query = $this->db->get($tablename);
        // echo  $this->db->last_query();exit();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }


      // select data using multiple conditions
    function select_data_by_condition($tablename, $contition_array = array(), $data = '*', $sortby = '', $orderby = '', $limit = '', $offset = '', $join_str = array(), $groupby = '') {
        
        
        $this->db->select($data);
        if (!empty($join_str)) {
           // pre($join_str);
            foreach ($join_str as $join) {
                if ($join['join_type'] == '') {
                $this->db->join($join['table'], $join['join_table_id'] . '=' . $join['from_table_id']);
                }
                else{
                    $this->db->join($join['table'], $join['join_table_id'] . '=' . $join['from_table_id'], $join['join_type']);
                }
            }
        }
        $this->db->where($contition_array);
        if(!empty($having)){
            $this->db->having($having);
        }
        //Setting Limit for Paging
        if ($limit != '' && $offset == 0) {
            $this->db->limit($limit);
        } else if ($limit != '' && $offset != 0) {
            $this->db->limit($limit, $offset);
        }
        //order by query
        if ($sortby != '' && $orderby != '') {
            $this->db->order_by($sortby, $orderby);
        }
        $this->db->group_by($groupby);
        $query = $this->db->get($tablename);
        // echo  $this->db->last_query();exit();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    // select data using multiple conditions and search keyword
    function select_data_by_search($tablename, $search_condition, $contition_array = array(), $data = '*', $sortby = '', $orderby = '', $limit = '', $offset = '', $join_str = array(), $groupby = '') {
        $this->db->select($data);
        if (!empty($join_str)) {
            foreach ($join_str as $join) {
                $this->db->join($join['table'], $join['join_table_id'] . '=' . $join['from_table_id']);
            }
        }
        $this->db->where($contition_array);
        if(!empty($search_condition))
        $this->db->where($search_condition);
        //Setting Limit for Paging
        if ($limit != '' && $offset == 0) {
            $this->db->limit($limit);
        } else if ($limit != '' && $offset != 0) {
            $this->db->limit($limit, $offset);
        }
        //order by query
        if ($sortby != '' && $orderby != '') {
            $this->db->order_by($sortby, $orderby);
        }
        $this->db->group_by($groupby);
        $query = $this->db->get($tablename);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

       // select data using multiple conditions and search keyword
    function select_data_by_in_search($tablename, $search_condition, $contition_array = array(), $data = '*', $sortby = '', $orderby = '', $limit = '', $offset = '', $join_str = array(), $groupby = '',$colum,$columnData) {
        $this->db->select($data);
        if (!empty($join_str)) {
            foreach ($join_str as $join) {
                $this->db->join($join['table'], $join['join_table_id'] . '=' . $join['from_table_id']);
            }
        }
        if(!empty($colum)){
        $this->db->where_in($colum,$columnData);
         }
        $this->db->where($contition_array);
        if(!empty($search_condition))
        $this->db->where($search_condition);
        //Setting Limit for Paging
        if ($limit != '' && $offset == 0) {
            $this->db->limit($limit);
        } else if ($limit != '' && $offset != 0) {
            $this->db->limit($limit, $offset);
        }
        //order by query
        if ($sortby != '' && $orderby != '') {
            $this->db->order_by($sortby, $orderby);
        }
        $this->db->group_by($groupby);
        $query = $this->db->get($tablename);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    // check unique avaliblity
    function check_unique_avalibility($tablename, $columname1, $columnid1_value, $columname2, $columnid2_value, $condition_array = array()) {
        // if edit than $columnid2_value use
        if ($columnid2_value != '') {
            $this->db->where($columname2 . " !=", $columnid2_value); //in this line make space between " and !=
        }
        if (!empty($condition_array)) {
            $this->db->where($condition_array);
        }
        $this->db->where($columname1, $columnid1_value);
        $query = $this->db->get($tablename);
        if ($query->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

}
