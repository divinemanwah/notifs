<?php

class break_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function breaklist($select = '*', $where = Array(), $breakmode = false) {

	/*
        $this->db->select($select, false)
                ->from("g4_member gm")
                ->join("dept dp", "dp.dept_no = gm.mb_deptno", "left")
                ->join("tk_attendance ta", "ta.mb_no = gm.mb_no and (CAST(CONCAT(ta.att_date,' ',ta.actual_in) as datetime) between (NOW() - interval 11 hour) and (NOW() + interval 2 hour)  or (DATE(now()) = ta.att_date and actual_in is not null))  and (ta.actual_out is not null or ta.actual_out = '')", "left")
                ->join("tk_member_schedule tms", "tms.mb_no = gm.mb_no AND (ta.actual_out IS NULL  OR ta.actual_out = '') AND ((ta.att_date is not null and DATE(CONCAT(tms.year,'-',LPAD(tms.month, 2, 0),'-',LPAD(tms.day, 2, 0))) = ta.att_date) OR (ta.att_date is null and DATE(CONCAT(tms.year,'-',LPAD(tms.month, 2, 0),'-',LPAD(tms.day, 2, 0))) BETWEEN DATE(NOW() - INTERVAL 1 DAY) AND DATE(NOW())))", "left")
                ->join("tk_shift_code tsc", "tms.shift_id = tsc.shift_id", "left")
                ->join("break_list bl", "bl.mb_no = gm.mb_no and bl.shift = CONCAT(tms.year,'-',LPAD(tms.month, 2, 0),'-',LPAD(tms.day, 2, 0))", "left")
        ;
		*/
		
		$this->db->select($select, false)
                ->from("g4_member gm")
                ->join("dept dp", "dp.dept_no = gm.mb_deptno", "left")
				->join("tk_member_schedule tms","tms.mb_no = gm.mb_no and DATE_FORMAT(concat(year,'-',month,'-',day),'%Y-%m-%d') IN(date(now()),date(now() - interval 1 day),date(now() + interval 1 day)) ","left")
				->join("tk_shift_code tsc","tsc.shift_id = tms.shift_id and now() between CAST(concat(DATE_FORMAT(concat(year,'-',month,'-',day),'%Y-%m-%d'),' ',LPAD(if(tsc.shift_hr_from=0,24,tsc.shift_hr_from),2,0),':',LPAD(tsc.shift_min_from,2,0),':00') as DATETIME)  and CAST(concat(IF(tsc.shift_hr_from<tsc.shift_hr_to,DATE_FORMAT(concat(year,'-',month,'-',day),'%Y-%m-%d'),DATE_FORMAT(concat(year,'-',month,'-',day),'%Y-%m-%d')+interval 1 day),' ',LPAD(if(tsc.shift_hr_to=0,24,tsc.shift_hr_to),2,0),':',LPAD(tsc.shift_min_to,2,0),':00') as DATETIME)","left")
				->join("tk_attendance ta","ta.mb_no = gm.mb_no and ta.att_date = DATE_FORMAT(concat(tms.year,'-',tms.month,'-',tms.day),'%Y-%m-%d') and (ta.shift_id >= 0 and ta.actual_in is not null and ta.actual_out is null)","left")
				->join("break_list bl", "bl.mb_no = gm.mb_no and bl.shift = CONCAT(tms.year,'-',LPAD(tms.month, 2, 0),'-',LPAD(tms.day, 2, 0))", "left")
				;
		
        $this->db->where("gm.mb_status", 1);
        $this->db->where($where);
		
		$this->db->where("(tms.leave_id = 0 or tms.leave_id is null)", NULL, FALSE);
		
		$this->db->where("(now() between CAST(concat(DATE_FORMAT(concat(year,'-',month,'-',day),'%Y-%m-%d'),' ',LPAD(if(tsc.shift_hr_from=0,24,tsc.shift_hr_from),2,0),':',LPAD(tsc.shift_min_from,2,0),':00') as DATETIME) and CAST(concat(IF(tsc.shift_hr_from<tsc.shift_hr_to,DATE_FORMAT(concat(year,'-',month,'-',day),'%Y-%m-%d'),DATE_FORMAT(concat(year,'-',month,'-',day),'%Y-%m-%d')+interval 1 day),' ',LPAD(if(tsc.shift_hr_to=0,24,tsc.shift_hr_to),2,0),':',LPAD(tsc.shift_min_to,2,0),':00') as DATETIME) or (ta.shift_id = 0 and ta.actual_in is not null and ta.actual_out is null))", NULL, FALSE);
		
