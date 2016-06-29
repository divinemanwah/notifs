<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class breaktime extends MY_Controller {

    function __construct() {

        parent::__construct();
        $this->load->model('employees_model', 'emp_m');
        $this->load->model('break_model', 'break');
        $this->load->model('shifts_model', 'shifts');
    }

    /* Views */

    public function index() {
        $this->view_template('breaktime/breaktime', 'Breaktime', array(
            'breadcrumbs' => array('Break TIME'),
            'css' => array(
                'break.css'
            ),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'date-time/bootstrap-datepicker.min.js',
                'breaktime/breaktime.breakmode.js'
            )
        ));
    }

    public function breaklist() {

        $date = new DateTime();
        $tmp_date = new DateTime($date->format("Y-m-d H:i:s"));
        $tmp_date->modify("-1 day");

        $period_dtl = (object) array();
        $period_dtl->start = $tmp_date->format("Y-m-d");
        $period_dtl->end = $date->format("Y-m-d");


        $depts = $this->emp_m->get_report_modules(Array("a.mb_no" => $this->session->userdata("mb_no"), "report_id" => 3));
        $emp_list = $this->emp_m->getAll(false, '*', "", 0, 0, array(), (count($depts) > 0 ? Array("d.dept_no IN(" . implode(",", array_map(function($a) {
                                        return $a->dept_no;
                                    }, $depts)) . ") " => NULL) : Array("mb_no" => $this->session->userdata("mb_no"))));


        $this->view_template('breaktime/breaklist', 'Breaktime', array(
            'breadcrumbs' => array('BREAK LIST'),
            'css' => array(
                'break.css'
            ),
            'js' => array(
                'jquery.handsontable.full.min.js',
                'date-time/bootstrap-datepicker.min.js',
                'breaktime/breaklist.js'
            ),
            "cur_period" => $period_dtl,
            "emp_dept" => (count($depts) > 1) ? 0 : $this->session->userdata("mb_deptno"),
            "depts" => (count($depts) > 0) ? $depts : $emp_list,
            "emp_list" => $emp_list,
            "emp_id" => $this->session->userdata("mb_no")
        ));
    }

    public function logs() {
        $date = new DateTime();
        $tmp_date = new DateTime($date->format("Y-m-d H:i:s"));
        $tmp_date->modify("-7 day");

        $period_dtl = (object) array();
        $period_dtl->start = $tmp_date->format("Y-m-d");
        $period_dtl->end = $date->format("Y-m-d");


        $depts = $this->emp_m->get_report_modules(Array("a.mb_no" => $this->session->userdata("mb_no"), "report_id" => 3));
        $emp_list = $this->emp_m->getAll(false, '*', "", 0, 0, array(), (count($depts) > 0 ? Array("d.dept_no IN(" . implode(",", array_map(function($a) {
                                        return $a->dept_no;
                                    }, $depts)) . ") " => NULL) : Array("mb_no" => $this->session->userdata("mb_no"))));

        $this->view_template('breaktime/logs', 'Breaktime', array(
            'breadcrumbs' => array('BREAKTIME LOGS'),
            'css' => array(
                'break.css'
            ),
            'js' => array(
                'jquery.handsontable.full.min.js',
                'date-time/bootstrap-datepicker.min.js',
                'breaktime/logs.js'
            ),
            "cur_period" => $period_dtl,
            "emp_list" => $emp_list,
            "emp_id" => $this->session->userdata("mb_no")
        ));
    }

    /* End of Views */

    public function datalogs() {

        $post = $this->input->post();
        $date = new DateTime();
        $tmp_date = new DateTime($date->format("Y-m-d H:i:s"));
        $tmp_date->modify("-7 day");
        $depts = $this->emp_m->get_report_modules(Array("a.mb_no" => $this->session->userdata("mb_no"), "report_id" => 3)); 
        
        $emp = isset($post["emp"]) ? $post["emp"] : (count($depts) > 0 ? "" : $this->session->userdata("mb_no"));
        $date_from = $post["datefrom"] ? $post["datefrom"] : $tmp_date->format("Y-m-d");
        $date_to = $post["dateto"] ? $post["dateto"] : $date->format("Y-m-d");
        $limit = isset($post["limit"]) ? $post["limit"] : 15;
        $page = isset($post["page"]) ? $post["page"] : 1;

        $where = Array("gm.mb_status" => 1);
        $cond = Array("brk.shift between '" . $date_from . "' and '" . $date_to . "' ", ($emp ? "gm.mb_no = " . $emp : (count($depts) > 0 ?"":"gm.mb_no = " .$this->session->userdata("mb_no"))),(count($depts) > 0 ?"dp.dept_no IN(" . implode(",", array_map(function($a) {
                                        return $a->dept_no;
                                    }, $depts)) . ") ":""));

        $brk = $this->break->breaktime($where, 'break_time brk', "brk.shift DESC", "inner", $page, $limit, $cond,"gm.mb_no, gm.mb_id, gm.mb_nick, gm.mb_lname, dept_name, shift, brk.out, brk.in, brk.render, (select concat(gg.mb_nick,' ') from g4_member gg where gg.mb_no = brk.tagged_by) tagged, (select concat(g.mb_nick,' ') from g4_member g where g.mb_no = brk.untagged_by) untagged ");
        //echo $this->db->last_query();
        echo json_encode(Array("data" => $brk['data'], "count" => $brk['count'], "page" => $page));
    }

    public function shiftlist() {

        $shift = $this->shifts->getAll(false, "shift_id,shift_code,concat(LPAD(shift_hr_from,2,0),':',LPAD(shift_min_from,2,0),':00')time_from,concat(LPAD(shift_hr_to,2,0),':',LPAD(shift_min_to,2,0),':00')time_to");

        echo json_encode($shift);
    }

    public function brklist($shift_id = 0) {

        $depts = $this->emp_m->get_report_modules(Array("a.mb_no" => $this->session->userdata("mb_no"), "report_id" => 3));
        $empsel = " gm.mb_no, concat(gm.mb_nick,' ', gm.mb_lname) fname, dp.dept_name, CONCAT(tms.year,'-',LPAD(tms.month, 2, 0),'-',LPAD(tms.day, 2, 0)) shift_date,bl.last_out,bl.last_in, IFNULL(bl.min,0)min, IFNULL(bl.total_break,0)total_break";
        $where = "gm.mb_no != ". $this->session->userdata("mb_no") ." AND dp.dept_no IN(" . implode(",", array_map(function($a) { return $a->dept_no; }, $depts)) . ") ";

        if (isset($shift_id) and $shift_id !== 0 and $shift_id !== "0")
            $where = ((strlen($where) > 0) ? $where . " AND (" : "(") . "tms.shift_id =" . $shift_id . " or tms.shift_id is null or tms.shift_id = 0)";

        if (count($depts) > 0)
            $shift_list = $this->break->breaklist($empsel, $where);
            //print_r($this->db->last_query());
        echo json_encode($shift_list);
    }

    public function getlogs() {

        $post = $this->input->post();
        $date = new DateTime();
        $tmp_date = new DateTime($date->format("Y-m-d H:i:s"));
        $tmp_date->modify("-1 day");

        $depts = $this->emp_m->get_report_modules(Array("a.mb_no" => $this->session->userdata("mb_no"), "report_id" => 3));

        $onbrk = $post["onbrk"] ? $post["onbrk"] : 0;
        $overbrk = $post["overbrk"] ? (($post["overbrk"] == 1) ? 61 : 0) : 0;
        $dept = !isset($post["dept"]) ? $this->session->userdata("mb_deptno") : (($post["dept"] !== "0") ? $post["dept"] : implode(",", array_map(function($a) {
                                    return $a->dept_no;
                                }, $depts)));
        $emp = isset($post["emp"]) ? $post["emp"] : (count($depts) > 0 ? "" : $this->session->userdata("mb_no"));
        $date_from = $post["datefrom"] ? $post["datefrom"] : $tmp_date->format("Y-m-d");
        $date_to = $post["dateto"] ? $post["dateto"] : $date->format("Y-m-d");
        $limit = isset($post["limit"]) ? $post["limit"] : 15;
        $page = isset($post["page"]) ? $post["page"] : 1;


        $where = Array("brk.break_status" => $onbrk, "brk.min >=" => $overbrk);
        $cond = Array("brk.shift between '" . $date_from . "' and '" . $date_to . "' ", ((strlen($dept) > 0) ? "dp.dept_no IN(" . $dept . ")" : ""), ($emp ? "gm.mb_no = " . $emp : ""));

        $logs = $this->break->breaktime($where, 'break_list brk', "brk.shift DESC", "inner", $page, $limit, $cond);
        //echo $this->db->last_query();
        echo json_encode(Array("data" => $logs['data'], "count" => $logs['count'], "page" => $page));
    }

    public function onbreak() {
        $depts = $this->emp_m->get_report_modules(Array("a.mb_no" => $this->session->userdata("mb_no"), "report_id" => 3));
        $empsel = "bt.id, gm.mb_no, concat(gm.mb_nick,' ', gm.mb_lname) fname, dp.dept_name, bt.out";

        if (count($depts) > 0)
            $shift_list = $this->break->onbreaklist($empsel, "dp.dept_no IN(" . implode(",", array_map(function($a) {
                                return $a->dept_no;
                            }, $depts)) . ")");

        echo json_encode($shift_list);
    }

    public function breakout($mb_no, $shift) {
        $last_in = "";
        $min = 0;
        $brklist = $this->break->breaktime(Array("brk.mb_no" => $mb_no, "brk.shift" => $shift), "break_list brk", "brk.mb_no");
        $emp = $this->emp_m->getAll(false, "d.dept_no", "", 0, 0, NULL, Array("m.mb_no" => $mb_no));

        if ($brklist["count"] > 0) {
            /**
              $brktime = $this->break->breaktime(Array("brk.mb_no" => $mb_no), "break_time brk", "brk.mb_no,brk.shift");
              foreach($brktime as $key => $brk) {
              $totalbrk += $brk["render"];
              $last_in = (strlen($last_in) > 0) ? $brk["`in`"] : "'".$last_in."'";
              }
             * $this->break->updatebreak(Array("mb_no" => $mb_no), Array("break_status" => 1, "last_in" => $last_in, "min" => $min), "break_list");
             * 
             */
            $this->break->updatebreak(Array("mb_no" => $mb_no, "shift" => $shift), Array("break_status" => 1), "break_list");
        } else {
            $this->break->insertbreak("break_list", Array("mb_no" => $mb_no, "dept_no" => $emp[0]->dept_no, "shift" => "'" . $shift . "'"));
        }

        $this->break->insertbreak("break_time", Array("mb_no" => $mb_no, "dept_no" => $emp[0]->dept_no, "shift" => "'" . $shift . "'", "out" => "now()"));
    }

    public function breakin($id) {
        $brklist = $this->break->breaktime(Array("brk.id" => $id), "break_time brk", "brk.id");

        if ($brklist["count"] > 0) {
            $this->break->updatebreak(Array("id" => $id), Array("in" => "now()", "render" => "TIMESTAMPDIFF(MINUTE,`out`,NOW())"), "break_time");
            $this->break->updatebreak(Array("mb_no" => $brklist["data"][0]["mb_no"], "shift" => $brklist["data"][0]["shift"]), Array("break_status" => 0, "last_in" => "(select `in` from break_time where id = " . $id . ")", "last_out" => "(select `out` from break_time where id = " . $id . ")", "min" => "(select sum(render) from break_time where mb_no = " . $brklist["data"][0]["mb_no"] . " and shift = '" . $brklist["data"][0]["shift"] . "')", "total_break" => "(select count(*) from break_time where mb_no = " . $brklist["data"][0]["mb_no"] . " and shift = '" . $brklist["data"][0]["shift"] . "')"), "break_list");
        }
    }

    public function chgbreakout() {
        $post = $this->input->post();
        foreach ($post["data"] as $key => $data) {
            $mb_no = $data["mb_no"];
            $shift = $data["shift_date"];
            $brklist = $this->break->breaktime(Array("brk.mb_no" => $mb_no, "brk.shift" => $shift), "break_list brk", "brk.mb_no");
            
            $emp = $this->emp_m->getAll(false, "d.dept_no", "", 0, 0, NULL, Array("m.mb_no" => $mb_no));
            
            if ($brklist["count"] > 0) {
                if($brklist["data"][0]["break_status"] == "0")
                    $this->break->updatebreak(Array("mb_no" => $mb_no, "shift" => $shift), Array("break_status" => 1), "break_list");
            } else {
                $this->break->insertbreak("break_list", Array("mb_no" => $mb_no, "dept_no" => $emp[0]->dept_no, "total_break" => 1, "shift" => "'" . $shift . "'"));
            }
            if(($brklist["count"] > 0 and $brklist["data"][0]["break_status"] == 0) or $brklist["count"] == 0)
            $this->break->insertbreak("break_time", Array("mb_no" => $mb_no, "dept_no" => $emp[0]->dept_no, "shift" => "'" . $shift . "'", "out" => "now()","tagged_by"=>$this->session->userdata("mb_no")));
        }
    }

    public function chgbreakin() {
        $post = $this->input->post();
        foreach ($post["data"] as $key => $data) {
            $id = $data["id"];
            $brklist = $this->break->breaktime(Array("brk.id" => $id), "break_time brk", "brk.id");
            if ($brklist["count"] > 0 and strlen($brklist["data"][0]["in"])==0) {
                $this->break->updatebreak(Array("id" => $id), Array("in" => "now()", "render" => "TIMESTAMPDIFF(MINUTE,`out`,NOW())","untagged_by"=>$this->session->userdata("mb_no")), "break_time");
                $this->break->updatebreak(Array("mb_no" => $brklist["data"][0]["mb_no"], "shift" => $brklist["data"][0]["shift"]), Array("break_status" => 0, "last_in" => "(select `in` from break_time where id = " . $id . ")", "last_out" => "(select `out` from break_time where id = " . $id . ")", "min" => "(select sum(render) from break_time where mb_no = " . $brklist["data"][0]["mb_no"] . " and shift = '" . $brklist["data"][0]["shift"] . "')", "total_break" => "(select count(*) from break_time where mb_no = " . $brklist["data"][0]["mb_no"] . " and shift = '" . $brklist["data"][0]["shift"] . "')"), "break_list");
            }
        }
    }

}
