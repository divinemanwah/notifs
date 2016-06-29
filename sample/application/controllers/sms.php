<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sms extends MY_Controller {

    function __construct() {

        parent::__construct();
        $this->load->model('employees_model', 'emp_m');
        $this->load->model('sms_model', 'sms_m');
    }

    /* Views */

    public function messages() {
        $emp_list = $this->emp_m->getAll(false, "*", false);
        $today = new DateTime();
        $allow_search = false;

		$depts = $this->emp_m->get_report_modules(Array("a.mb_no" => $this->session->userdata("mb_no"), "report_id" => 2));
            
		if (count($depts) > 0) {
			//$depts = $this->emp_m->getDeptHeads(array("h.employee_id"=>$this->session->userdata("user_id")));
			$dept_head_list = array();
			foreach ($depts as $dept) {
				$dept_head_list[] = $dept->dept_no;
			}
			$allow_search = true;
			$emp_list = $this->emp_m->getAll(false, "*", false, 0, 0, array("mb_lname" => "ASC"), array("mb_deptno IN (" . implode(",", $dept_head_list) . ") AND " => 1));
			$dept_list = $this->emp_m->getDepts("dept_no IN (" . implode(",", $dept_head_list) . ")");
		} else {
			$emp_list = $this->emp_m->getAll(false, "*", false, 0, 0, array("mb_lname" => "ASC"), array("mb_no" => $this->session->userdata("mb_no")));
			$dept_list = $this->emp_m->getDepts("dept_no = '" . $this->session->userdata("mb_deptno") . "'");
		}

        $this->view_template('sms/messages', 'SMS', array(
            'breadcrumbs' => array('Received Messages'),
            'js' => array(
                'jquery.dataTables.min.js',
                'jquery.dataTables.bootstrap.js',
                'date-time/bootstrap-datepicker.min.js',
                'sms.messages.js'
            ),
            'depts' => $dept_list,
            'emp_list' => $emp_list,
            'emp_id' => 0,
            'emp_dept' => $this->session->userdata("mb_deptno"),
            'today' => $today->format("Y-m-d"),
            'allow_search' => $allow_search
        ));
    }

	/* End of Views */
	
    public function getAllMessages() {
        $post = $this->input->post();
        $order_arr = array();
        if (is_array($post['order'])) {
            foreach ($post['order'] as $orderDtl) {
                $order_arr[$post['columns'][$orderDtl['column']]['data']] = $orderDtl['dir'];
            }
        }

        $search_str = "";

        
        $depts = $this->emp_m->get_report_modules(Array("a.mb_no" => $this->session->userdata("mb_no"), "report_id" => 2));
        if (count($depts) > 0) {
            
            $dept_head_list = array();
            foreach ($depts as $dept) {
                $dept_head_list[] = $dept->dept_no;
            }

            if($post["dept"] == 0){
                $search_str .= (empty($search_str) ? "" : " AND ") . "d.dept_no IN (" . implode(",", $dept_head_list) . ") ";
            }

            if (!empty($post["dept"])) {
                   $search_str .= (empty($search_str) ? "" : " AND ") . "d.dept_no = '" . $post["dept"] . "' ";
            }
            
            if (!empty($post["emp"])) {
                $search_str .= (empty($search_str) ? "" : " AND ") . "gm.mb_no = '" . $post["emp"] . "' ";
            }
            
        }else{
                $search_str .= (empty($search_str) ? "" : " AND ") . "gm.mb_no = '" . $this->session->userdata("mb_no") . "' ";
        }
        
		if($this->session->userdata("mb_deptno") == 24 && !empty($search_str) && empty($post["emp"]) && empty($post["dept"]))
			$search_str = "(".$search_str." OR (gm.mb_no IS NULL AND sms_in_text not like '%test%') ) ";
		
        if (!empty($post["from"])) {
            $search_str .= (empty($search_str) ? "" : " AND ") . "hsi.sms_in_datetime >= '" . $post["from"] . " 00:00:00' ";
        }

        if (!empty($post["to"])) {
            $search_str .= (empty($search_str) ? "" : " AND ") . "hsi.sms_in_datetime <= '" . $post["to"] . " 23:59:59' ";
        }

        $having_str = "";
        if (!empty($post['search']['value'])) {
            foreach ($post['columns'] as $column) {
                if ($column['searchable'] == "true") {
                    $having_str .= (empty($having_str) ? "" : " OR ") . $column['data'] . " LIKE '%" . $post['search']['value'] . "%' ";
                }
            }
        }
		
        $having_str = empty($having_str) ? $search_str : "(" . $search_str . ") AND (" . $having_str . ")";

        $select_str = "hsi.*, UCASE(hsi.code) code, gm.mb_no, d.dept_no, DATE(hsi.sms_in_datetime) sms_in_date, IFNULL(gm.mb_nick,'N/A') mb_nick, IFNULL(gm.mb_lname,'') mb_lname, d.dept_name, CASE hsi.status WHEN '1' THEN 'Success' WHEN '2' THEN 'Invalid PIN Code' ELSE 'Invalid' END status_lbl";

        $data = $this->sms_m->getAllMessagesFiltered($select_str, $search_str);
        $all_approval_count = count($data);

        $data_all = $this->sms_m->getAllMessagesFiltered($select_str, $having_str);
        $all_filtered_count = count($data_all);

        $data = $this->sms_m->getAllMessagesFiltered($select_str, $having_str, $post['start'], $post['length'], $order_arr);

        echo json_encode(array("data" => $data,
            "draw" => (int) $post["draw"],
            "recordsTotal" => $all_approval_count,
            "recordsFiltered" => $all_filtered_count, "having"=>$having_str));
    }

	public function getTotalMessagesForTheDay() {
		$today = new DateTime();
		$search_str = "DATE(sms_in_datetime) = '".$today->format("Y-m-d")."'";
		$depts = $this->emp_m->get_report_modules(Array("a.mb_no" => $this->session->userdata("mb_no"), "report_id" => 2));
        if (count($depts) > 0) {
            $dept_head_list = array();
            foreach ($depts as $dept) {
                $dept_head_list[] = $dept->dept_no;
            }
			$search_str .= (empty($search_str) ? "" : " AND ") . "d.dept_no IN (" . implode(",", $dept_head_list) . ") ";
        }
		else {
                $search_str .= (empty($search_str) ? "" : " AND ") . "gm.mb_no = '" . $this->session->userdata("mb_no") . "' ";
        }
        $data = $this->sms_m->getAllMessagesFiltered("*", $search_str);
        $all_approval_count = count($data);
		
		echo json_encode(array("recordsTotal" => $all_approval_count));
	}
	
}