		/*
        $this->db->where("(CAST(concat(tms.year,'-',LPAD(tms.month,2,0),'-',LPAD(tms.day,2,0),' ',LPAD(tsc.shift_hr_from,2,0),':',LPAD(tsc.shift_min_from,2,0),':00' ) as DATETIME) between (NOW() - interval 11 hour) and (NOW()) OR (tms.shift_id = 0 and ta.actual_in is not null))", NULL, FALSE);
        
        $this->db->where("(tms.leave_id is null or tms.leave_id = 0)", NULL, FALSE);


        $this->db->where("(bl.break_status = 0 or bl.break_status is null)", NULL, FALSE);

        

        $this->db->group_by("gm.mb_no");
		*/
        
		
		$this->db->order_by("dp.dept_name,gm.mb_nick,ta.actual_in DESC");

        //$this->db->stop_cache();
        //$count = $this->db->count_all_results();
        //$this->db->limit($per_page, $page ? ($page - 1) * $per_page : 0);

        $filtered = $this->db->get()->result();

        //$this->db->flush_cache();

        return array(
            //'count' => $count,
            'data' => $filtered
        );
    }

    function onbreaklist($select = '*', $where = Array()) {
        //$this->db->start_cache();
        $this->db->select($select, false)
                ->from("break_list bl")
                ->join("break_time bt", "bt.mb_no = bl.mb_no and bt.shift = bl.shift and bt.in is null", "left")
                ->join("g4_member gm", "gm.mb_no = bl.mb_no", "left")
                ->join("dept dp", "dp.dept_no = gm.mb_deptno", "left")
        ;

        $this->db->where("bl.break_status", 1);
        $this->db->where("gm.mb_status", 1);

        $this->db->where($where);

        $this->db->order_by("bl.last_in desc, bl.min desc", FALSE);

        //$this->db->stop_cache();
        //$count = $this->db->count_all_results();
        //$this->db->limit($per_page, $page ? ($page - 1) * $per_page : 0);

        $filtered = $this->db->get()->result();

        //$this->db->flush_cache();

        return array(
            //'count' => $count,
            'data' => $filtered
        );
    }
    
    function onbreakstatus($mb_no){
        $this->db->where("mb_no",$mb_no);
        $count = $this->db->from("break_list")->where("break_status",1)->count_all_results();
        return $count;
    }
    
    function breaktime($where = Array(), $tbl, $orderby, $join = 'left', $page = 0, $per_page = 0, $cond = Array(),$select = '*') {
        $this->db->start_cache();
		$this->db->select($select,FALSE);
		
        $this->db->order_by($orderby)
                ->join('g4_member gm', 'gm.mb_no = brk.mb_no', $join)
                ->join("dept dp", "dp.dept_no = gm.mb_deptno", "left")
                ->where($where)


        ;

        if (count($cond) > 0) {
            foreach ($cond as $whr)
                if (strlen($whr) > 0)
                    $this->db->where($whr, NULL, FALSE);
        }

        $this->db->stop_cache();

        $count = $this->db->from($tbl)->count_all_results();

        if ($page > 0)
            $this->db->limit($per_page, $page ? ($page - 1) * $per_page : 0);

        $brk = $this->db->get($tbl)->result_array();
        
        $this->db->flush_cache();
        
        return array(
            'count' => $count,
            'data' => $brk
        );
    }

    function updatebreak($where, $data, $tbl) {
        $update = $this->db;
        foreach ($where as $key => $find) {
            if (is_array($find) or is_object($find))
                $update = $update->where($find);
            elseif (array_key_exists($key, $where))
                $update = $update->where($key, $find);
            else
                $update = $update->where($find, NULL, FALSE);
        }

        foreach ($data as $chgkey => $add)
            $update = $update->set($chgkey, $add, FALSE);

        $update = $update->update($tbl);
        return $update;
    }

    function insertbreak($tbl, $data) {
        foreach ($data as $tblkey => $add)
            $this->db->set($tblkey, $add, FALSE);
        $this->db->insert($tbl);
    }

}
