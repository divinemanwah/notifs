<?php

class Attendance_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function getAllAttendanceFiltered($select = '*', $where = "", $start = 0, $limit = 0, $order_arr = array()) {
        $this->db->select($select, false)
                ->from('tk_attendance a');

        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($limit > 0)
            $this->db->limit($limit, $start);

        if (!empty($order_arr)) {
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        }

        return $this->db->get()->result();
    }

    public function add_chg_att($data) {
        return $this->db->insert('tk_attendance_changes', $data);
    }

    public function update_chg_att($data, $param) {
        return $this->db->update('tk_attendance_changes', $data, $param);
         
    }

    public function update_attendance($where) {
        $att = $this->db->where($where, NULL, FALSE);
        $att = $att->set("ta.actual_in", "if(ta.actual_in = tac.new_in or tac.new_in is null or tac.new_in = '',ta.actual_in,tac.new_in)", FALSE);
        $att = $att->set("ta.actual_out", "if(ta.actual_out = tac.new_out or tac.new_out is null or tac.new_out = '', ta.actual_out,tac.new_out)", FALSE);
        $att = $att->update("tk_attendance ta inner join tk_attendance_changes tac on ta.att_id = tac.att_id");
        return $att;
    }
    public function ins_att($data){
        $this->db->insert('tk_attendance', $data);
        return $this->db->insert_id();
    }
    
    public function att_update($where,$param){
        $attinfo = $this->db->where($where, NULL, FALSE);

        foreach($param as $k => $v) {
            $attinfo = $attinfo->set($k, $v, FALSE);
        }

        $attinfo = $attinfo->update("tk_attendance");
        return $attinfo;
    }

    public function change_att_info($select = '*', $where = "", $start = 0, $limit = 0, $order_arr = array()) {
        $this->db->select($select, false)
                ->from('tk_attendance_changes tac')
                ->join('g4_member gm', 'gm.mb_no = tac.mb_no', 'inner');

        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($limit > 0)
            $this->db->limit($limit, $start);

        if (!empty($order_arr)) {
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        }

        return $this->db->get()->result();
    }

    public function getAllLogsFiltered($select = '*', $where = "", $start = 0, $limit = 0, $order_arr = array()) {
        $this->db->select($select, false)
                ->from('tk_biometric_log a')
                ->join('g4_member gm', 'a.enroll_number = gm.enroll_number', 'inner');

        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($limit > 0)
            $this->db->limit($limit, $start);

        if (!empty($order_arr)) {
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        }

        return $this->db->get()->result();
    }

    public function getAllAWOL($select = '*', $having = "", $start = 0, $limit = 0, $order_arr = array()) {
        $this->db->select($select, false)
                ->from('tk_awol a')
                ->join('g4_member gm', 'a.created_by = gm.mb_no', 'left');

        //$this->db->where(Array('a.awol_status'=>1));

        if (!empty($having)) {
            $this->db->having($having);
        }

        if ($limit > 0)
            $this->db->limit($limit, $start);

        if (!empty($order_arr)) {
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        }

        return $this->db->get()->result();
    }

    public function getAllAWOLHistory($select = '*', $having = "", $start = 0, $limit = 0, $order_arr = array()) {
        $this->db->select($select, false)
                ->from('tk_awol_history a')
                ->join('g4_member gm', 'a.created_by = gm.mb_no', 'left');

        //$this->db->where(Array('a.awol_status'=>1));

        if (!empty($having)) {
            $this->db->where($having);
        }

        if ($limit > 0)
            $this->db->limit($limit, $start);

        if (!empty($order_arr)) {
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        }

        return $this->db->get()->result();
    }

    public function insertAWOL($data) {
        return $this->db->insert("tk_awol", $data);
    }

    public function updateAWOL($data, $param) {
        return $this->db->update("tk_awol", $data, $param);
    }

    public function getEmployeeAttendance($select = '*', $where = "", $start = 0, $limit = 0, $order_arr = array(), $group_arr = array()) {
        $this->db->select($select, false)
                ->from('tk_member_schedule tms')
                ->join('g4_member gm', 'tms.mb_no = gm.mb_no', 'inner')
                ->join('dept d', 'd.dept_no = gm.mb_deptno', 'inner')
                ->join('tk_shift_code tsc', 'tms.shift_id = tsc.shift_id', 'inner')
                ->join('tk_attendance ta', 'tms.mb_no = ta.mb_no AND CONCAT(tms.year,"-",LPAD(tms.month, 2, "0"),"-",LPAD(tms.day, 2, "0")) = ta.att_date', 'left')
                ->join('tk_leave_code tlc', 'tms.leave_id = tlc.leave_id', 'left')
                ->join('tk_lv_application tla', 'tms.lv_app_id = tla.lv_app_id', 'left')
                ->join('tk_leave_sub_categ tlsc', 'tla.sub_categ_id = tlsc.leave_id', 'left')
                ->join('tk_awol tawol', 'CONCAT(tms.year,"-",LPAD(tms.month,2,0),"-",LPAD(tms.day,2,0)) = tawol.att_date AND tms.mb_no = tawol.mb_no AND tawol.awol_status = 1', 'left')
        ;
        //$this->db->where(Array('tawol.awol_status'=>1));
        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($limit > 0)
            $this->db->limit($limit, $start);

        if (!empty($order_arr)) {
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        }

        if (!empty($group_arr)) {
            foreach ($group_arr as $field)
                $this->db->group_by($field);
        }
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    public function getEmployeeAttendanceDetails($select = '*', $where = "", $start = 0, $limit = 0, $order_arr = array(), $group_arr = array()) {
        $this->db->select($select, false)
                ->from('tk_member_schedule tms')
                ->join('g4_member gm', 'tms.mb_no = gm.mb_no', 'inner')
                ->join('dept d', 'd.dept_no = gm.mb_deptno', 'inner')
                ->join('tk_shift_code tsc', 'tms.shift_id = tsc.shift_id', 'inner')
                ->join('tk_attendance ta', 'tms.mb_no = ta.mb_no AND CONCAT(tms.year,"-",LPAD(tms.month, 2, "0"),"-",LPAD(tms.day, 2, "0")) = ta.att_date', 'left')
                ->join('tk_change_sched_req tcsq', 'tcsq.mb_no = gm.mb_no and ta.att_date BETWEEN tcsq.att_date_from and tcsq.att_date_to', 'left')
                ->join('tk_leave_code tlc', 'tms.leave_id = tlc.leave_id', 'left')
                ->join('tk_lv_application tla', 'tla.mb_no = gm.mb_no and CONCAT(tms.year,\'-\',LPAD(tms.month, 2, \'0\'),\'-\',LPAD(tms.day, 2, \'0\')) BETWEEN date(tla.date_from) and date(tla.date_to)', 'left')
                /** ->join('tk_lv_application_approval tlaa', 'tlaa.lv_app_id = tla.lv_app_id', 'left') * */
                ->join('tk_obt_application toa', 'toa.mb_no = gm.mb_no and CONCAT(tms.year,\'-\',LPAD(tms.month, 2, \'0\'),\'-\',LPAD(tms.day, 2, \'0\')) BETWEEN toa.date and toa.date', 'left')
                ->join('tk_leave_sub_categ tlsc', 'tla.sub_categ_id = tlsc.leave_id', 'left')
                ->join('tk_awol tawol', 'CONCAT(tms.year,"-",LPAD(tms.month,2,0),"-",LPAD(tms.day,2,0)) = tawol.att_date AND tms.mb_no = tawol.mb_no AND tawol.awol_status = 1', 'left')
        ;
        //$this->db->where(Array('tawol.awol_status'=>1));
        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($limit > 0)
            $this->db->limit($limit, $start);

        if (!empty($order_arr)) {
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        }

        if (!empty($group_arr)) {
            foreach ($group_arr as $field)
                $this->db->group_by($field);
        }
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

    function query($query) {
        return $this->db->query($query);
    }
    
    public function getAttinfo($select = '*', $where = "", $start = 0, $limit = 0, $order_arr = array(), $group_arr = array()) {
                $this->db->select($select, false)
                ->from('tk_attendance ta')
                ->join('tk_shift_code tsc', 'ta.shift_id = tsc.shift_id', 'left');

        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($limit > 0)
            $this->db->limit($limit, $start);

        if (!empty($order_arr)) {
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        }

        if (!empty($group_arr)) {
            foreach ($group_arr as $field)
                $this->db->group_by($field);
        }

        return $this->db->get()->result();
    }
    
    public function getEmployeeAttendanceSummary($select = '*', $where = "", $start = 0, $limit = 0, $order_arr = array(), $group_arr = array()) {
        $this->db->select($select, false)
                ->from('tk_member_schedule tms')
                ->join('g4_member gm', 'tms.mb_no = gm.mb_no', 'inner')
                ->join('dept d', 'd.dept_no = gm.mb_deptno', 'inner')
                ->join('tk_shift_code tsc', 'tms.shift_id = tsc.shift_id', 'inner')
                ->join('tk_attendance ta', 'tms.mb_no = ta.mb_no AND CONCAT(tms.year,"-",LPAD(tms.month, 2, "0"),"-",LPAD(tms.day, 2, "0")) = ta.att_date', 'left')
                ->join('tk_obt_application toa', 'toa.mb_no = tms.mb_no AND CONCAT(tms.year, "-", LPAD(tms.month, 2, "0"), "-", LPAD(tms.day, 2, "0")) = `toa`.`date`', 'left')
                ->join('tk_awol tawol', 'CONCAT(tms.year,"-",LPAD(tms.month,2,0),"-",LPAD(tms.day,2,0)) = tawol.att_date AND tms.mb_no = tawol.mb_no AND tawol.awol_status = 1', 'left')
        ;
        //$this->db->where(Array('tawol.awol_status'=>1));

        if (!empty($where)) {
            $this->db->where($where);
        }

        if ($limit > 0)
            $this->db->limit($limit, $start);

        if (!empty($order_arr)) {
            foreach ($order_arr as $field => $dir)
                $this->db->order_by($field, $dir);
        }

        if (!empty($group_arr)) {
            foreach ($group_arr as $field)
                $this->db->group_by($field);
        }
        // $this->db->get();
        // echo $this->db->last_query();
        return $this->db->get()->result();
    }

}
