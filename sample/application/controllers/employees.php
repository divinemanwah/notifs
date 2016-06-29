<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Employees extends MY_Controller {

	function __construct() {
	
		parent::__construct();
		
		$this->load->model('employees_model', 'employees');
		$this->load->model('violations_model', 'violations');
        $this->load->model('kpi_model', 'kpi');
		$this->load->model('overtime_model', 'overtime');
		$this->load->model('leaves_model', 'leave');
		$this->load->model('obt_model', 'obt');
		$this->load->model('break_model', 'break');
		
		$this->load->model('shifts_model', 'shifts');
		
		$this->lang->load('employees');
		
        $this->report_status = array(
                1 => array("value" => 1, "label" => lang('employees_report_status_attendance_label')),
                2 => array("value" => 2, "label" => "SMS"),
                3 => array("value" => 3, "label" => lang('employees_report_status_break_label'))
                //4=>array("value"=>4, "label"=>"OBT"), 
                //5=>array("value"=>5, "label"=>"CWS")
        );
    }

	public function index()	{
		$this->view_template('employees_list', lang('employees_title'), array(
			'breadcrumbs' => array(lang('employees_view_all')),
			'css' => array(
					'jquery.gritter.css',
					'bootstrap-editable.css'
				),
			'js' => array(
					'jquery.maskedinput.min.js',
					'ajaxq.js',
					'jquery.gritter.min.js',
                'bootbox.min.js',
					'x-editable/bootstrap-editable.min.js',
					'x-editable/ace-editable.min.js',
					'employees.js'
				),
			'depts' => $this->employees->getDepts(),
            'condos' => $this->employees->getCondo(),
			'approver_ot' => $this->overtime->getAllApprovalGroups(),
			'approver_leave' => $this->leave->getAllApprovalGroups(),
			'approver_obt' => $this->obt->getAllApprovalGroups(),
			'approver_cws' => $this->shifts->getAllCWSApprovalGroups(),
			'approval_groups' => $this->shifts->getAllApprovalGroups(), 
			'report_status' => $this->report_status
		));
	}
	
	public function expat()	{
		$this->view_template('employees_expat', lang('employees_title'), array(
			'breadcrumbs' => array(lang('employees_expatriates')),
			'js' => array(
					'employees.expat.js'
				)
		));
	}
	
    private function array_remove_empty($haystack) {
		foreach ($haystack as $key => $value) {
			if (is_array($value)) {
				$haystack[$key] = $this->array_remove_empty($haystack[$key]);
			}
	
			if (empty($haystack[$key])) {
				unset($haystack[$key]);
			}
		}
	
		return $haystack;
	}
	
	/**
	 * Get HR KPI Data
	 * @param unknown $from
	 * @param unknown $to
	 * @param unknown $id
	 */
	public function hr_kpi_data($from, $to, $id = null){
		
		$res = array();
		$total = 0;
		$_hr = 0;
		$_hrmis = 0;
		
// 		if ($id && $this->session->userdata('mb_deptno') != '24')
// 		redirect('employees/profile', 'refresh');
		
		$id = $id ? $id : $this->session->userdata('mb_no');
		
		$user_info = $this->get($id, false);
		
		if ($user_info['mb_commencement']) {
		
			list($c_year, $c_month, $c_day) = explode('-', $user_info['mb_commencement']);
		
			$user_info['mb_commencement'] = mktime(0, 0, 0, $c_month, $c_day, $c_year);
		}
		
		$_uid = $this->employees->_getById($user_info['mb_id'])->hr_users_id;
		
		$averageHrScore= $this->kpi->getHrScoreAvgOnCutOff($id, $from, $to);
		$averageDeptScore = $this->kpi->getDeptScoreAvgOnCutOff($_uid, $from, $to);
		$averageHrmisScore = $this->kpi->getHrmisScoreAvgOnCutOff($user_info['mb_id'], $this->base_hrmis_score, $from, $to);
		
		if (count($averageHrScore))
			$_hr = floatval(number_formAt(floatval($averageHrScore[0]->avgScore), 2)) + 0;
		
		if (count($averageDeptScore))
			$_dept = floatval(number_format(floatval($averageDeptScore[0]->avgScore), 2)) + 0;
		
		if (count($averageHrmisScore))
			$_hrmis = floatval(number_format(floatval($averageHrmisScore[0]->avgScore), 2)) + 0;
		
		$total = $_hr + $_dept + $_hrmis;
		
		array_push($res, $_hr, $_dept, $_hrmis, $total);
		
		print json_encode(array('data' => $res));
	}
	
	public function profile($id = null) {
	
    	
		$curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));
        $curr_year = $curr_date->format("Y");
		
        /* First Cut Off */
        $coDateA = "$curr_year-05-01";
        $toDateA = date("Y-m-t", strtotime($coDateA));
		$prev_year = $curr_date->modify("-1 year")->format("Y");
        $fromDateA = "$prev_year-12-01";
        
        /* Second Cut Off */
        $coDateB = "$curr_year-11-01";
        $toDateB = date("Y-m-t", strtotime($coDateB));
        $fromDateB = "$curr_year-06-01";
		
        if ($id && $this->session->userdata('mb_deptno') != '24')
			redirect('employees/profile', 'refresh');
		
		$_id = $id ? $id : $this->session->userdata('user_id');
		$id = $id ? $id : $this->session->userdata('mb_no');
		
		$user_info = $this->get($id, false);
		
        if ($user_info['mb_commencement']) {
				
			list($c_year, $c_month, $c_day) = explode('-', $user_info['mb_commencement']);
				
			$user_info['mb_commencement'] = mktime(0, 0, 0, $c_month, $c_day, $c_year);
		}
		
		$_uid = $this->employees->_getById($user_info['mb_id'])->hr_users_id;

        $averageHrScoreA= $this->kpi->getHrScoreAvgOnCutOff($id, $fromDateA, $toDateA);
        $averageDeptScoreA = $this->kpi->getDeptScoreAvgOnCutOff($_uid, $fromDateA, $toDateA);
        $averageHrisScoreA = $this->kpi->getHrmisScoreAvgOnCutOff($user_info['mb_id'], $this->base_hrmis_score, $fromDateA, $toDateA);
        
        $averageHrScoreB= $this->kpi->getHrScoreAvgOnCutOff($id, $fromDateB, $toDateB);
        $averageDeptScoreB = $this->kpi->getDeptScoreAvgOnCutOff($_uid, $fromDateB, $toDateB);
        $averageHrisScoreB = $this->kpi->getHrmisScoreAvgOnCutOff($user_info['mb_id'], $this->base_hrmis_score, $fromDateB, $toDateB);
		
		$_user = $this->employees->_getById($user_info['mb_id']);
		










		$_total = 0;
		$_hr = 0;
		$_dept = 0;
		$_hrmis = 0;
		
		//$curr_date2 = new DateTime('2015-05-01', new DateTimeZone('Asia/Manila'));
		
		$_total2 = null;
		$_hr2 = null;
		$_dept2 = null;
		$_hrmis2 = null;
		
        if (count($averageHrScoreA))
            $_hr = floatval(number_formAt(floatval($averageHrScoreA[0]->avgScore), 2)) + 0;

        if (count($averageDeptScoreA))
            $_dept = floatval(number_format(floatval($averageDeptScoreA[0]->avgScore), 2)) + 0;

        if (count($averageHrisScoreA))
            $_hrmis = floatval(number_format(floatval($averageHrisScoreA[0]->avgScore), 2)) + 0;

        if (count($averageHrScoreB))
            $_hr2 = floatval(number_formAt(floatval($averageHrScoreB[0]->avgScore), 2)) + 0;
	
        if (count($averageDeptScoreB))
        	$_dept2 = floatval(number_format(floatval($averageDeptScoreB[0]->avgScore), 2)) + 0;
		
        if (count($averageHrisScoreB))
        	$_hrmis2 = floatval(number_format(floatval($averageHrisScoreB[0]->avgScore), 2)) + 0;

		
		$_total = $_hr + $_dept + $_hrmis;
        $_total2 = $_hr2 + $_dept2 + $_hrmis2;
		
        if (!is_null($_hr2) && !is_null($_dept2) && !is_null($_hrmis2))
			$_total2 = $_hr2 + $_dept2 + $_hrmis2;

		$this->view_template('employees_profile', lang('employees_title'), array(
			'breadcrumbs' => array(lang('employees_user_profile')),
			'css' => array(
					'jquery.qtip.min.css'	
				),
			'js' => array(
					'imagesloaded.pkg.min.js',
					'jquery.qtip.min.js',
					'jquery.maskedinput.min.js',
					'employees.profile.js'
				),
			'user_info' => $user_info,
			'logged_in' => $this->employees->logged_in($_user->hr_users_id),
			'logged_status' => ($this->break->onbreakstatus($id)?'On Break':($this->employees->logged_in($_user->hr_users_id)?'Online':'Offline')),
			'user_shift' => $this->shifts->getEmployeeSchedules('*', "tms.mb_no = $id and tms.year = " . $curr_date->format('Y') . ' and tms.month = ' . $curr_date->format('n') . ' and tms.day = ' . $curr_date->format('j')),
			'last_online' => $this->employees->last_online($_user->hr_users_id),
			'_user' => $this->ion_auth->user()->row(), // Temporary (must also allow HR to view other's profile)
			//'kpi' => isset($user_info['kpi'][$curr_date->format('Y')]) && isset($user_info['kpi'][$curr_date->format('Y')][$curr_date->format('n')]) ? $user_info['kpi'][$curr_date->format('Y')][$curr_date->format('n')] : $this->base_hr_score,
			//'violations' => $this->employees->getViolations($id),
			//'cites' => $this->employees->getCites($id)
			'_total' => $_total,
			'_hr' => $_hr,
			'_dept' => $_dept,
			'_hrmis' => $_hrmis,
			'_total2' => $_total2,
			'_hr2' => $_hr2,
			'_dept2' => $_dept2,
			'_hrmis2' => $_hrmis2,
			'__id' => $id,
			'_curr_year' => $curr_year,	//Setting the display of 
			'_prev_year' => $prev_year
		));
	}
	
	public function get($id, $json = true) {
	
		$emp = $this->employees->get($id);
		
		if(is_array($emp) && count($emp)) {
			
			unset($emp['mb_password']);
		
			$emp['kpi'] = $this->_getKPI($emp['mb_no']);
            $emp['reports'] = $this->employees->get_report_modules(array("a.mb_no" => $emp['mb_no'], "a.status" => '1'));
            if (count($emp['reports']) > 0) {
                foreach ($emp['reports'] as &$row) {
					$row->report_name = $this->report_status[$row->report_id]['label']; //also set the $return    
				}
			 }
			 
			//$emp['report_list'] = $this->report_status; 
			// $emp['groups'] = $this->ion_auth->get_users_groups($this->employees->_getById($emp['mb_id'])->hr_users_id)->result();
		}
		
		if($json)
			print json_encode($emp);
		else
			return $emp;
	}
	
    public function getAll($_show_inactive = false, $nationality = 0, $dept = 0, $publish = true) {
	
		$show_inactive = $this->input->get('show_inactive', true) || $_show_inactive;
		$filters = $this->input->get('filters', true);
		
		if(is_array($filters))
			$filters = implode(',', $filters);
		else
			$filters = 'mb_id, mb_lname, mb_fname, dept_name, mb_status, mb_no, mb_3, mb_nick';
	
		$employees = array();
		$res = array();
	
		switch($nationality) {
			case 0:
			
				$res = $this->employees->getAll($show_inactive, $filters, $dept);
			
				break;
			case 1:
			
				$res = $this->employees->getAllExpats($show_inactive, $filters, $dept);
			
				break;
			case 2:
			
				$res = $this->employees->getAllLocals($show_inactive, $filters, $dept);
			
				break;
		}
		foreach($res as $r)
			$employees[] = array_values((array) $r);

        if ($publish)
		print json_encode(array('data' => $employees));
        else
            return $employees;
	}
	
	public function getAll2($_show_inactive = false, $nationality = 0, $dept = 0) {
	
		$show_inactive = $this->input->get('show_inactive', true) || $_show_inactive;
		$filters = $this->input->get('filters', true);
		
		if(is_array($filters))
			$filters = implode(',', $filters);
		else
			$filters = 'mb_id, mb_lname, mb_fname, dept_name, mb_status, mb_no, mb_3, mb_nick';
	
		$employees = array();
		$res = array();
	
		switch($nationality) {
			case 0:
			
				$res = $this->employees->getAll2($show_inactive, $filters, $dept);
			
				break;
			case 1:
			
				$res = $this->employees->getAllExpats($show_inactive, $filters, $dept);
			
				break;
			case 2:
			
				$res = $this->employees->getAllLocals($show_inactive, $filters, $dept);
			
				break;
		}
		
		foreach($res as $r)
			$employees[] = array_values((array) $r);

		print json_encode(array('data' => $employees));
	}
	
	public function getAllExpats_extended($show_inactive = false, $dept = 0, $page = 1, $per_page = 25, $order_by = 'm.mb_lname') {
	
		$data = $this->employees->getAllExpats_extended($show_inactive, $dept, $page, $per_page, $order_by);
		
        $data['data'] = array_map(function ($a) {
            unset($a->mb_password);
            return array_values(get_object_vars($a));
        }, $data['data']);
        $data['page'] = $page;
        clearstatcache();
        echo json_encode($data);
    }

    public function getAllExpats_expired() {

        $data = $this->employees->getAllExpats_extended(false, 0, 1, 25, 'm.mb_lname', TRUE);

        $data['data'] = array_map(function ($a) {
            unset($a->mb_password);
            return array_values(get_object_vars($a));
        }, $data['data']);
        clearstatcache();
		echo json_encode($data);
	}
	
	public function getExpatNationalities() {
	
		echo json_encode($this->employees->getExpatNationalities($this->input->get('q', true)));
	}
	
	public function getJobTitles() {
	
		echo json_encode($this->employees->getJobTitles($this->input->get('q', true)));
	}
	
	public function tree() {
		
        if (in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(114, 229))) {
            $allow_search = true;
            $emp_list = $this->employees->getAll(true, "*", false, 0, 0, array("mb_lname" => "ASC"));
            $dept_list = $this->employees->getDepts();
        } else {
            if ($this->employees->isDeptHead()) {
                $depts = $this->employees->getDeptHeads(array("h.employee_id" => $this->session->userdata("user_id")));
                $dept_head_list = array();
                foreach ($depts as $dept) {
                    $dept_head_list[] = $dept->dept_no;
                }
                $emp_list = $this->employees->getAll(false, "*", false, 0, 0, array("mb_lname" => "ASC"), array("mb_deptno IN (" . implode(",", $dept_head_list) . ") AND " => 1));
                $dept_list = $this->employees->getDepts("dept_no IN (" . implode(",", $dept_head_list) . ")");
            } else {
                $emp_list = $this->employees->getAll(false, "*", false, 0, 0, array("mb_lname" => "ASC"), array("mb_no" => $this->session->userdata("mb_no")));
                $dept_list = $this->employees->getDepts("dept_no = '" . $this->session->userdata("mb_deptno") . "'");
            }
        }

		$this->view_template('employees_tree', lang('employees_title'), array(
            'breadcrumbs' => array(lang('employees_hierarchy'), lang('employees_tree')),
            'depts' => $dept_list
		));
	}
	
	public function settings() {
		
		$this->view_template('employees_settings', lang('employees_title'), array(
			'breadcrumbs' => array(lang('employees_hierarchy'), lang('employees_settings')),
			'js' => array(
					'employees.settings.js'
				),
			'depts' => $this->employees->getDeptHeads()
		));
    }

    public function options() {

        $notification = Array(7 => "Per Week",
            15 => "Semi Month",
            30 => "Per Month",
            92 => "3 Months",
            182 => "6 Months",
            365 => "Yearly");

        $this->view_template('employees_options', lang('employees_title'), array(
            'breadcrumbs' => array(lang('employees_view_all'), lang('employees_settings')),
            'js' => array(
                'employees.options.js'
            ),
            'employees' => $this->getAll(0, 0, 24, 0),
            'notif' => $notification
        ));
    }

    public function insertdocuments() {
        $post = $this->input->post();
        $this->db->update('hr_documents_notification', $post);
        echo json_encode(Array('success' => $this->db->trans_status()));
    }

    public function setdocs() {
        echo json_encode($this->db->get('hr_documents_notification')->result());
    }

    public function kpi_department() {

        $base_score = $this->kpi->getBaseScore(2);

        $this->view_template('employees/kpi/department', lang('employees_title'), array(
            'breadcrumbs' => array('KPI', lang('employees_dept')),
            'js' => array(
                'moment.min.js',
                'jquery.handsontable.full.min.js',
                'ajaxq.js',
                'jquery.gritter.min.js',
                'date-time/bootstrap-datepicker.min.js',
                'employees.kpi.department.js'
            ),
            'depts' => $this->employees->getDeptHeads(),
            'base_score' => $base_score[0]->score
        ));
    }

    public function kpi_hr_records() {

        $base_score = $this->kpi->getBaseScore(3);

        $this->view_template('employees/kpi/hr_records', lang('employees_title'), array(
            'breadcrumbs' => array('KPI', lang('employees_hr_records')),
            'js' => array(
                'moment.min.js',
                'jquery.handsontable.full.min.js',
                'ajaxq.js',
                'date-time/bootstrap-datepicker.min.js',
                'employees.kpi.hr.js'
            ),
            'depts' => $this->employees->getDeptHeads(),
            'base_score' => $base_score[0]->score
        ));
    }

    public function kpi_settings() {

        $base_scores = $this->kpi->getBaseScore();
        $obj_kpi = array(
            array("fa-street-view", "HRMIS", "max_hrmis"),
            array("fa-users", "Department", "max_department"),
            array("fa-table", "HR Records", "max_hr_records")
        );

        $this->view_template('employees/kpi/settings', lang('employees_title'), array(
            'breadcrumbs' => array('KPI', lang('employees_settings')),
            'js' => array(
                'fuelux.spinner.min.js',
                'employees.kpi.settings.js',
                'jquery.gritter.min.js'
            ),
            'base_scores' => $base_scores,
            'obj_kpi' => $obj_kpi
        ));
    }

    public function kpi_hrmis() {

        $base_score = $this->kpi->getBaseScore(1);

        if (in_array($this->session->userdata("mb_deptno"), array(24)) || in_array($this->session->userdata("mb_no"), array(114, 229))) {
            $allow_search = true;
            $emp_list = $this->employees->getAll(true, "*", false, 0, 0, array("mb_lname" => "ASC"));
            $dept_list = $this->employees->getDepts();
        } else {
            if ($this->employees->isDeptHead()) {
                $depts = $this->employees->getDeptHeads(array("h.employee_id" => $this->session->userdata("user_id")));
                $dept_head_list = array();

                foreach ($depts as $dept) {
                    $dept_head_list[] = $dept->dept_no;
                }

                $emp_list = $this->employees->getAll(false, "*", false, 0, 0, array("mb_lname" => "ASC"), array("mb_deptno IN (" . implode(",", $dept_head_list) . ") AND " => 1));
                $dept_list = $this->employees->getDepts("dept_no IN (" . implode(",", $dept_head_list) . ")");
            } else {
                $emp_list = $this->employees->getAll(false, "*", false, 0, 0, array("mb_lname" => "ASC"), array("mb_no" => $this->session->userdata("mb_no")));
                $dept_list = $this->employees->getDepts("dept_no = '" . $this->session->userdata("mb_deptno") . "'");
            }
        }

        $this->view_template('employees/kpi/hrmis', lang('employees_title'), array(
            'breadcrumbs' => array('KPI', 'HRMIS'),
            'js' => array(
                'jquery.handsontable.full.min.js',
                'ajaxq.js',
                'jquery.gritter.min.js',
                'date-time/bootstrap-datepicker.min.js',
                'employees.hrmis.js'
            ),
            'depts' => $dept_list,
            'emp_list' => $emp_list,
            'base_score' => $base_score[0]->score
        ));
    }

    public function getEmpHRMIS() {

        $bgcolor = ['286090', 'F8F8F8', 'D8D8D8'];
        $dept_id = $this->input->post("department");
        $emp_status = $this->input->post("emp_status");
        $limit = $this->input->post("limit");
        $page = $this->input->post("page");
        $offset = ($page - 1) * $limit;
        $return_arr = array();
        $response_arr = array("ID", "Name", "Department", "Started Date", "Status");

        $year_set = intval($this->input->post("inclusive_year"));
        $nationality = $this->input->post("nationality");

        $where_arr = array();

        if ($nationality)
            $where_arr["mb_3"] = $nationality;

        if ($emp_status)
            $where_arr["mb_employment_status"] = $emp_status;


        $employees = $this->employees->getAll(false, "*", $dept_id, 0, 0, array(), $where_arr);
        $total_count = count($employees);

        $employees = $this->employees->getAll(false, "*", $dept_id, $offset, $limit, array(), $where_arr);
        $filter_count = count($employees);
        $bgColor = $bgcolor[0];
        foreach ($employees as $employee) {

            if ($employee->mb_commencement) {
                $tmp_date = new DateTime($year_set . "-01-01" . " 00:00:00");
                $mb_commencement = new DateTime($employee->mb_commencement . " 00:00:00");
                $emp_data = array(
                    "mb_id" => $employee->mb_id,
                    "mb_nick" => $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname),
                    "dept_name" => $employee->dept_name,
                    "commence_date" => $employee->mb_commencement,
                    "mb_employment_status" => ($employee->mb_employment_status == 1) ? "Probationary" : "Regular"
                );

                // Fix for not rendering gray on previous month from commenced month.
                $dayVal = intval($mb_commencement->format('d'));

//                if ($dayVal === 1) {
                    $mb_commencement->modify("-1 month");
//                }

                for ($i = 1; $i < 13; $i++) {

                    $monthHeader = $tmp_date->format("M-y");
                    $response_arr[] = $monthHeader;
                    $dateStarted = $mb_commencement->format("M-y");

                    $record = $this->kpi->getScore($employee->mb_id, $tmp_date->format("Ym"));

                    $bgColor = $bgcolor[0];

                    if (!$record) {
                        $score = "";
                    } else {
                        $score = $record[0]->score;
                        if ($record[0]->month_point == "0") {
                            $bgColor = $bgcolor[1];
                        }
                    }

                    if ($mb_commencement->format("Ym") > $tmp_date->format("Ym")) {
                        $bgColor = $bgcolor[2];
                    }

                    $emp_data[$monthHeader] = $score . "#" . $bgColor . "#";

                    if ($mb_commencement >= $tmp_date) {
                        $emp_data[$monthHeader] = "#" . $bgcolor[2];
                    }

                    $tmp_date->modify("+1 month");
                }

                $return_arr[] = $emp_data;
            }
        }

        echo json_encode(array("data" => $return_arr, "header" => $response_arr, "total_count" => $total_count, "bgcolor" => $bgColor, "page" => $page, "filter_count" => $filter_count));
    }

    public function getDepartmentScores() {

        $dept_id = $this->input->post("department");
        $limit = $this->input->post("limit");
        $return_arr = array();
        $data_header = array("ID", "Name", "Department", "Started Date", "Status");
        $page = $this->input->post("page");
        $offset = ($page - 1) * $limit;

        $nationality = $this->input->post("nationality");

        $where_arr = array();

        if ($nationality)
            $where_arr["mb_3"] = $nationality;


        $employees = $this->employees->getAll(false, "*", $dept_id, 0, 0, array(), $where_arr);
        $total_count = count($employees);

        $employees = $this->employees->getAll(false, "*", $dept_id, $offset, $limit, array(), $where_arr);
        $year_set = intval($this->input->post("inclusive_year"));

        foreach ($employees as $employee) {
            $cur_date = new DateTime();
            $tmp_date = new DateTime($year_set . "-01-01" . " 00:00:00");
            $mb_commencement = new DateTime($employee->mb_commencement . " 00:00:00");
            $dayVal = intval($mb_commencement->format('d'));
//            
//            if ($dayVal <> 1) {
//              $mb_commencement->modify("+1 month");
//            }
						//Getting the users_info detalls
            $user_info = $this->db->get_where('users_info', array('employee_id' => $employee->mb_id))->row();

            $emp_data = array(
                "mb_id" => $employee->mb_id,
                "mb_nick" => $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname),
                "dept_name" => $employee->dept_name,
                "commence_date" => $employee->mb_commencement,
                "mb_employment_status" => ($employee->mb_employment_status == 1) ? "Probationary" : "Regular"
            );

            for ($i = 1; $i < 13; $i++) {

                $cellType = 1;
                $score = '';
                $monthHeader = $tmp_date->format("M-y");
                $data_header[] = $monthHeader;
                $dateStarted = $mb_commencement->format("M-y");

                $record = $this->kpi->getDeptScore($user_info->hr_users_id, $tmp_date->format("m"), $tmp_date->format("Y"));

                if (!$record) {
                    $score = "";
                } else {
                    $score = $record[0]->score;
                }

                if ($mb_commencement->format("Ym") > $tmp_date->format("Ym")) {
                    $cellType = 0;
                }

                if ($cur_date->format("Ym") === $tmp_date->format("Ym")) {
                    $cellType = 2;
                }

                switch ($cellType) {
                    case "0":
                        $emp_data[$monthHeader] = "$score#D8D8D8";
                        break;
                    case "1":
                        $emp_data[$monthHeader] = "$score#F8F8F8";
                        break;
                    case "2":
                        $emp_data[$monthHeader] = "$score#E8F5E2";
                        break;
                    default:
                        $emp_data[$monthHeader] = "#F8F8F8";
                }

                $tmp_date->modify("+1 month");
            }

            $return_arr[] = $emp_data;
        }

        echo json_encode(array("data" => $return_arr, "total_count" => $total_count, "header" => $data_header));
    }

    public function getHrScores() {

        $dept_id = $this->input->post("department");
        $limit = $this->input->post("limit");
        $return_arr = array();
        $data_header = array("ID", "Name", "Department", "Started Date", "Status");
        $page = $this->input->post("page");
        $offset = ($page - 1) * $limit;
		
        $nationality = $this->input->post("nationality");

        $where_arr = array();

        if ($nationality)
            $where_arr["mb_3"] = $nationality;

        $employees = $this->employees->getAll(false, "*", $dept_id, 0, 0, array(), $where_arr);
        $total_count = count($employees);

        $employees = $this->employees->getAll(false, "*", $dept_id, $offset, $limit, array(), $where_arr);
        $year_set = intval($this->input->post("inclusive_year"));

        foreach ($employees as $employee) {
            $cur_date = new DateTime();
            $tmp_date = new DateTime($year_set . "-01-01" . " 00:00:00");
            $mb_commencement = new DateTime($employee->mb_commencement . " 00:00:00");
            $dayVal = intval($mb_commencement->format('d'));
//            
//            if ($dayVal <> 1) {
//              $mb_commencement->modify("+1 month");
//            }

            $emp_data = array(
                "mb_id" => $employee->mb_id,
                "mb_nick" => $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname),
                "dept_name" => $employee->dept_name,
                "commence_date" => $employee->mb_commencement,
                "mb_employment_status" => ($employee->mb_employment_status == 1) ? "Probationary" : "Regular"
            );

            for ($i = 1; $i < 13; $i++) {

                $cellType = 1;
                $score = '';
                $monthHeader = $tmp_date->format("M-y");
                $data_header[] = $monthHeader;
                $dateStarted = $mb_commencement->format("M-y");

                $record = $this->kpi->getHrScore($employee->mb_no, $tmp_date->format("Ym"));

                if (!$record) {
                    $score = "";
                } else {
                    $score = $record[0]->score;
                }

                if ($mb_commencement->format("Ym") > $tmp_date->format("Ym")) {
                    $cellType = 0;
                }

                if ($cur_date->format("Ym") === $tmp_date->format("Ym")) {
                    $cellType = 2;
                }

                switch ($cellType) {
                    case "0":
                        $emp_data[$monthHeader] = "$score#D8D8D8";
                        break;
                    case "1":
                        $emp_data[$monthHeader] = "$score#F8F8F8";
                        break;
                    case "2":
                        $emp_data[$monthHeader] = "$score#7c7c7c";
                        break;
                    default:
                        $emp_data[$monthHeader] = "#F8F8F8";
                }

                $tmp_date->modify("+1 month");
            }

            $return_arr[] = $emp_data;
        }

        echo json_encode(array("data" => $return_arr, "total_count" => $total_count, "header" => $data_header));
    }

    /**
     * Return Increment point Value
     * Calculation to decide increment value base on month commenced
     * and employee status
     *
     * @param type $emp_stat
     * @param type $emp_type
     * @return type
     */
    private function incrementPointValue($emp_stat, $emp_type) {
        return ($emp_type == 1) ? ($emp_stat == 0) ? 2 : 5 : 5;
    }

    public function getHrScoreData($nationality = 0, $dept = 0, $page = 1, $limit = 25, $YM) {

        $response_arr = array("ID", "Name", "Department", "Month", "Score");
        $offset = ($page - 1) * $limit;

        $curr_date = new DateTime("$YM-01 00:00:00");

        $return_arr = array();
        $where_arr = array();
        $date = explode("-", $YM);
        if ($nationality)
            $where_arr["mb_3"] = $nationality;

        $where_arr['extract(YEAR_MONTH from mb_commencement) <='] = $date[0] . $date[1];

        $employees = $this->employees->getAll(false, "*", $dept, 0, 0, array(), $where_arr);

        $total_count = count($employees);

        $employees = $this->employees->getAll(false, "*", $dept, $offset, $limit, array(), $where_arr);
        foreach ($employees as $employee) {

            $mb_commencement = new DateTime($employee->mb_commencement . " 00:00:00");
            $fromMonth = new DateTime($employee->mb_commencement . " 00:00:00");

            if ($employee->mb_commencement && ($curr_date->format('Ym') >= $fromMonth->format('Ym'))) {

                $data = $this->kpi->getHrScore($employee->mb_no, $curr_date->format("Ym"));
                $score = (!$data) ? "" : $data[0]->score;

//                do{
//                    
//                    $data = $this->kpi->getDeptScore($employee->mb_no, $fromMonth->format("Ym"));
//                    $score = (!$data) ? "" : $data[0]->score;
//                    
//                    if($fromMonth-format("Ym") > $curr_date->format("Ym")){
//                        break;
//                    }
//                    
//                    $fromMonth->modify("+1 month");
//
//                }while($curr_date->format("Ym") !== $fromMonth->format("Ym"));

                $deptHead = $this->employees->getDeptHeadId($employee->mb_deptno);

                $emp_data = array(
                    "mb_no" => $employee->mb_no,
                    "mb_id" => $employee->mb_id,
                    "mb_nick" => $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname),
                    "dept_name" => $employee->dept_name,
                    "dept_id" => $employee->mb_deptno,
//                    "head_id" => $deptHead[0]->employee_id,
                    "month" => $curr_date->format("M-y"),
                    'month_raw' => $curr_date->format("Y-m-d"),
                    "score" => $score
                );

                $return_arr[] = $emp_data;
            }
        }

        echo json_encode(array("data" => $return_arr, "header" => $response_arr, "total_count" => $total_count, "page" => $page));
    }

    public function getDeptScoreData($nationality = 0, $dept = 0, $page = 1, $limit = 25, $YM) {

        $response_arr = array("ID", "Name", "Department", "Month", "Score");
        $offset = ($page - 1) * $limit;

        $curr_date = new DateTime("$YM-01 00:00:00");

        $return_arr = array();
        $where_arr = array();
        $date = explode("-", $YM);
        if ($nationality)
            $where_arr["mb_3"] = $nationality;

        $where_arr['extract(YEAR_MONTH from mb_commencement) <='] = $date[0] . $date[1];
				$where_arr['mb_deptno !='] = '28'; // Omit Management Department Staff

        $employees = $this->employees->getAll(false, "*", $dept, 0, 0, array(), $where_arr);

        $total_count = count($employees);

        $employees = $this->employees->getAll(false, "*", $dept, $offset, $limit, array(), $where_arr);
        foreach ($employees as $employee) {

            $user_info = $this->db->get_where('users_info', array('employee_id' => $employee->mb_id))->row();
            
            $mb_commencement = new DateTime($employee->mb_commencement . " 00:00:00");
            $fromMonth = new DateTime($employee->mb_commencement . " 00:00:00");

            $data = $this->kpi->getDeptScore($user_info->hr_users_id, $curr_date->format("m"),  $curr_date->format("Y"));
						$deptHead = $this->employees->getDeptHeadId($employee->mb_deptno);

						/* Try-Catch for getting array object */
                $score = (!$data) ? "" : $data[0]->score;
						$head_id = (!$deptHead) ? "" : $deptHead[0]->employee_id;
						/* End Try-Catch */

                $emp_data = array(
                    "mb_no" => $user_info->hr_users_id,
                    "mb_id" => $employee->mb_id,
                    "mb_nick" => $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname),
                    "dept_name" => $employee->dept_name,
                    "dept_id" => $employee->mb_deptno,
                	"head_id" => $head_id,
                    "month" => $curr_date->format("M-y"),
                    'month_raw' => $curr_date->format("Y-m-d"),
                    "score" => $score
                );

                $return_arr[] = $emp_data;
        }

        echo json_encode(array("data" => $return_arr, "header" => $response_arr, "total_count" => $total_count, "page" => $page));
    }

    /**
     * Fetching score date points to insert score data
     * @param type $dept
     * @param type $nationality
     * @param type $emp_status
     * @param type $page
     * @param type $limit
     */
    public function getScoreData($dept, $nationality, $emp_status, $page, $limit = 25) {

        $response_arr = array("ID", "Name", "Department", "Status", "Commencement Date", "Month", "Score");
        $curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));
        $where_arr = array();
        $offset = ($page - 1) * $limit;
        $return_arr = array();

        if ($nationality)
            $where_arr["mb_3"] = $nationality;

        if ($emp_status)
            $where_arr["mb_employment_status"] = $emp_status;

        //$limit
        $employees = $this->employees->getAll(false, "*", $dept, 0, 0, array(), $where_arr);
        $total_count = count($employees);

        $employees = $this->employees->getAll(false, "*", $dept, $offset, $limit, array(), $where_arr);
        foreach ($employees as $employee) {

            if ($employee->mb_commencement) {

                $mb_commencement = new DateTime($employee->mb_commencement . " 00:00:00");
                $fromMonth = new DateTime($employee->mb_commencement . " 00:00:00");

                $mb_scoring_month = $mb_commencement;
                $status = ($employee->mb_employment_status == 1) ? 0 : 1;
                $type = ($employee->mb_3 == "Expat") ? 1 : 0;

                $i = $this->incrementPointValue($status, $type);
                $index = 0;

                do {
                    $d = $i;
                    if ($mb_scoring_month->format('Ym') > $curr_date->format('Ym')) {
                        //if($mb_commencement->format('m'))
                        if ($index == 0)
                            $d--;

                        $fromMonth->modify("-$d month");
                        break;
                    }

                    // Fix for the 1st Month
                    if ($index > 0)
                        $d ++;

                    $mb_scoring_month->modify("+$d month");
                    $record = $this->kpi->getScore($employee->mb_id, $mb_commencement->format("Ym"));
                    $fromMonth->modify("+$d month");

                    if (!$record) {
                        $score = '';

                        // Fix for the 1st Month
                        if ($index > 0)
                            $d--;

                        $fromMonth->modify("-$d month");
                    }else {
                        $score = $record[0]->score;
                    }
                    $index ++;
                } while ($score);

                $data_status = "<span class='label label-success arrowed'>Hey!</span>";

                $emp_data = array(
                    "mb_no" => $employee->mb_no,
                    "mb_id" => $employee->mb_id,
                    "mb_nick" => $employee->mb_lname . ", " . ($employee->mb_3 == "Expat" ? $employee->mb_nick : $employee->mb_fname),
                    "dept_name" => $employee->dept_name,
                    "mb_employment_status" => ($status == 0) ? "Probationary" : "<b>Regular</b>",
                    "commence_date" => $employee->mb_commencement,
                    "month_raw" => $mb_scoring_month->format("Y-m-d"),
                	"scoring_month" => $mb_scoring_month->format("F Y"),
                    "month" => $fromMonth->format("M-y") . ' to ' . $mb_scoring_month->format("M-y"),
                    "score" => $score,
                    "incrementer" => $i,
                    "status" => $data_status
                );

                $return_arr[] = $emp_data;
            }
        }

        echo json_encode(array("data" => $return_arr, "header" => $response_arr, "total_count" => $total_count, "page" => $page));
	}
	
	public function violations($mode) {
		$success = false;
		$count = 0;
		
		if(in_array($mode, array('add', 'edit'))) {
			$success = $this->violations->add(2, $this->input->post(null, true));
			$count = $this->cite->getPendingCount();
		}
		print json_encode(array('success' => $success, 'count' => $count));
	}
	
	public function kpi() {
		// if($this->employees->getKPI(1))
			// print 'a';
		// else
			// $this->output->set_status_header(403);
		print json_encode($this->employees->getKPI('department'));
	}
	
	public function avatar($id) {
	
		$this->output->set_content_type('image/jpeg');
		$emp = $this->employees->get($id);
		if(is_array($emp) && count($emp))
			echo file_get_contents('assets/avatars/default-avatar-' . ($emp['mb_sex'] == 'F' ? 'fe' : '') . 'male.jpg');
		else
			echo file_get_contents('assets/avatars/default-avatar-male.jpg');
	}
	
	private function upload_image($id, $data) {

		$maxWidth = $maxHeight = 150;
		
		$uri = base64_decode(str_replace(' ', '+', substr($data, strpos($data, ',') + 1)));
		
		$info = getimagesizefromstring($uri);
		
		list($origWidth, $origHeight) = $info;

		$image = imagecreatefromstring($uri);
		
		if($origWidth > $maxWidth || $origHeight > $maxHeight) {
		
            if ($origWidth > $origHeight) {
				// target image is landscape/wide (ex: 4x3)
				$newWidth = $maxWidth;
				$ratio = $maxWidth / $origWidth;
				$newHeight = floor($origHeight * $ratio);
				// make sure the image wasn't heigher than expected
                if ($newHeight > $maxHeight) {
					// it is so limit by the height
					$newHeight = $maxHeight;
					$ratio = $maxHeight / $origHeight;
					$newWidth = floor($origWidth * $ratio);
				}
            } else {
				// target image is portrait/tall (ex: 3x4)
				$newHeight = $maxHeight;
				$ratio = $maxHeight / $origHeight;
				$newWidth = floor($origWidth * $ratio);
				// make sure the image wasn't wider than expected
                if ($newWidth > $maxWidth) {
					// it is so limit by the width
					$newWidth = $maxWidth;
					$ratio = $maxWidth / $origWidth;
					$newHeight = floor($origHeight * $ratio);
				}
			}
        } else {
			$newWidth = $origWidth;
			$newHeight = $origHeight;
		}
		
		$newImage = imagecreatetruecolor($newWidth, $newHeight);
		
		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
		
		imagejpeg($newImage, FCPATH . "assets\uploads\avatars\\$id.jpg", 65);
		
		imagedestroy($image);
		imagedestroy($newImage);
	}
	
	public function create() {

		$ret = false;

		$post = $this->input->post(null, true);
		
		$keys = array_keys($post);
		
		if($post && !in_array('mb_id', $keys) && !in_array('mb_username', $keys) && !in_array('mb_email', $keys) && !in_array('mb_password', $keys))
			print json_encode(array('success' => false, 'error' => 1));
		else {
		
			if($post['mb_password'] == '')
				$post['mb_password'] = 'Welcome1';
			
			$new_id = $this->ion_auth->register($post['mb_username'], $post['mb_password'], $post['mb_email']);
			
			if($new_id) {
			
				// $group_id = intval($post['group_id']);
				// if($group_id)
					// $this->ion_auth->add_to_group($group_id, $new_id);
				// unset($post['group_id']);
			
				if(in_array('photo', $keys)) {
					$this->upload_image($post['mb_id'], $this->input->post('photo'));
					
					unset($post['photo']);
				}
			}
		
			print json_encode(array('success' => $new_id && $this->employees->create($new_id, $post), 'error' => 2, 'details' => $this->ion_auth->errors()));
		}
	}
	
	public function update() {

		$ret = false;
	
		$post = $this->input->post(null, true);
		 
		if($post && in_array('mb_no', $post))
			print json_encode(array('success' => false, 'error' => 1));
		else {
			
			// $password = $post['password'];
			$id = $post['mb_no'];
            $access_arr['emp_reports'] = (isset($post['emp_reports'])) ? $post['emp_reports'] : array();
            $access_arr['emp_access_depts'] = (isset($post['emp_reports'])) ? $post['emp_access_depts'] : array();
		
			// $_groups = array();
			// $groups = $this->ion_auth->groups()->result();
			// foreach($groups as $group)
				// if(in_array($group->name, array('approver_ot', 'approver_leave')))
					// $_groups[] = (string)$group->id;
			// $_id = $this->employees->_getById($post['mb_id'])->hr_users_id;
			// $this->ion_auth->remove_from_group($_groups, $_id);
			// $group_id = intval($post['group_id']);

			if(isset($post['photo']))
				$this->upload_image($id, $this->input->post('photo'));

			unset($post['mb_no'], $post['group_id'], $post['photo']); 
			unset($post['emp_reports'], $post['emp_access_depts']); 
			
			$group_updated = true;
			  
			// if($group_id)
				// $group_updated = $this->ion_auth->add_to_group($group_id, $_id);
			// if($this->employees->check_valid($this->session->userdata('mb_no'), $password)) 
				$success = $this->employees->update($id, $post, count($post) == 1 && isset($post['mb_password']));  
				
				//update access report 
				$access_reports = array(); 
				$current_date = date("Y-m-d H:i:s");
            if ($success) {
					//access reports 
					if(is_array($id)) {
						
						foreach($id as $_id)
							$del_reports = $this->employees->delete_access_report("tk_access_report",array("status"=>'0'),array("mb_no"=>$_id));
					}
					else
						$del_reports = $this->employees->delete_access_report("tk_access_report",array("status"=>'0'),array("mb_no"=>$id));
					
					$access_reports = array(""); 
					if(!empty($access_arr['emp_reports'])) {  
						$x = 0; 
                    foreach ($access_arr['emp_reports'] as $rep => $report) {
                        if (trim($report) && trim($access_arr['emp_access_depts'][$rep])) {
                            if (count($this->employees->get_report_modules(array("a.report_id" => trim($report), "a.mb_no" => $id, "a.dept_no" => trim($access_arr['emp_access_depts'][$rep]))))) {

                                $this->employees->delete_access_report("tk_access_report", array("status" => '1'), array("report_id" => trim($report),
                                    "mb_no" => $id,
                                    "dept_no" => trim($access_arr['emp_access_depts'][$rep])));
                            } else {
                                $rows[$x] = array("report_id" => trim($report),
                                    "mb_no" => $id,
                                    "dept_no" => trim($access_arr['emp_access_depts'][$rep]),
                                    "created_by" => $this->session->userdata('mb_no'),
                                    "created_datetime" => $current_date,
                                    "updated_by" => $this->session->userdata('mb_no'),
                                    "updated_datetime" => $current_date,
                                    "status" => '1'
												  ); 
                                                            }
								$x++; 
							 }
						}
						
                    if (isset($rows))
                        $count_access = $this->employees->batch_insert("tk_access_report", $rows);
					 }
				 } 
				 
				print json_encode(array('success' => $success && $group_updated, 'error' => 2, 'details' => $this->ion_auth->errors()));
			// else
				// print json_encode(array('success' => false, 'error' => 3));
		}
	}
	
	public function updateExpat() {
		
		echo json_encode(array('updated' => $this->employees->updateExpat($this->input->post('changes', true))));
	}
	
    /**
     * Update HRMIS Score Function
     */
    public function updateScores() {
        echo json_encode(array('score' => $this->kpi->updateScores($this->input->post('changes', true))));
    }

    public function updateDeptKpiScore() {
        echo json_encode(array('score' => $this->kpi->updateDeptKpiScore($this->input->post('changes', true))));
    }

    public function updateHrKpiScore() {
        echo json_encode(array('score' => $this->kpi->updateHrKpiScore($this->input->post('changes', true))));
    }

    public function updateKpiPercentage() {

        $hrmisKpi = $this->input->post('hrmisKpi');
        $departmentKpi = $this->input->post('departmentKpi');
        $hrRecordKpi = $this->input->post('hrRecordKpi');
        $this->kpi->updatePercentage($hrmisKpi, $departmentKpi, $hrRecordKpi);
    }

	public function getDepts() {
		
		print json_encode($this->employees->getDepts());
	}
	
    /**
     * 
     * @param type $mb_no
     * @param type $year
     * @param type $month
     */
    public function updateHRScorePoint($mb_no, $year, $month) {
        $score = $this->_getKPI($mb_no);
        $base_score = $this->kpi->getBaseScore(3);
        $fScore = $score[$year][$month];

        $scoreDate = "$year-$month-" . date('d');
        $this->kpi->insertHrKPIScore($mb_no, $scoreDate, $fScore, '', '', $base_score[0]->score);
    }

    public function getDeptHeadID($dept_id) {
        print json_encode($this->employees->getDeptHeadId($dept_id));
    }

	public function getKPI($id = 0, $dept_average = false) {
	
		if($dept_average) {
		
			$curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));
		
			$depts = array();
			
			foreach($this->employees->getDepts() as $dept) {
			
				$depts[$dept->dept_name] = array();
				
				foreach($this->employees->getAll(false, 'mb_no', $dept->dept_no) as $emp) {
				
					$kpi = $this->_getKPI($emp->mb_no);
					
					for($i = $curr_date->format('n'); $i > 0; $i--) {
						
						if(!isset($depts[$dept->dept_name][$i]))
							$depts[$dept->dept_name][$i] = array();

						$depts[$dept->dept_name][$i][] = isset($kpi[$curr_date->format('Y')][$i]) ? $kpi[$curr_date->format('Y')][$i] : $this->base_hr_score;
					}
				}
				foreach(array_keys($depts[$dept->dept_name]) as $_m)
					$depts[$dept->dept_name][$_m] = round(array_sum($depts[$dept->dept_name][$_m]) / count($depts[$dept->dept_name][$_m]), 2);

				// $depts[$dept->dept_no] = count($depts[$dept->dept_no]) ? round(array_sum($depts[$dept->dept_no]) / count($depts[$dept->dept_no]), 2) : $this->base_hr_score;
			}

			echo json_encode($depts);
        } else
			echo json_encode($this->_getKPI($id));
	}
	
	private function _getKPI($id) {
	
		$curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));
		
		$violations = $this->employees->getViolations($id);
		
		$scores = array();
		
		$vio_occurence = 0;
		
		foreach($violations as $v_index => $violation) {
			
			if(@strtolower($violation->pen_desc) != 'waived') {
			
				$com_date = new DateTime($violation->commission_date, new DateTimeZone('Asia/Manila'));
				$matrix = explode(',', $violation->kpi_matrix);
				$p_period = explode(',', $violation->prescriptive_period);
				
				if(!isset($scores[$com_date_year = $com_date->format('Y')]))
					$scores[$com_date_year] = array();
				
				if(!isset($scores[$com_date_year][$com_date_month = $com_date->format('n')]))
					$scores[$com_date_year][$com_date_month] = array();
	
				if(!isset($scores[$com_date_year][$com_date_month][$violation_id = $violation->vio_id]))
					$scores[$com_date_year][$com_date_month][$violation_id] = array();
	
				if(!$violation->no_dismissal) {
					
					if(isset($violations[$v_index - 1])) {
							
						$prev_vio = $violations[$v_index - 1];
					
						$_com_date = new DateTime($prev_vio->commission_date, new DateTimeZone('Asia/Manila'));
					
						$_diff = $com_date->diff($_com_date);
						
						$cond = false;
						$in_choices = in_array($p_period[1], array(1, 2));
						
						switch($p_period[1]) {
							case 1:
								
								$cond = $_diff->d;
								
								break;
							case 2:
								
								$cond = $_diff->d && ($p_period[2] < 12 ? $_com_date->format('n') > $p_period[2] : true);
	
								break;
						}
	
						if($prev_vio->vio_id == $violation->vio_id && $_diff->y >= $p_period[0] && $cond && $in_choices)
							$vio_occurence = 1;
						elseif($prev_vio->vio_id == $violation->vio_id && $in_choices)
							$vio_occurence += 1;
						elseif($in_choices)
							$vio_occurence = 1;
                    } else
						$vio_occurence = 1;
				}
	
				@$scores[$com_date_year][$com_date_month][$violation_id][] = $violation->initial ? $violation->initial : ($violation->no_dismissal ? $matrix[0] : $matrix[$vio_occurence - 1]);
			}
		}

		foreach($scores as $year => $v)
			foreach($v as $month => $v2) {
				
				foreach($v2 as $vid => $v3)
					$scores[$year][$month][$vid] = in_array('D', $scores[$year][$month][$vid]) ? $this->base_hr_score : array_sum($scores[$year][$month][$vid]);

				$scores[$year][$month] = max($this->base_hr_score - array_sum($scores[$year][$month]), 0);
			}

		return $scores;
	}
	
        public function insertDeptKpiScore(){
            $subordinates = $this->input->post('subordinates');
			$head_id = $this->input->post('subordinates');
            
            foreach($subordinates as $mb_no => $score){

						// $emp = $this->employees->getById($mb_no);
            //$month = date('Y') . "-" . date('m') . "-01 00:00:00";
            $this->kpi->insertKPIScore($mb_no, date('Y'), date('m'), $score, $this->session->userdata('mb_deptno'), $this->session->userdata('user_id'));
            }
        }

	public function qqq($id) {
// 		$start = microtime(true);
		print_r($this->_getKPI($id));
// 		print_r($this->employees->getViolations($id));
// 		var_dump($this->employees->getKPI('employee', $id));
// 		echo microtime(true) - $start;
	}
	
	public function getRegularables() {
	
		$employees = array();
	
		$res = $this->employees->getRegularables();
		
		foreach($res as $r)
			$employees[] = array_values((array) $r);
		
		echo json_encode(array('data' => $employees));
	}
	
	public function getRegularablesCount() {
		
		echo json_encode(array('count' => $this->employees->getRegularablesCount()));
	}
	
	public function updateDeptHead() {
	
		$post = $this->input->post(null, true);
		
		echo json_encode(array('success' => $this->employees->updateDeptHead($post['dept_id'], $post['emp_id'])));
	}
	
	public function getSubordinatesScores() {
	
		$id = $this->input->post('id', true);
		
		$id = $id === false ? 0 : intval($id, 10);
		
		echo json_encode($this->employees->getSubordinates($id));
	}
	
	public function test() {
	
		$dt = new DateTime();

		$r = new $this->when();
		$r->startDate($dt->setDate($dt->format('Y'), 7, 0)->setTime(0, 0))
		  ->freq("yearly")
		  ->count(5)
		  ->bymonth(6)
		  ->bymonthday($r->startDate->format('d'))
		  ->generateOccurrences();

		print_r($r->occurrences);
		
		var_dump($r->occursAt($dt->setDate($dt->format('Y'), 7, 0)->setTime(0, 0)));
	}
	
    public function hr_history($id, $type, $fromDate = "201412", $toDate = "201505", $month = 0) {
		
		$ret = '';
		
		switch($type) {
			case 0:
				
// 				$vio = $this->violations->getAll(2, false, $id, $month);
// 				$ret = '<table class="table table-striped table-bordered table-hover">
// 							<tbody>';
// 				foreach($vio as $v) {
// 					$ret .= "		<tr>
// 										<td>{$v->description}</td>
// 										<td>{$v->commission_date}</td>
// 										<td>1</td>
// 									</tr>";
// 				}
// 				if(!count($vio))
// 					$ret .= '<tr>
// 								<td>
// 									<div class="alert alert-info">
// 										<strong>No details yet!</strong>
// 										Migration of records is still in progress.
// 										<br>
// 									</div>
// 								</td>
// 							</tr>';
// 				$ret .= '	</tbody>
// 						</table>';
				
                $hr = $this->kpi->getHrScoreByEmployee2($id, $fromDate , $toDate);
				
				if(count($hr)) {
						
					$ret = '<table class="table table-striped table-bordered table-hover">
								<tbody>';
						
					foreach($hr as $h) {
							
						$temp_date = new DateTime($h->added_date, new DateTimeZone('Asia/Manila'));
				
						$ret .= "	<tr>
										<td>{$temp_date->format('M Y')}</td>
										<td>{$h->score}</td>
										<td><a class=\"vio-month-details\" href=\"#\" data-ym=\"{$temp_date->format('Ym')}\"><i class=\"ace-icon fa fa-search\"></i></a></td>
									</tr>";
					}
				
					$ret .= '	</tbody>
							</table>';
                } else
					$ret = '<div class="alert alert-info">
								<strong>No details yet!</strong>
						
								Migration of records is still in progress.
								<br>
							</div>';
				
				break;
			case 1:
				
				$user_info = $this->get($id, false);
				
				$emp = $this->employees->_getById($user_info['mb_id']);

                $dept = $this->kpi->getDeptScoreByEmployee2($emp->hr_users_id, $fromDate , $toDate);
				
				if(count($dept)) {
					
					$ret = '<table class="table table-striped table-bordered table-hover">
								<tbody>';
					
					foreach($dept as $d) {
					
						$temp_date = new DateTime($d->added_date, new DateTimeZone('Asia/Manila'));
				
						$ret .= "<tr>
									<td>{$temp_date->format('M Y')}</td>
									<td>{$d->score}</td>
								</tr>";
					}
				
					$ret .= '	</tbody>
							</table>';
                } else
					$ret = '<div class="alert alert-info">
								<strong>No details yet!</strong>
						
								Migration of records is still in progress.
								<br>
							</div>';
			
				break;
			case 2:
				
				$user_info = $this->get($id, false);
				
                $hrmis = $this->kpi->getScoresByEmployee2($user_info['mb_id'], $fromDate , $toDate);
				
				if(count($hrmis)) {
					
					$ret = '<table class="table table-striped table-bordered table-hover">
								<tbody>';
					
					foreach($hrmis as $h) {
					
						$temp_date = new DateTime($h->create_ymd, new DateTimeZone('Asia/Manila'));
							
						$score = number_format((floatval($h->score) * floatval($this->base_hrmis_score)) / floatval($h->base_score), 2);
							
						$ret .= "	<tr>
										<td>{$temp_date->format('M Y')}</td>
										<td>{$score}</td>
									</tr>";
					}
					
					$ret .= '	</tbody>
							</table>';
                } else
					$ret = '<div class="alert alert-info">
								<strong>No details yet!</strong>
						
								Migration of records is still in progress.
								<br>
							</div>';
			
				break;
			case 3:
				
				$total = array();
				
                $hr = $this->kpi->getHrScoreByEmployee2($id, $fromDate , $toDate);
				
				$user_info = $this->get($id, false);
				
				$emp = $this->employees->_getById($user_info['mb_id']);
				
                $dept = $this->kpi->getDeptScoreByEmployee2($emp->hr_users_id, $fromDate , $toDate);
				
                $hrmis = $this->kpi->getScoresByEmployee2($user_info['mb_id'], $fromDate , $toDate);
				
				foreach($hr as $h) {
					
					$temp_date = new DateTime($h->added_date, new DateTimeZone('Asia/Manila'));
					
					$total[$temp_date->format('M Y')] = array('hr' => $h->score);
				}
				
				foreach($dept as $d) {
					
					$temp_date = new DateTime($d->added_date, new DateTimeZone('Asia/Manila'));
					
					if(isset($total[$temp_date->format('M Y')]))
						$total[$temp_date->format('M Y')]['dept'] = $d->score;
					else
						$total[$temp_date->format('M Y')] = array('dept' => $d->score);
				}
				
				foreach($hrmis as $h) {
						
					$temp_date = new DateTime($h->create_ymd, new DateTimeZone('Asia/Manila'));
						
					$score = floatval(number_format((floatval($h->score) * floatval($this->base_hrmis_score)) / floatval($h->base_score), 2));
						
					if(isset($total[$temp_date->format('M Y')]))
						$total[$temp_date->format('M Y')]['hrmis'] = $score;
					else
						$total[$temp_date->format('M Y')] = array('hrmis' => $score);
				}
				
				if(count($total)) {
					$ret = '<table class="table table-striped table-bordered table-hover">
								<tbody>';
					
					foreach($total as $i => $t)
						$ret .= '	<tr>
										<td>' . $i . '</td>
										<td>' . array_sum($t) . '</td>
									</tr>';
					
					$ret .= '	</tbody>
							</table>';
                } else
					$ret = '<div class="alert alert-info">
								<strong>No details yet!</strong>
					
								Migration of records is still in progress.
								<br>
							</div>';
			
				break;
		}
		
		echo $ret;
	}

public function mermertime() {
    
    $emps = $this->employees->getAll();
    
    $months = array(6, 7, 8, 9);
    
    foreach($emps as $emp) {
        
        foreach($months as $month) {
            
            $kpi = $this->_getKPI(intval($emp->mb_no, 10));
            
            $h = $this->employees->getDeptHeadId(intval($emp->mb_deptno, 10))[0]->employee_id;
            
            if($h)
                $this->kpi->insertHrKPIScore(intval($emp->mb_no, 10), "2015-0$month-01 00:00:00", isset($kpi[2015][$month]) ? $kpi[2015][$month] : 20, intval($emp->mb_deptno, 10), $h);
        }
    }
}
	
// 	public function import() {
// 		$this->load->helper('email');
// 		$emps = $this->employees->getAll(false, '*', 35);
// 		$asd = 0;
// 		foreach($emps as $emp) {
// 			if($id = $this->ion_auth->register($emp->mb_username, 'Welcome1', valid_email($emp->mb_email) ? $emp->mb_email : "{$emp->mb_username}@pacificseainvests.com"))
// 				if($this->employees->import($emp->mb_no, $id))
// 					$asd++;
// 		}
// 		echo count($emps) . ' ==== ' . $asd;
// 	}

    public function exportHRMISKpi() {
        $this->load->library('excel');
        $activeSheetInd = -1;
        $file_name = "HRMIS KPI Report.xls";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function exportDepartmentKpi() {
        $this->load->library('excel');
        $activeSheetInd = -1;
        $file_name = "Department KPI Report.xls";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function exportHrKpi() {
        $this->load->library('excel');
        $activeSheetInd = -1;
        $file_name = "HR Records KPI Report.xls";

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */