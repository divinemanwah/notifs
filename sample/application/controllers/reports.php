<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('employees_model', 'employees_m');
		$this->load->model('overtime_model', 'overtime_m');
		$this->load->model('leaves_model', 'leaves_m');
		$this->load->model('obt_model', 'obt_m');
		$this->load->model('shifts_model', 'shifts_m');
		$this->load->model('attendance_model', 'att_m');
	}
	
	/* Views */
	public function index() {
	  // redirect("/reports/leave");
	}
	
	public function leave_list() {
	  $date = new DateTime();
	  $period_dtl = (object) array();
	  $period_dtl->end = $date->format("Y-m-d");
	  $period_dtl->start = $date->modify("-7 days")->format("Y-m-d");
	  $emp_list = $this->employees_m->getAll(true,"*",false,0,0,array("mb_lname"=>"ASC"));
	  
	  $this->view_template('reports/leave_list', 'Reports', array(
			'breadcrumbs' => array('Filed Leave List'),
			'js' => array(
			          'jquery.handsontable.full.min.js',
					  'date-time/bootstrap-datepicker.min.js',
					  'reports.leave.js'
					),
			'cur_period' 	=> $period_dtl,
			'depts' 		=> $this->employees_m->getDepts(),
			'emp_list'		=> $emp_list
		));
	}
	
	public function overtime_list() {
	  $date = new DateTime();
	  $period_dtl = (object) array();
	  $period_dtl->end = $date->format("Y-m-d");
	  $period_dtl->start = $date->modify("-7 days")->format("Y-m-d");
	  $emp_list = $this->employees_m->getAll(true,"*",false,0,0,array("mb_lname"=>"ASC"));
	  
	  $this->view_template('reports/overtime_list', 'Reports', array(
			'breadcrumbs' => array('Filed Overtime List'),
			'js' => array(
			          'jquery.handsontable.full.min.js',
					  'date-time/bootstrap-datepicker.min.js',
					  'reports.overtime.js'
					),
			'cur_period' 	=> $period_dtl,
			'depts' 		=> $this->employees_m->getDepts(),
			'emp_list'		=> $emp_list
		));
	}
	
	public function obt_list() {
	  $date = new DateTime();
	  $period_dtl = (object) array();
	  $period_dtl->end = $date->format("Y-m-d");
	  $period_dtl->start = $date->modify("-7 days")->format("Y-m-d");
	  $emp_list = $this->employees_m->getAll(true,"*",false,0,0,array("mb_lname"=>"ASC"));
	  
	  $this->view_template('reports/obt_list', 'Reports', array(
			'breadcrumbs' => array('Filed OBT List'),
			'js' => array(
			          'jquery.handsontable.full.min.js',
					  'date-time/bootstrap-datepicker.min.js',
					  'reports.obt.js'
					),
			'cur_period' 	=> $period_dtl,
			'depts' 		=> $this->employees_m->getDepts(),
			'emp_list'		=> $emp_list
		));
	}
	
	public function cws_list() {
	  $date = new DateTime();
	  $period_dtl = (object) array();
	  $period_dtl->end = $date->format("Y-m-d");
	  $period_dtl->start = $date->modify("-7 days")->format("Y-m-d");
	  $emp_list = $this->employees_m->getAll(false,"*",false,0,0,array("mb_lname"=>"ASC"));
	  
	  $shifts_dtl = $this->shifts_m->getAll(false, "*, CONCAT(LPAD(shift_hr_from,2,'0'),':',LPAD(shift_min_from,2,'0'),'-',LPAD(shift_hr_to,2,'0'),':',LPAD(shift_min_to,2,'0')) shift_sched, shift_color");
	  
	  $shifts_dtl[] = (object) array("shift_id"=>0,"shift_code"=>"RD", "shift_sched"=>"Rest Day", "shift_color"=>"1B7935") ;
	  $shifts_dtl[] = (object) array("shift_id"=>-1,"shift_code"=>"SS", "shift_sched"=>"Suspension", "shift_color"=>"FA4747") ;
	  $shifts_dtl[] = (object) array("shift_id"=>-2,"shift_code"=>"PH", "shift_sched"=>"Holiday", "shift_color"=>"D87947") ;
	  $shifts_list = array();
	  foreach($shifts_dtl as $shift) {
	    $shifts_list[$shift->shift_id] = (object) array();
	    $shifts_list[$shift->shift_id]->scode = $shift->shift_code;
		$shifts_list[$shift->shift_id]->stime = $shift->shift_sched;
	  }
	  
	  $this->view_template('reports/cws_list', 'Reports', array(
			'breadcrumbs' => array('Filed Change of Working Schedule List'),
			'js' => array(
			          'jquery.handsontable.full.min.js',
					  'date-time/bootstrap-datepicker.min.js',
					  'reports.cws.js'
					),
			'cur_period' 	=> $period_dtl,
			'depts' 		=> $this->employees_m->getDepts(),
			'emp_list'		=> $emp_list,
			'shifts'		=> $shifts_list
		));
	}
	
	public function schedule_list() {
	  $date = new DateTime();
	  $period_dtl = (object) array();
	  $period_dtl->end = $date->format("Y-m-d");
	  $period_dtl->start = $date->modify("-7 days")->format("Y-m-d");
	  $emp_list = $this->employees_m->getAll(false,"*",false,0,0,array("mb_lname"=>"ASC"));
	  
	  $this->view_template('reports/sched_list', 'Reports', array(
			'breadcrumbs' => array('Schedule Uploads'),
			'js' => array(
			          'jquery.handsontable.full.min.js',
					  'date-time/bootstrap-datepicker.min.js',
					  'reports.sched.js'
					),
			'cur_period' 	=> $period_dtl,
			'depts' 		=> $this->employees_m->getDepts(),
			'emp_list'		=> $emp_list
		));
	}
	
	public function attendance_list() {
	  $date = new DateTime();
	  $date->modify("-1 days");
	  $period_dtl = (object) array();
	  $period_dtl->end = $date->format("Y-m-d");
	  $period_dtl->start = $date->modify("-7 days")->format("Y-m-d");  
	  
	  if ($this->session->userdata('mb_deptno') == 24  || management_access()) {
	    $emp_list = $this->employees_m->getAll(true,"*",false,0,0,array("mb_lname"=>"ASC"));
		$depts = $this->employees_m->getDepts();
	  }
	  else {
	    $reports = $this->session->userdata("reports");
	    if(isset($reports[1])) {
	      $user_depts = implode(',',$reports[1]);
		  $emp_list = $this->employees_m->getAll(true,"*",false,0,0,array("mb_lname"=>"ASC"), array("FIND_IN_SET(mb_deptno, '{$user_depts}') !="=>0));
		  $depts = $this->employees_m->getDepts(array("FIND_IN_SET(dept_no, '{$user_depts}') !="=>0));
		}
	  }
	  
	  $this->view_template('reports/attendance_list', 'Reports', array(
			'breadcrumbs' => array('Attendance Report'),
			'js' => array(
			          'jquery.handsontable.full.min.js',
					  'date-time/bootstrap-datepicker.min.js',
					  'jquery.validate.min.js',
					  'reports.attendance.js'
					),
			'cur_period' 	=> $period_dtl,
			'depts' 		=> $depts,
			'emp_list'		=> $emp_list
      ));
	}
	
	/* End of Views */
	
	/* Leave List */
	public function getEmpLeavesGrid() {
	  $post 	= $this->input->post();
	  $limit 	= $this->input->post("limit");
	  $page 	= $this->input->post("page");
	  $dept_id 	= $this->input->post("department");
	  $status 	= $this->input->post("status");
	  $mb_no 	= $this->input->post("emp");
	  $type 	= $this->input->post("type");
	  $offset 	= ($page - 1) * $limit;
	
	  $date_from = $this->input->post("dateFrom");
	  $date_from = new DateTime($date_from." 00:00:00");
	  
	  $date_to = $this->input->post("dateTo");
	  $date_to = new DateTime($date_to." 00:00:00");
	  $all_leaves_count = 0;
	  $having_str = "(( date_from BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."') OR (date_to BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."') OR ( '".$date_from->format("Y-m-d")."' BETWEEN date_from AND date_to) OR ( '".$date_to->format("Y-m-d")."' BETWEEN date_from AND date_to ))";
	  if(!empty($status)) {
	    $having_str .= " AND tla.status = '".($status=="-1"?0:$status)."'";
	  }
	  if(!empty($mb_no)) {
	    $having_str .= " AND tla.mb_no = '".$mb_no."'";
	    $data = $this->leaves_m->getEmpLeaveApplication("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  else if(!empty($dept_id)) {	    
	    $having_str .= " AND gm.mb_deptno = '".$dept_id."' AND gm.mb_status = 1";
	    $data = $this->leaves_m->getEmpLeaveApplication("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  else {
	    $having_str .= " AND gm.mb_status = 1";
	    $data = $this->leaves_m->getEmpLeaveApplication("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  
	  
	  $select_str = "*, DATE(tla.date_from) date_from, DATE(tla.date_to) date_to, CASE tla.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END lv_status_lbl, tla.status lv_status";
	  
	  $response_arr = array("Request ID", "Employee ID", "Name", "Dept", "Date From", "Date To", "Type","Allocated", "Used","For Approval","Status","Action");
	  $widths_arr = array(80,100,300,120,120,120,65,50,80,160,108);
	  
	  $data_all = $this->leaves_m->getEmpLeaveApplication($select_str, $having_str, $offset, $limit, array("lv_app_id"=>"Desc"));
	  
	  $return_arr = array();
	  foreach($data_all as $leave) {
	    $approver = "";
	    if(in_array($leave->lv_status,array(1))) {
	      $for_approval = $this->leaves_m->getAllForApprovalFiltered("tlaa.approved_level, tlaa.lv_apprv_grp_id, tla.lv_app_id", "lv_apprv_grp_id = '".$leave->lv_apprv_grp_id."' AND lv_app_id = '".$leave->lv_app_id."'");
		  if(count($for_approval)) {
		    $approvers = $this->leaves_m->getApprovalGroupApprover($leave->lv_apprv_grp_id,"GROUP_CONCAT(gm.mb_nick) approvers",array("level"=>$for_approval[0]->approved_level));
		    if(count($approvers)) {
		      $approver = $approvers[0]->approvers;
			}
		  }
		}
	    $return_arr[] = array(
						  "lv_app_id"	=> $leave->lv_app_id,
						  "mb_id"		=> $leave->mb_id,
						  "mb_name"		=> $leave->mb_lname.", ".($leave->mb_3 =="Expat"?$leave->mb_nick:$leave->mb_fname),
						  "dept_name"	=> $leave->dept_name,
						  "date_from"	=> $leave->date_from,
						  "date_to"		=> $leave->date_to,
						  "leave_code"	=> $leave->leave_code,
						  "allocated"	=> $leave->allocated,
						  "used"	=> $leave->used,
						  "approver"	=> $approver,
						  "status_lbl"	=> $leave->lv_status_lbl,
						  "action"		=> '<div class="action-buttons">'.
											  (in_array($leave->lv_status,array(1,2,3,4))?'<a class="green request-view" href="#" data-id="'.$leave->lv_app_id.'" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>':'').
											'</div>'
						);
	  }
	  echo json_encode(array("data"=>$return_arr, "header"=>$response_arr, "width"=>$widths_arr, "total_count"=>$all_leaves_count, "page" => $page));
	  
	}
	/* End of Leave List */
	
	/* Overtime List */
	public function getEmpOvertimeGrid() {
	  $post 	= $this->input->post();
	  $limit 	= $this->input->post("limit");
	  $page 	= $this->input->post("page");
	  $dept_id 	= $this->input->post("department");
	  $status 	= $this->input->post("status");
	  $mb_no 	= $this->input->post("emp");
	  $type 	= $this->input->post("type");
	  $offset 	= ($page - 1) * $limit;
	
	  $date_from = $this->input->post("dateFrom");
	  $date_from = new DateTime($date_from." 00:00:00");
	  
	  $date_to = $this->input->post("dateTo");
	  $date_to = new DateTime($date_to." 00:00:00");
	  $all_leaves_count = 0;
	  $having_str = "date BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."'";
	  if(!empty($status)) {
	    $having_str .= " AND tla.status = '".($status=="-1"?0:$status)."'";
	  }
	  if(!empty($mb_no)) {
	    $having_str .= " AND tla.mb_no = '".$mb_no."'";
	    $data = $this->overtime_m->getEmpOTApplication("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  else if(!empty($dept_id)) {	    
	    $having_str .= " AND gm.mb_deptno = '".$dept_id."' AND gm.mb_status = 1";
	    $data = $this->overtime_m->getEmpOTApplication("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  else {
	    $having_str .= " AND gm.mb_status = 1";
	    $data = $this->overtime_m->getEmpOTApplication("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  
	  $select_str = "*, DATE(tla.date) ot_date, CASE tla.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END ot_status_lbl, tla.status ot_status";
	  
	  $response_arr = array("Request ID", "Employee ID", "Name", "Dept", "Date", "Time In", "Time Out","For Approval","Status","Action");
	  $widths_arr = array(80,100,300,120,120,120,80,160,108);
	  
	  $data_all = $this->overtime_m->getEmpOTApplication($select_str, $having_str, $offset, $limit, array("ot_app_id"=>"Desc"));
	  
	  $return_arr = array();
	  foreach($data_all as $overtime) {
	    $approver = "";
	    if(in_array($overtime->ot_status,array(1))) {
	      $for_approval = $this->overtime_m->getAllForApprovalFiltered("tlaa.approved_level, tlaa.ot_apprv_grp_id, tla.ot_app_id", "ot_apprv_grp_id = '".$overtime->ot_apprv_grp_id."' AND ot_app_id = '".$overtime->ot_app_id."'");
		  if(count($for_approval)) {
		    $approvers = $this->overtime_m->getApprovalGroupApprover($overtime->ot_apprv_grp_id,"GROUP_CONCAT(gm.mb_nick) approvers",array("level"=>$for_approval[0]->approved_level));
		    if(count($approvers)) {
		      $approver = $approvers[0]->approvers;
			}
		  }
		}
	    $return_arr[] = array(
						  "ot_app_id"	=> $overtime->ot_app_id,
						  "mb_id"		=> $overtime->mb_id,
						  "mb_name"		=> $overtime->mb_lname.", ".($overtime->mb_3 =="Expat"?$overtime->mb_nick:$overtime->mb_fname),
						  "dept_name"	=> $overtime->dept_name,
						  "ot_date"		=> $overtime->ot_date,
						  "time_in"		=> $overtime->time_in,
						  "time_out"	=> $overtime->time_out,
						  "approver"	=> $approver,
						  "status_lbl"	=> $overtime->ot_status_lbl,
						  "action"		=> '<div class="action-buttons">'.
											  (in_array($overtime->ot_status,array(1,2,3,4))?'<a class="green request-view" href="#" data-id="'.$overtime->ot_app_id.'" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>':'').
											'</div>'
						);
	  }
	  echo json_encode(array("data"=>$return_arr, "header"=>$response_arr, "width"=>$widths_arr, "total_count"=>$all_leaves_count, "page" => $page));
	}
	/* End of Overtime List */
	
	/* OBT List */
	public function getEmpOBTGrid() {
	  $post 	= $this->input->post();
	  $limit 	= $this->input->post("limit");
	  $page 	= $this->input->post("page");
	  $dept_id 	= $this->input->post("department");
	  $status 	= $this->input->post("status");
	  $mb_no 	= $this->input->post("emp");
	  $type 	= $this->input->post("type");
	  $offset 	= ($page - 1) * $limit;
	
	  $date_from = $this->input->post("dateFrom");
	  $date_from = new DateTime($date_from." 00:00:00");
	  
	  $date_to = $this->input->post("dateTo");
	  $date_to = new DateTime($date_to." 00:00:00");
	  $all_leaves_count = 0;
	  $having_str = "date BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."'";
	  if(!empty($status)) {
	    $having_str .= " AND tla.status = '".($status=="-1"?0:$status)."'";
	  }
	  if(!empty($mb_no)) {
	    $having_str .= " AND tla.mb_no = '".$mb_no."'";
	    $data = $this->obt_m->getEmpOBTApplication("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  else if(!empty($dept_id)) {	    
	    $having_str .= " AND gm.mb_deptno = '".$dept_id."' AND gm.mb_status = 1";
	    $data = $this->obt_m->getEmpOBTApplication("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  else {
	    $having_str .= " AND gm.mb_status = 1";
	    $data = $this->obt_m->getEmpOBTApplication("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  
	   $select_str = "*, DATE(tla.date) obt_date, CASE tla.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END obt_status_lbl, tla.status obt_status";
	  
	  $response_arr = array("Request ID", "Employee ID", "Name", "Dept", "Date", "Time In", "Time Out","For Approval","Status","Action");
	  $widths_arr = array(80,100,300,120,120,120,80,160,108);
	  
	  $data_all = $this->obt_m->getEmpOBTApplication($select_str, $having_str, $offset, $limit, array("obt_app_id"=>"Desc"));
	  
	  $return_arr = array();
	  foreach($data_all as $obt) {
	    $approver = "";
	    if(in_array($obt->obt_status,array(1))) {
	      $for_approval = $this->obt_m->getAllForApprovalFiltered("tlaa.approved_level, tlaa.obt_apprv_grp_id, tla.obt_app_id", "obt_apprv_grp_id = '".$obt->obt_apprv_grp_id."' AND obt_app_id = '".$obt->obt_app_id."'");
		  if(count($for_approval)) {
		    $approvers = $this->obt_m->getApprovalGroupApprover($obt->obt_apprv_grp_id,"GROUP_CONCAT(gm.mb_nick) approvers",array("level"=>$for_approval[0]->approved_level));
		    if(count($approvers)) {
		      $approver = $approvers[0]->approvers;
			}
		  }
		}
	    $return_arr[] = array(
						  "obt_app_id"	=> $obt->obt_app_id,
						  "mb_id"		=> $obt->mb_id,
						  "mb_name"		=> $obt->mb_lname.", ".($obt->mb_3 =="Expat"?$obt->mb_nick:$obt->mb_fname),
						  "dept_name"	=> $obt->dept_name,
						  "obt_date"	=> $obt->obt_date,
						  "time_in"		=> $obt->time_in,
						  "time_out"	=> $obt->time_out,
						  "approver"	=> $approver,
						  "status_lbl"	=> $obt->obt_status_lbl,
						  "action"		=> '<div class="action-buttons">'.
											  (in_array($obt->obt_status,array(1,2,3,4))?'<a class="green request-view" href="#" data-id="'.$obt->obt_app_id.'" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>':'').
											'</div>'
						);
	  }
	  echo json_encode(array("data"=>$return_arr, "header"=>$response_arr, "width"=>$widths_arr, "total_count"=>$all_leaves_count, "page" => $page));
	}
	/* End of OBT List */
	
	/* CWS List */
	public function getEmpCWSGrid() {
	  $post 	= $this->input->post();
	  $limit 	= $this->input->post("limit");
	  $page 	= $this->input->post("page");
	  $dept_id 	= $this->input->post("department");
	  $status 	= $this->input->post("status");
	  $mb_no 	= $this->input->post("emp");
	  $type 	= $this->input->post("type");
	  $offset 	= ($page - 1) * $limit;
	
	  $date_from = $this->input->post("dateFrom");
	  $date_from = new DateTime($date_from." 00:00:00");
	  
	  $date_to = $this->input->post("dateTo");
	  $date_to = new DateTime($date_to." 00:00:00");
	  $all_leaves_count = 0;
	  $having_str = "(( att_date_from BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."') OR (att_date_to BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."') OR ( '".$date_from->format("Y-m-d")."' BETWEEN att_date_from AND att_date_to) OR ( '".$date_to->format("Y-m-d")."' BETWEEN att_date_from AND att_date_to ))";
	  if(!empty($status)) {
	    $having_str .= " AND tcsq.status = '".($status=="-1"?0:$status)."'";
	  }
	  if(!empty($mb_no)) {
	    $having_str .= " AND tcsq.mb_no = '".$mb_no."'";
	    $data = $this->shifts_m->getEmployeeChangeSchedules("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  else if(!empty($dept_id)) {	    
	    $having_str .= " AND gm.mb_deptno = '".$dept_id."' AND gm.mb_status = 1";
	    $data = $this->shifts_m->getEmployeeChangeSchedules("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  else {
	    $having_str .= " AND gm.mb_status = 1";
	    $data = $this->shifts_m->getEmployeeChangeSchedules("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  
	   $select_str = "*, CASE tcsq.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END cws_status_lbl, tcsq.status cws_status";
	  
	  $response_arr = array("Request ID", "Employee ID", "Name", "Dept", "Date From", "Date To", "For Approval","Status","Action");
	  $widths_arr = array(80,100,300,120,120,120,80,160,108);
	  
	  $data_all = $this->shifts_m->getEmployeeChangeSchedules($select_str, $having_str, $offset, $limit, array("cs_req_id"=>"Desc"));
	  
	  $return_arr = array();
	  foreach($data_all as $cws) {
	    $approver = "";
	    if(in_array($cws->cws_status,array(1))) {
	      $for_approval = $this->shifts_m->getAllForApprovalChangeShiftFiltered("tcsa.approved_level, tcsa.apprv_grp_id, tcsr.cs_req_id", "apprv_grp_id = '".$cws->apprv_grp_id."' AND cs_req_id = '".$cws->cs_req_id."'");
		  if(count($for_approval)) {
		    $approvers = $this->shifts_m->getCWSApprovalGroupApprover($cws->apprv_grp_id,"GROUP_CONCAT(gm.mb_nick) approvers",array("level"=>$for_approval[0]->approved_level));
		    if(count($approvers)) {
		      $approver = $approvers[0]->approvers;
			}
		  }
		}
	    $return_arr[] = array(
						  "cs_req_id"	=> $cws->cs_req_id,
						  "mb_id"		=> $cws->mb_id,
						  "mb_name"		=> $cws->mb_lname.", ".($cws->mb_3 =="Expat"?$cws->mb_nick:$cws->mb_fname),
						  "dept_name"	=> $cws->dept_name,
						  "date_from"	=> $cws->att_date_from,
						  "date_to"		=> $cws->att_date_to,
						  "approver"	=> $approver,
						  "status_lbl"	=> $cws->cws_status_lbl,
						  "action"		=> '<div class="action-buttons">'.
											  (in_array($cws->cws_status,array(1,2,3,4))?'<a class="green request-view" href="#" data-id="'.$cws->cs_req_id.'" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>':'').
											'</div>'
						);
	  }
	  echo json_encode(array("data"=>$return_arr, "header"=>$response_arr, "width"=>$widths_arr, "total_count"=>$all_leaves_count, "page" => $page));
	}
	/* End of OBT List */
	
	/* Attendance List */
	public function getAttendanceGrid() {
	  $post 	= $this->input->post();
	  $limit 	= $this->input->post("limit");
	  $page 	= $this->input->post("page");
	  $dept_id 	= $this->input->post("department");
	  $type 	= $this->input->post("type");
	  $mb_no 	= $this->input->post("emp");
	  $emp_type = $this->input->post("emp_type");
	  $offset 	= ($page - 1) * $limit;
	
	  $date_from = $this->input->post("dateFrom");
	  $date_from = new DateTime($date_from." 00:00:00");
	  
	  $date_to = $this->input->post("dateTo");
	  $date_to = new DateTime($date_to." 00:00:00");
	  
	  $today = new DateTime();
	  $reports = $this->session->userdata("reports");
	  $user_depts = 0;
	  if(isset($reports[1])) {
	    $user_depts = implode(',',$reports[1]);
	  }

	  
	  $action = "concat('<div class=\'action-buttons\'><a class=\'green request-view\' href=\'#\' data-id=\'',gm.mb_no,'\' data-date=\'',tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0'),'\' title=\'View\'><i class=\'ace-icon fa fa-search bigger-130\'></i></div>')";
	  $remarks = "if(ta.actual_in is NULL and ta.actual_out is NULL,

                            if(length(tawol.is_awol) > 0,
                                    if(tawol.is_awol = 1,
                                            if(tawol.is_leave_deduct = 1,
                                                    concat('AWoL - Leave Deducted<br/>',tawol.awol_reason),
                                                    concat('AWoL<br/>',tawol.awol_reason)
                                    ),
                                    if(tawol.is_leave_deduct = 1,
                                    concat('Not AWoL',if(tawol.is_el,' - Leave Deducted<br/>EL',''),'',tawol.awol_reason),
                                    concat('Not AWoL',if(tawol.is_el,' - EL',''),'<br/>',tawol.awol_reason)
                                    )
                            ),
                                    Case 

                                    WHEN length(toa.status) > 0 THEN
                                            if(toa.time_in = CONCAT(LPAD(tsc.shift_hr_from,2,'0'),':',LPAD(tsc.shift_min_from,2,'0')) and toa.time_out = CONCAT(LPAD(tsc.shift_hr_to,2,'0'),':',LPAD(tsc.shift_min_to,2,'0')),
                                                    if(toa.status = 1,'Has filed OBT',''),
                                            '')
                                    WHEN length(tla.status) > 0 THEN
                                            if(tla.status = 1,'Has filed Leave','')
                                    WHEN length(tcsq.status) > 0 THEN
                                            if(tcsq.status = 1,'Has filed CWS','')
                                    ELSE
                                            ''
                                    END
                            ),
                            if(ta.undertime > 0,
                            if(tawol.is_awol is not NULL,
                                    if(tawol.is_awol = 1,
                                            if(tawol.is_leave_deduct = 1,
                                                    concat('AWoL - Leave Deducted<br/>',tawol.awol_reason),
                                                    concat('AWoL<br/>',tawol.awol_reason)
                                            ),
                                            if(tawol.is_leave_deduct = 1,
                                            concat('Not AWoL',if(tawol.is_el,' - Leave Deducted<br/>EL',''),'',tawol.awol_reason),
                                            concat('Not AWoL',if(tawol.is_el,' - EL',''),'<br/>',tawol.awol_reason)
                                            )
                                    ),
                            ''),
                        '')

                    )remarks";
          
 	  $select = "gm.mb_id,
                    CONCAT(gm.mb_lname,', ',IF(mb_3='Local',gm.mb_fname,gm.mb_nick)) full_name,
                    d.dept_name,
                    CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) sched_date,
                    tsc.shift_code,
                    CONCAT(LPAD(tsc.shift_hr_from,2,'0'),':',LPAD(tsc.shift_min_from,2,'0')) shift_from,
                    CONCAT(LPAD(tsc.shift_hr_to,2,'0'),':',LPAD(tsc.shift_min_to,2,'0')) shift_to,
                    ta.actual_in,
                    ta.actual_out,
                    ta.undertime,
                    ta.tardy,
                    ".$action." `action`,
                    ".$remarks."
                    ,if(tawol.created_by is not NULL,(select mb_nick from g4_member where mb_no = tawol.created_by),'') tagger";


	  $where = "CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."' AND (tms.lv_app_id IS NULL OR tms.lv_app_id = 0) AND (toa.obt_app_id IS NULL OR ((toa.status = 3 AND toa.time_in = CONCAT(LPAD(tsc.shift_hr_from, 2, '0'), ':', LPAD(tsc.shift_min_from, 2, '0')) and toa.time_out = CONCAT(LPAD(tsc.shift_hr_to, 2, '0'), ':', LPAD(tsc.shift_min_to, 2, '0'))) != 1 ))";
	  if($mb_no)
	    $where .= " AND tms.mb_no = '$mb_no'";
	  else if($dept_id)
	    $where .= " AND gm.mb_deptno = '$dept_id' AND gm.mb_status = 1";
	  else
	    $where .= ($this->session->userdata('mb_deptno') == 24  || management_access() )?" AND gm.mb_status = 1":" AND gm.mb_status = 1 AND gm.mb_deptno IN({$user_depts})";
		
	
	  if($emp_type)
	    $where .= " AND gm.mb_employment_status = '$emp_type' ";
		
	  
	  $emp_list = ($this->session->userdata('mb_deptno') == 24  || management_access())?$this->employees_m->getAll(true,"*",false,0,0,array("mb_lname"=>"ASC")):$this->employees_m->getAll(true,"*",false,0,0,array("mb_lname"=>"ASC"), array("FIND_IN_SET(mb_deptno, '{$user_depts}') !="=>0));
		
		
		
	  
	  switch($type) {
	    case "incomplete":
		    $where .= " AND ta.att_id IS NOT NULL AND ((ta.actual_in IS NULL OR ta.actual_in = '') OR (ta.actual_out IS NULL OR ta.actual_out = '')) ";
		  break;
	    case "awol":
		    $where .= " AND ((ta.actual_in IS NULL OR ta.actual_in = '') AND (ta.actual_out IS NULL OR ta.actual_out = '')) ";
		  break;
		case "tardy-ut":
		    $where .= " AND (ta.undertime <> 0 OR ta.tardy <> 0) ";
		  break;
		default:
		    $where .= " AND (((ta.actual_in IS NULL OR ta.actual_in = '') OR (ta.actual_out IS NULL OR ta.actual_out = '')) OR (ta.undertime <> 0 OR ta.tardy <> 0)) ";
		  break;
	  }
	  
	  $attendance = $this->att_m->getEmployeeAttendanceDetails($select, $where, 0, 0, array("d.dept_name"=>"ASC","year"=>"ASC","month"=>"ASC","day"=>"ASC","mb_lname"=>"ASC"),Array("tms.mb_no","tms.year","tms.month","tms.day"));
	  $response_arr = $return_arr = array();
	  $response_arr = array("ID", "Name", "Department","Date","Shift","IN","OUT","Actual IN","Actual Out","UT (Min)","Tardy (Min)", "Detail", "Remarks","Tagged By");
	  $width_arr = array(60,250,120,80,40,60,60,60,70,70,80,50,200,90);
          $return_arr[] =  $attendance;
	  
	   echo json_encode(array("data"=>array_slice($return_arr[0],$offset,$limit), "header"=>$response_arr, "width"=>$width_arr, "total_count"=>count($return_arr[0]), "page" => $page));
	}
	
	public function viewRecord() {
	  $post = $this->input->post();
	  $emp	= $post['mb_no'];
	  $day  = $post['day'];
	  
	  $employee = (object) $this->employees_m->get($emp);
	  
	  $day_record = $this->att_m->getEmployeeAttendance("ta.*, tms.*, CONCAT(tms.year,'-',LPAD(tms.month,2,'0'),'-',LPAD(tms.day,2,'0')) att_date, CONCAT(IF(gm.mb_3='Local', gm.mb_fname, gm.mb_nick),' ',gm.mb_lname) fullname, d.dept_name, tsc.shift_code, CONCAT(LPAD(tsc.shift_hr_from,2,'0'),':',LPAD(tsc.shift_min_from,2,'0')) shift_from, CONCAT(LPAD(tsc.shift_hr_to,2,'0'),':',LPAD(tsc.shift_min_to,2,'0')) shift_to","tms.mb_no = '".$emp."' AND CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) = '".$day."' ");
	  
	  $leave_record = $this->leaves_m->getEmpLeaveApplication("tla.*, tlc.leave_code, tlc.leave_name, CASE tla.status WHEN 1 THEN 'Pending' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Rejected' END status_lbl", "tla.mb_no ='".$emp."' AND '".$day."' BETWEEN tla.date_from AND tla.date_to");
	  
	  $obt_record = $this->obt_m->getEmpOBTApplication("tla.*, CASE tla.status WHEN 1 THEN 'Pending' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Rejected' END status_lbl", "tla.mb_no ='".$emp."' AND '".$day."' = DATE(`date`)");
	  
	  $cws_record = $this->shifts_m->getEmployeeChangeSchedules("tcsq.*, CASE tcsq.status WHEN 1 THEN 'Pending' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Rejected' END status_lbl, CONCAT(LPAD(tsc2.shift_hr_from,2,'0'),':',LPAD(tsc2.shift_min_from,2,'0')) shift_from, CONCAT(LPAD(tsc2.shift_hr_to,2,'0'),':',LPAD(tsc2.shift_min_to,2,'0')) shift_to", "tcsq.mb_no ='".$emp."' AND '".$day."' BETWEEN tcsq.att_date_from AND tcsq.att_date_to");
	  
	  $date = new DateTime($day." 00:00:00");
	  $dateFrom = new DateTime($date->format("Y-m-d")." ".$day_record[0]->shift_from);
	  $dateTo = new DateTime($date->format("Y-m-d")." ".$day_record[0]->shift_to);
	  if($dateFrom > $dateTo)
	    $dateTo->modify("+1 days");
	  $att_record = $this->att_m->getAllLogsFiltered("a.*, gm.mb_no, CONCAT(year_log,'-',LPAD(month_log,2,0),'-',LPAD(day_log,2,0), ' ', LPAD(hour_log,2,0),':',LPAD(min_log,2,0)) time_log, CASE in_out_mode WHEN 0 THEN 'Time In' WHEN 1 THEN 'Time Out' END log_type", "gm.mb_no ='".$emp."' AND CONCAT(year_log,'-',LPAD(month_log,2,0),'-',LPAD(day_log,2,0)) BETWEEN '".$dateFrom->format("Y-m-d")."' AND '".$dateTo->format("Y-m-d")."'");
	  
	  $awol_record = $this->att_m->getAllAWOL("a.*", "a.awol_status = 1 and mb_no ='".$emp."' AND att_date ='".$day."'");
	  $awol_select = "update_datetime awol_date,
                            CONCAT(IF(awol_reason = '' AND is_awol = 0 AND is_el = 0,
                                        'Remove Tag by ',
                                        CONCAT(
                                        CASE WHEN is_awol = 1 THEN 'AWOL'
                                             WHEN is_el = 1 THEN 'EL'
                                             ELSE 'NOT AWOL'
                                        END,
                                                ' - ',
                                                awol_reason,
                                                ' <br> by ')),
                                    mb_name) awol_history";
          $awol_history = ($this->session->userdata('mb_deptno') == 24) ? $this->att_m->getAllAWOLHistory($awol_select, "a.mb_no ='".$emp."' AND att_date ='".$day."'",0,0,Array("update_datetime"=>"desc")) : Array();
	  
	  echo json_encode(array("data"=>$day_record,"leave_data"=>$leave_record,"obt_data"=>$obt_record, "cws_data"=>$cws_record, "att_data"=>$att_record, "awol_data"=>$awol_record, "dept_no"=>$this->session->userdata("mb_deptno") , "awol_history"=>$awol_history));
	  
	}
	
	public function markAsAWOL() {
	  $post = $this->input->post();
	  $creator = $this->session->userdata("mb_no");
	  $date = new DateTime();
	  $employee = (object) $this->employees_m->get($post['mb_no']);
	  $awol_record = $this->att_m->getAllAWOL("a.*", "mb_no ='".$post['mb_no']."' AND att_date ='".$post['day']."'");
	  if(!count($awol_record)) {
	    if($employee->mb_3 == "Local" and !in_array($employee->mb_deptno,Array(34,33,32))) 
		  $leave_code = "VL";
		else
		  $leave_code = "AL";
		$leave_dtl = $this->leaves_m->getAllLeavesFiltered(true,"l.leave_id, l.leave_code", "l.leave_code = '".$leave_code."'");
		$awol_date = new DateTime($post['day']);
		$emp_balance = $this->leaves_m->getEmpLeaveBalances($employee->mb_no,"*",$leave_dtl[0]->leave_id, $awol_date->format("Y"));
		
		$data = array("mb_no"=>$post['mb_no'],"att_date"=>$post['day'],"awol_reason"=>$post['reason'],"is_awol"=>1,"created_by"=>$creator,"created_datetime"=>$date->format("Y-m-d H:i:s"));
		if(count($emp_balance) && $emp_balance[0]->bal > 0) {
		  $this->leaves_m->updateEmpLeaveBalances(array("bal"=>($emp_balance[0]->bal*1)-1), array("leave_id"=>$leave_dtl[0]->leave_id,"mb_no"=>$employee->mb_no,"year"=>$awol_date->format("Y")));
		  $data["is_leave_deduct"] = 1;
		  $this->att_m->insertAWOL($data);
		}
		else {
		  $data["is_leave_deduct"] = 0;
		  $this->att_m->insertAWOL($data);
		}
	  }else{
              $this->editawol();
              exit();
          }
	  echo json_encode(array("success"=>1));
	}
	
	public function markAsEL() {
	  $post = $this->input->post();
	  $creator = $this->session->userdata("mb_no");
	  $date = new DateTime();
	  $employee = (object) $this->employees_m->get($post['mb_no']);
	  $awol_record = $this->att_m->getAllAWOL("a.*", "mb_no ='".$post['mb_no']."' AND att_date ='".$post['day']."'");
	  if(!count($awol_record)) {
	    if($employee->mb_3 == "Local" and !in_array($employee->mb_deptno,Array(34,33,32))) 
		  $leave_code = "VL";
		else
		  $leave_code = "AL";
		$leave_dtl = $this->leaves_m->getAllLeavesFiltered(true,"l.leave_id, l.leave_code", "l.leave_code = '".$leave_code."'");
		$awol_date = new DateTime($post['day']);
		$emp_balance = $this->leaves_m->getEmpLeaveBalances($employee->mb_no,"*",$leave_dtl[0]->leave_id, $awol_date->format("Y"));
		
		$data = array("mb_no"=>$post['mb_no'],"att_date"=>$post['day'],"awol_reason"=>$post['reason'],"is_awol"=>0,"is_el"=>1,"created_by"=>$creator,"created_datetime"=>$date->format("Y-m-d H:i:s"));
		if(count($emp_balance) && $emp_balance[0]->bal > 0) {
		  $this->leaves_m->updateEmpLeaveBalances(array("bal"=>($emp_balance[0]->bal*1)-1), array("leave_id"=>$leave_dtl[0]->leave_id,"mb_no"=>$employee->mb_no,"year"=>$awol_date->format("Y")));
		  $data["is_leave_deduct"] = 1;
		  $this->att_m->insertAWOL($data);
		}
		else {
		  $data["is_leave_deduct"] = 0;
		  $this->att_m->insertAWOL($data);
		}
	  }else{
              $this->editawol();
              exit();
          }
	  echo json_encode(array("success"=>1));
	}
        
 	public function markAsNotAWOL() {
	  $post = $this->input->post();
	  $creator = $this->session->userdata("mb_no");
	  $date = new DateTime();
	  $employee = (object) $this->employees_m->get($post['mb_no']);
	  $awol_record = $this->att_m->getAllAWOL("a.*", "mb_no ='".$post['mb_no']."' AND att_date ='".$post['day']."'");
	  if(!count($awol_record)) {
		$data = array("mb_no"=>$post['mb_no'],"att_date"=>$post['day'],"awol_reason"=>$post['reason'],"is_awol"=>0,"created_by"=>$creator,"created_datetime"=>$date->format("Y-m-d H:i:s"));
	    $data["is_leave_deduct"] = 0;
	    $this->att_m->insertAWOL($data);
          }else{
              $this->editawol();
              exit();
          }
	  echo json_encode(array("success"=>1));
	}   
        
	function editawol(){
            $post = $this->input->post();
            $creator = $this->session->userdata("mb_no");
            $employee = (object) $this->employees_m->get($post['mb_no']);
            $awol_record = $this->att_m->getAllAWOL("a.*", "mb_no ='".$post['mb_no']."' AND att_date ='".$post['day']."'");

            
                if($employee->mb_3 == "Local" and !in_array($employee->mb_deptno,Array(34,33,32))) 
		  $leave_code = "VL";
		else
		  $leave_code = "AL";
                
                
		$leave_dtl = $this->leaves_m->getAllLeavesFiltered(true,"l.leave_id, l.leave_code", "l.leave_code = '".$leave_code."'");
		$awol_date = new DateTime($post['day']);
		$emp_balance = $this->leaves_m->getEmpLeaveBalances($employee->mb_no,"*",$leave_dtl[0]->leave_id, $awol_date->format("Y"));
                
                $updateAwol = Array('awol_reason'=>$post['reason'],'created_by'=>$creator); 
             
            if(isset($post['awol_mark']) && $post['awol_mark'] == "1"){
                $updateAwol = array_merge($updateAwol,Array('is_awol'=>1,'is_el'=>0));
                
                 if(count($emp_balance) && $emp_balance[0]->bal > 0 ) {
		  if($awol_record[0]->is_leave_deduct !== "1")$this->leaves_m->updateEmpLeaveBalances(array("bal"=>($emp_balance[0]->bal*1)-1), array("leave_id"=>$leave_dtl[0]->leave_id,"mb_no"=>$employee->mb_no,"year"=>$awol_date->format("Y")));
		  $leave_deduct["is_leave_deduct"] = 1;
                 }
                 if($emp_balance[0]->bal == 0)$leave_deduct["is_leave_deduct"] = 0;
                
                $updateAwol = array_merge($updateAwol,$leave_deduct,Array('awol_status'=>1));
                
            }else if(isset($post['awol_mark']) && $post['awol_mark'] == "0"){
    
                if($awol_record[0]->is_awol or $awol_record[0]->is_el){
                    if($awol_record[0]->is_leave_deduct == "1")$this->leaves_m->updateEmpLeaveBalances(array("bal"=>($emp_balance[0]->bal*1)+1), array("leave_id"=>$leave_dtl[0]->leave_id,"mb_no"=>$employee->mb_no,"year"=>$awol_date->format("Y")));
                    
                   $updateAwol = array_merge($updateAwol,Array('is_awol'=>0,'is_el'=>0,'is_leave_deduct'=>0));
                }
                $updateAwol = array_merge($updateAwol,Array('awol_status'=>1));
            }else if(isset($post['awol_mark']) && $post['awol_mark'] == "el"){
                $updateAwol = array_merge($updateAwol,Array('is_awol'=>0,'is_el'=>1));
                                
                 if(count($emp_balance) && $emp_balance[0]->bal > 0) {
		  if($awol_record[0]->is_leave_deduct !== "1")$this->leaves_m->updateEmpLeaveBalances(array("bal"=>($emp_balance[0]->bal*1)-1), array("leave_id"=>$leave_dtl[0]->leave_id,"mb_no"=>$employee->mb_no,"year"=>$awol_date->format("Y")));
		  $leave_deduct["is_leave_deduct"] = 1;
                 }
                 if($emp_balance[0]->bal == 0)$leave_deduct["is_leave_deduct"] = 0;
                
                $updateAwol = array_merge($updateAwol,$leave_deduct,Array('awol_status'=>1));
                
            }else{

                if($awol_record[0]->is_leave_deduct == "1")$this->leaves_m->updateEmpLeaveBalances(array("bal"=>($emp_balance[0]->bal*1)+1), array("leave_id"=>$leave_dtl[0]->leave_id,"mb_no"=>$employee->mb_no,"year"=>$awol_date->format("Y")));
                $updateAwol = array_merge($updateAwol,Array('is_awol'=>0,'is_el'=>0,'is_leave_deduct'=>0,'awol_status'=>0));
            }
            
            $this->att_m->updateAWOL($updateAwol,Array('awol_id'=>$awol_record[0]->awol_id));
            echo json_encode(array("success"=>1));
            
        }


	
	public function exportAttendance() {
	  $activeSheetInd = -1;
	  $post 	= $this->input->post();
	  $limit 	= $this->input->post("limit");
	  $page 	= $this->input->post("page");
	  $dept_id 	= $this->input->post("export-dept");
	  $type 	= $this->input->post("type");
	  $mb_no 	= $this->input->post("export-emp");
	  $emp_type	= $this->input->post("export-emp-stat");
	  $offset 	= ($page - 1) * $limit;
	
	  $date_from = $this->input->post("export-from");
	  $date_from = new DateTime($date_from." 00:00:00");
	  
	  // $date_to = $this->input->post("export-to");
	  $date_to = $this->input->post("export-from");
	  $date_to = new DateTime($date_to." 00:00:00");
	  
	  $today = new DateTime();
	   
	  $reports = $this->session->userdata("reports");
	  $user_depts = 0;
	  if(isset($reports[1])) {
	    $user_depts = implode(',',$reports[1]);
	  }
		
	  $select = "tms.*, CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) sched_date, gm.mb_id, CONCAT(gm.mb_lname,', ',IF(mb_3='Local',gm.mb_fname,gm.mb_nick)) full_name, gm.mb_nick, gm.mb_3, CASE gm.mb_employment_status WHEN 1 THEN 'Probationary' WHEN 2 THEN 'Confirmed' END employment_label, d.dept_name, tsc.shift_code, CONCAT(LPAD(tsc.shift_hr_from,2,'0'),':',LPAD(tsc.shift_min_from,2,'0')) shift_from, CONCAT(LPAD(tsc.shift_hr_to,2,'0'),':',LPAD(tsc.shift_min_to,2,'0')) shift_to, ta.att_date, ta.actual_in, ta.actual_out, ta.undertime, ta.tardy, ta.undertime, tla.sub_categ_id, tlc.leave_code, tlsc.sub_categ_code";
	  
	  $where = "CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."' AND (tms.lv_app_id IS NULL OR tms.lv_app_id = 0 OR tlc.leave_code IS NULL OR tlc.leave_code IN ('EL','SL') OR (tlc.leave_code = 'AL' AND tlsc.sub_categ_id = '1')) AND (tawol.is_awol IS NULL OR tawol.is_awol = 1)";
	  if($mb_no)
	    $where .= " AND tms.mb_no = '$mb_no'";
	  else if($dept_id)
	    $where .= " AND gm.mb_deptno = '$dept_id' AND gm.mb_status = 1";
	  else
	    $where .= ($this->session->userdata('mb_deptno') == 24  || management_access() )?" AND gm.mb_status = 1":" AND gm.mb_status = 1 AND gm.mb_deptno IN({$user_depts})";
	
	  if($emp_type)
	    $where .= " AND gm.mb_employment_status = '$emp_type' ";
	
	  $where_incomplete = " AND ta.att_id IS NOT NULL AND ((ta.actual_in IS NULL OR ta.actual_in = '') OR (ta.actual_out IS NULL OR ta.actual_out = '')) ";
	  $where_all = " AND (((ta.actual_in IS NULL OR ta.actual_in = '') AND (ta.actual_out IS NULL OR ta.actual_out = '')) OR (ta.undertime <> 0 OR ta.tardy <> 0))  AND (SELECT COUNT(*) FROM tk_obt_application toa where CONCAT(LPAD(tsc.shift_hr_from, 2, '0'),':',LPAD(tsc.shift_min_from, 2, '0')) = toa.time_in AND CONCAT(LPAD(tsc.shift_hr_to, 2, '0'),':',LPAD(tsc.shift_min_to, 2, '0')) = toa.time_out AND CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) = toa.date AND tms.mb_no = toa.mb_no) = 0";
	  
	  $depts = $this->employees_m->getDepts(($this->session->userdata('mb_deptno') == 24  || management_access() )?array():array("FIND_IN_SET(dept_no, '{$user_depts}') !="=>0));
	  
	  $default_shifts[0]	= "RD";
	  $default_shifts[-1]	= "SS";
	  $default_shifts[-2]	= "PH";
	  
	  $this->load->library('excel');
	  
	  $headerStyle = array('alignment' => array('wrap'       => true,
												'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
												),
						   'font'=> array('bold'=>true, 'size'=> "10"));
	  $tblHeaderStyle = array('alignment' => array('wrap'       => true,
												'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
												),
							  'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
												'color' => array('rgb' => 'F7FF80')
												),
							  'borders' => array('outline' => array(
												'style' => PHPExcel_Style_Border::BORDER_THIN,
												'color' => array('argb' => '000000'),
												)),
						      'font'=> array('bold'=>true, 'size'=> "10", 'italic'=>true));
	  $tblDataStyle = array('alignment' => array('wrap'       => true,
												'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
												),
							  'borders' => array('outline' => array(
												'style' => PHPExcel_Style_Border::BORDER_THIN,
												'color' => array('argb' => '000000'),
												)),
						      'font'=> array('bold'=>true, 'size'=> "10", 'italic'=>true));
							  
	  $activeSheet = $this->excel->setActiveSheetIndex(++$activeSheetInd);
	  $c = 0;
	  $row = 1;
	  $column = PHPExcel_Cell::stringFromColumnIndex($c);
	  $column2 = PHPExcel_Cell::stringFromColumnIndex($c+10);
	  $cell = $column.$row;
	  $cell2 = $column2.$row;
	  $activeSheet->mergeCells($cell.":".$cell2); 
	  $activeSheet->setCellValue($cell, "DAILY ATTENDANCE REPORT");
	  $activeSheet->getStyle($cell)->applyFromArray($headerStyle);
	  
	  $row++;
	  $column = PHPExcel_Cell::stringFromColumnIndex($c);
	  $column2 = PHPExcel_Cell::stringFromColumnIndex($c+10);
	  $cell = $column.$row;
	  $cell2 = $column2.$row;
	  $activeSheet->mergeCells($cell.":".$cell2); 
	  $activeSheet->getCell($cell)->setValueExplicit($date_from->format("F j, Y"), PHPExcel_Cell_DataType::TYPE_STRING);
	  $activeSheet->getStyle($cell)->applyFromArray($headerStyle);
	  
	  $row++;
	  $column = PHPExcel_Cell::stringFromColumnIndex($c);
	  $column2 = PHPExcel_Cell::stringFromColumnIndex($c+10);
	  $cell = $column.$row;
	  $cell2 = $column2.$row;
	  $activeSheet->mergeCells($cell.":".$cell2); 
	  $activeSheet->getCell($cell)->setValueExplicit("Shift Covered: 6:00 am (of ".$date_from->format("M j").") -7:00 am (of ".$date_to->format("M j").")", PHPExcel_Cell_DataType::TYPE_STRING);
	  $activeSheet->getStyle($cell)->applyFromArray($headerStyle);
	  
	  $row++;
	  $column = PHPExcel_Cell::stringFromColumnIndex($c);
	  $cell = $column.$row;
	  $activeSheet->setCellValue($cell, "No");
	  $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $cell = $column.$row;
	  $activeSheet->setCellValue($cell, "Department");
	  $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $cell = $column.$row;
	  $activeSheet->setCellValue($cell, "Name");
	  $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $cell = $column.$row;
	  $activeSheet->setCellValue($cell, "Nick\nName");
	  $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $cell = $column.$row;
	  $activeSheet->setCellValue($cell, "Local\nExpat");
	  $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $cell = $column.$row;
	  $activeSheet->setCellValue($cell, "Employment\nStatus");
	  $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $cell = $column.$row;
	  $activeSheet->setCellValue($cell, "Shift");
	  $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $cell = $column.$row;
	  $activeSheet->setCellValue($cell, "Absence");
	  $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $cell = $column.$row;
	  $activeSheet->setCellValue($cell, "Tardiness");
	  $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $cell = $column.$row;
	  $activeSheet->setCellValue($cell, "Undertime");
	  $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $cell = $column.$row;
	  $activeSheet->setCellValue($cell, "Remarks");
	  $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  
	  $activeSheet = $this->excel->setActiveSheetIndex(0);
	  $activeSheet->setTitle('Attendance Report');

	  $highestCol = $activeSheet->getHighestColumn();
	  $total_columns = PHPExcel_Cell::columnIndexFromString($highestCol);
	  for ($col = 0; $col<$total_columns; $col++) {
		$column_start = PHPExcel_Cell::stringFromColumnIndex($col);
		$activeSheet->getColumnDimension($column_start)->setAutoSize(true); 
	  }
	  
	  $employee = (object) $this->employees_m->get($mb_no);
	  foreach($depts as $key=>$dept) {
	    if($dept_id || $mb_no ) {
		  if($dept_id == $dept->dept_no || $dept == $employee->mb_deptno)
		    $attendance = $this->att_m->getEmployeeAttendance($select, $where.$where_all, 0, 0, array("d.dept_name"=>"ASC","year"=>"ASC","month"=>"ASC","day"=>"ASC","mb_lname"=>"ASC"));
		  else
		    $attendance = array();
		}
		else {
		  $where_dtl = $where.$where_all." AND gm.mb_deptno = '".$dept->dept_no."' ";
		  $attendance = $this->att_m->getEmployeeAttendance($select, $where_dtl, 0, 0, array("d.dept_name"=>"ASC","year"=>"ASC","month"=>"ASC","day"=>"ASC","mb_lname"=>"ASC"));
		}
		
		$rows_to_merge = count($attendance);
		
		$row++;
		$c = 0;
	    $column = PHPExcel_Cell::stringFromColumnIndex($c);
	    $mcell = $cell = $column.$row;
		if($rows_to_merge > 1) {
		  $cell2 = $column.($row+($rows_to_merge-1));
		  $activeSheet->mergeCells($cell.":".$cell2); 
		  $mcell = $cell.":".$cell2;
		}
	    $activeSheet->setCellValue($cell, $key+1);
	    $activeSheet->getStyle($mcell)->applyFromArray($tblDataStyle);
	    
		$column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	    $mcell = $cell = $column.$row;
		if($rows_to_merge > 1) {
		  $cell2 = $column.($row+($rows_to_merge-1));
		  $activeSheet->mergeCells($cell.":".$cell2); 
		  $mcell = $cell.":".$cell2;
		}
	    $activeSheet->setCellValue($cell, $dept->dept_name);
	    $activeSheet->getStyle($mcell)->applyFromArray($tblDataStyle);
		
		if(count($attendance)) {
		  foreach($attendance as $att) {
		    $z = $c;
		    $column = PHPExcel_Cell::stringFromColumnIndex(++$z);
	        $mcell = $cell = $column.$row;
	        $activeSheet->setCellValue($cell, $att->full_name);
		    $activeSheet->getStyle($mcell)->applyFromArray($tblDataStyle);
			$column = PHPExcel_Cell::stringFromColumnIndex(++$z);
	        $mcell = $cell = $column.$row;
	        $activeSheet->setCellValue($cell, $att->mb_nick);
		    $activeSheet->getStyle($mcell)->applyFromArray($tblDataStyle);
			$column = PHPExcel_Cell::stringFromColumnIndex(++$z);
	        $mcell = $cell = $column.$row;
	        $activeSheet->setCellValue($cell, $att->mb_3);
		    $activeSheet->getStyle($mcell)->applyFromArray($tblDataStyle);
			$column = PHPExcel_Cell::stringFromColumnIndex(++$z);
	        $mcell = $cell = $column.$row;
	        $activeSheet->setCellValue($cell, $att->employment_label);
		    $activeSheet->getStyle($mcell)->applyFromArray($tblDataStyle);
			$column = PHPExcel_Cell::stringFromColumnIndex(++$z);
	        $mcell = $cell = $column.$row;
	        $activeSheet->setCellValue($cell, $att->shift_from." - ".$att->shift_to);
		    $activeSheet->getStyle($mcell)->applyFromArray($tblDataStyle);
			$column = PHPExcel_Cell::stringFromColumnIndex(++$z);
	        $mcell = $cell = $column.$row;
	        $activeSheet->setCellValue($cell, ($att->sub_categ_code?$att->sub_categ_code:$att->leave_code));
		    $activeSheet->getStyle($mcell)->applyFromArray($tblDataStyle);
			$column = PHPExcel_Cell::stringFromColumnIndex(++$z);
	        $mcell = $cell = $column.$row;
	        $activeSheet->setCellValue($cell, $att->tardy > 0?$att->tardy." minute(s)":"");
		    $activeSheet->getStyle($mcell)->applyFromArray($tblDataStyle);
			$column = PHPExcel_Cell::stringFromColumnIndex(++$z);
	        $mcell = $cell = $column.$row;
	        $activeSheet->setCellValue($cell, $att->undertime > 0?$att->undertime." minute(s)":"");
		    $activeSheet->getStyle($mcell)->applyFromArray($tblDataStyle);
			$column = PHPExcel_Cell::stringFromColumnIndex(++$z);
	        $mcell = $cell = $column.$row;
	        $activeSheet->setCellValue($cell, "");
		    $activeSheet->getStyle($mcell)->applyFromArray($tblDataStyle);
			$row++;
		  }
		  $row--;
		}
		else {
		  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		  $cell = $column.$row;
		  $column2 = PHPExcel_Cell::stringFromColumnIndex($c+8);
		  $cell2 = $column2.$row;
		  $activeSheet->mergeCells($cell.":".$cell2); 
		  $activeSheet->setCellValue($cell, "NO ISSUE");
		  $activeSheet->getStyle($cell.":".$cell2)->applyFromArray($tblDataStyle);
		}
		// $row+=($rows_to_merge?$rows_to_merge-1:0);
		
	  }
	  
	  $attendance = $this->att_m->getEmployeeAttendance($select, $where.$where_incomplete, 0, 0, array("d.dept_name"=>"ASC","year"=>"ASC","month"=>"ASC","day"=>"ASC","mb_lname"=>"ASC"));
	  
	  $row+=2;
	  $c=-1;
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $column2 = PHPExcel_Cell::stringFromColumnIndex($c+2);
	  $cell = $column.$row;
	  $cell2 = $column2.$row;
	  $activeSheet->mergeCells($cell.":".$cell2); 
	  $activeSheet->setCellValue($cell, "List of Employee present but with issue:");
	  $activeSheet->getStyle($cell)->applyFromArray($headerStyle);
	  foreach($attendance as $key=>$att) {
	    $row++;
		$c = -1;
	    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	    $cell = $column.$row;
	    $activeSheet->setCellValue($cell, $key+1);
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		$column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	    $cell = $column.$row;
	    $activeSheet->setCellValue($cell, $att->full_name);
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		$column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	    $cell = $column.$row;
		$type = (empty($att->actual_in)?"No Time In":"No Time Out");
	    $activeSheet->setCellValue($cell, $type);
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
	  }
	  // die();
	  
	  $c = -1;
	  $total_columns = $c+11;
	  for ($col = 0; $col<$total_columns; $col++) {
		$column_start = PHPExcel_Cell::stringFromColumnIndex($col);
		$activeSheet->getColumnDimension($column_start)->setAutoSize(true); 
	  } 
	  
	  $file_name = "Daily Attendance Report.xlsx";
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="' . $file_name . '"');
      header('Cache-Control: max-age=0');
      $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
      $objWriter->save('php://output');
	  
	  
	}
	
	public function exportSummaryAttendance() {
	  $activeSheetInd = -1;
	  $post 	= $this->input->post();
	  $limit 	= $this->input->post("limit");
	  $page 	= $this->input->post("page");
	  $dept_id 	= $this->input->post("export-dept");
	  $type 	= $this->input->post("type");
	  $mb_no 	= $this->input->post("export-emp");
	  $emp_type	= $this->input->post("export-emp-stat");
	  $offset 	= ($page - 1) * $limit;
	
	  $date_from = $this->input->post("export-from");
	  $date_from = new DateTime($date_from." 00:00:00");
	  
	  // $date_to = $this->input->post("export-to");
	  $date_to = $this->input->post("export-to");
	  $date_to = new DateTime($date_to." 00:00:00");
	  
	  $today = new DateTime();
	  
	  $headers = array("No.","Name","Department","Status","TARDY","UNDERTIME");
	  
	  $leave_types = $this->leaves_m->getAllLeaves(false,'tlsc.*,l.*',true);
	  $total_leaves = array();
	  $t_local = $t_expat = $te_undertime = $te_tardy = $tl_undertime = $tl_tardy = $tl_awol = $te_awol = 0;
	  foreach($leave_types as $leave) {
	    $headers[] = ($leave->sub_categ_code?$leave->sub_categ_code:$leave->leave_code);
		$total_leaves['expat'][($leave->sub_categ_code?$leave->sub_categ_code:$leave->leave_code)] = 0;
		$total_leaves['local'][($leave->sub_categ_code?$leave->sub_categ_code:$leave->leave_code)] = 0;
	  }
	  $headers[] = "AWOL";
	  
	  $reports = $this->session->userdata("reports");
	  $user_depts = 0;
	  if(isset($reports[1])) {
	    $user_depts = implode(',',$reports[1]);
	  }
		
	  $select = "tms.*, CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) sched_date, gm.mb_id, CONCAT(gm.mb_lname,', ',IF(mb_3='Local',gm.mb_fname,gm.mb_nick)) full_name, gm.mb_nick, gm.mb_3, CASE gm.mb_employment_status WHEN 1 THEN 'Probationary' WHEN 2 THEN 'Confirmed' END employment_label, d.dept_name, tsc.shift_code, CONCAT(LPAD(tsc.shift_hr_from,2,'0'),':',LPAD(tsc.shift_min_from,2,'0')) shift_from, CONCAT(LPAD(tsc.shift_hr_to,2,'0'),':',LPAD(tsc.shift_min_to,2,'0')) shift_to, ta.att_date, ta.actual_in, ta.actual_out, ta.undertime, SUM(IFNULL(ta.tardy,0)) tardy, SUM(IFNULL(ta.undertime,0)) undertime";
	  
	  $where = "CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."' AND (tawol.is_awol IS NULL OR tawol.is_awol = 1)";
	  if($mb_no)
	    $where .= " AND tms.mb_no = '$mb_no'";
	  else if($dept_id)
	    $where .= " AND gm.mb_deptno = '$dept_id' AND gm.mb_status = 1";
	  else 
		$where .= ($this->session->userdata('mb_deptno') == 24  || management_access() )?" AND gm.mb_status = 1":" AND gm.mb_status = 1 AND gm.mb_deptno IN({$user_depts})";
	
	  if($emp_type)
	    $where .= " AND gm.mb_employment_status = '$emp_type' ";
		
	  $where_all = " AND (((ta.actual_in IS NULL OR ta.actual_in = '') AND (ta.actual_out IS NULL OR ta.actual_out = '')) OR (ta.undertime <> 0 OR ta.tardy <> 0)) AND toa.obt_app_id IS NULL AND tms.shift_id > 0 AND (SELECT COUNT(*) FROM tk_obt_application toa where CONCAT(LPAD(tsc.shift_hr_from, 2, '0'),':',LPAD(tsc.shift_min_from, 2, '0')) = toa.time_in AND CONCAT(LPAD(tsc.shift_hr_to, 2, '0'),':',LPAD(tsc.shift_min_to, 2, '0')) = toa.time_out AND CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) = toa.date AND tms.mb_no = toa.mb_no) = 0";
	  
	  $attendance = $this->att_m->getEmployeeAttendanceSummary($select, $where.$where_all, 0, 0, array("d.dept_name"=>"ASC","employment_label"=>"DESC","year"=>"ASC","month"=>"ASC","day"=>"ASC","mb_lname"=>"ASC"),array("tms.mb_no"));
	  
	  $this->load->library('excel');
	  
	  $headerStyle = array('alignment' => array('wrap'       => true,
												'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
												),
						   'font'=> array('bold'=>true, 'size'=> "10"));
	  $tblHeaderStyle = array('alignment' => array('wrap'       => true,
												'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
												),
							  'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
												'color' => array('rgb' => 'F7FF80')
												),
							  'borders' => array('outline' => array(
												'style' => PHPExcel_Style_Border::BORDER_THIN,
												'color' => array('argb' => '000000'),
												)),
						      'font'=> array('bold'=>true, 'size'=> "10", 'italic'=>true));
	  $tblDataStyle = array('alignment' => array('wrap'       => true,
												'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
												),
							  'borders' => array('outline' => array(
												'style' => PHPExcel_Style_Border::BORDER_THIN,
												'color' => array('argb' => '000000'),
												)),
						      'font'=> array('bold'=>true, 'size'=> "10", 'italic'=>true));
							  
	  $activeSheet = $this->excel->setActiveSheetIndex(++$activeSheetInd);
	  $c = -1;
	  $row = 1;
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $column2 = PHPExcel_Cell::stringFromColumnIndex($c+19);
	  $cell = $column.$row;
	  $cell2 = $column2.$row;
	  $activeSheet->mergeCells($cell.":".$cell2); 
	  $activeSheet->setCellValue($cell, "LEAVE / ABSENCE SUMMARY");
	  $activeSheet->getStyle($cell)->applyFromArray($headerStyle);
	  $c = -1;
	  $row++;
	  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
	  $column2 = PHPExcel_Cell::stringFromColumnIndex($c+19);
	  $cell = $column.$row;
	  $cell2 = $column2.$row;
	  $activeSheet->mergeCells($cell.":".$cell2); 
	  $activeSheet->setCellValue($cell, $date_from->format("M j, Y")." - ".$date_to->format("M j, Y"));
	  $activeSheet->getStyle($cell)->applyFromArray($headerStyle);
	  $row++;
	  $c = -1;
	  foreach($headers as $header){
	    $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		$cell = $column.$row;
		$activeSheet->setCellValue($cell, $header);
	    $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  }
	  
	  foreach($attendance as $ind=>$att) {
	    $row++;
	    $c = -1;
		$column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		$cell = $column.$row;
		$activeSheet->setCellValue($cell, $ind+1);
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		$column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		$cell = $column.$row;
		$activeSheet->setCellValue($cell, $att->full_name);
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		$column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		$cell = $column.$row;
		$activeSheet->setCellValue($cell, $att->dept_name." (".$att->mb_3.")");
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		$column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		$cell = $column.$row;
		$activeSheet->setCellValue($cell, $att->employment_label);
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		$column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		$cell = $column.$row;
		$activeSheet->setCellValue($cell, $att->tardy);
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		if($att->mb_3 == 'Local') {
		  $t_local++;
		  $tl_tardy += $att->tardy;
		  $tl_undertime += $att->undertime;
		}
		else {
		  $t_expat++;
		  $te_tardy += $att->tardy;
		  $te_undertime += $att->undertime;
		}
		$column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		$cell = $column.$row;
		$activeSheet->setCellValue($cell, $att->undertime);
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		
		foreach($leave_types as $leave) {
		  if(!empty($leave->sub_categ_code)) {
			$query = "SELECT
							tla.*,
							IF(tla.date_from<'".$date_from->format("Y-m-d")."','".$date_from->format("Y-m-d")."',tla.date_from),
							IF(tla.date_to>'".$date_to->format("Y-m-d")."','".$date_to->format("Y-m-d")."',tla.date_to),
							tla.mb_no,
							(SELECT COUNT(*) FROM tk_member_schedule tms WHERE tms.mb_no = tla.mb_no AND 
							   CONCAT(tms.year,'-',LPAD(tms.month,2,'0'),'-',LPAD(tms.day,2,'0'))
								 BETWEEN DATE(IF(tla.date_from<'".$date_from->format("Y-m-d")."','".$date_from->format("Y-m-d")."',tla.date_from)) AND DATE(IF(tla.date_to>'".$date_to->format("Y-m-d")."','".$date_to->format("Y-m-d")."',tla.date_to))) absences
						FROM
							tk_lv_application tla
						WHERE
							tla.leave_id = '".$leave->leave_id."' AND
							tla.sub_categ_id = '".$leave->sub_categ_id."' AND
							tla.mb_no = '".$att->mb_no."' AND
							(
							  tla.date_from BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."' OR
							  tla.date_to BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."' OR
							  '".$date_from->format("Y-m-d")."' BETWEEN tla.date_from AND tla.date_to OR
							  '".$date_to->format("Y-m-d")."' BETWEEN tla.date_from AND tla.date_to
							) AND
							tla.status IN ('1','3');";
		  }
		  else {
		    $query = "SELECT
							tla.*,
							IF(tla.date_from<'".$date_from->format("Y-m-d")."','".$date_from->format("Y-m-d")."',tla.date_from),
							IF(tla.date_to>'".$date_to->format("Y-m-d")."','".$date_to->format("Y-m-d")."',tla.date_to),
							tla.mb_no,
							(SELECT COUNT(*) FROM tk_member_schedule tms WHERE tms.mb_no = tla.mb_no AND 
							   CONCAT(tms.year,'-',LPAD(tms.month,2,'0'),'-',LPAD(tms.day,2,'0'))
								 BETWEEN DATE(IF(tla.date_from<'".$date_from->format("Y-m-d")."','".$date_from->format("Y-m-d")."',tla.date_from)) AND DATE(IF(tla.date_to>'".$date_to->format("Y-m-d")."','".$date_to->format("Y-m-d")."',tla.date_to))) absences
						FROM
							tk_lv_application tla
						WHERE
							tla.leave_id = '".$leave->leave_id."' AND
							tla.mb_no = '".$att->mb_no."' AND
							(
							  tla.date_from BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."' OR
							  tla.date_to BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."' OR
							  '".$date_from->format("Y-m-d")."' BETWEEN tla.date_from AND tla.date_to OR
							  '".$date_to->format("Y-m-d")."' BETWEEN tla.date_from AND tla.date_to
							) AND
							tla.status IN ('1','3');";
		  }
		  $record_res = $this->att_m->query($query);
		  $leaves_rec = $record_res->result();
		  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		  $cell = $column.$row;
		  if(count($leaves_rec)) {
		    $activeSheet->setCellValue($cell, $leaves_rec[0]->absences);
			$code = $leave->sub_categ_code?$leave->sub_categ_code:$leave->leave_code;
			if($att->mb_3 == 'Local')
			  $total_leaves['local'][$code]+=$leaves_rec[0]->absences;
			else
			  $total_leaves['expat'][$code]+=$leaves_rec[0]->absences;
		  }
		  else {
		    $activeSheet->setCellValue($cell, 0);
		  }
		  $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		}
		
		$query = "SELECT tms.*
					FROM
						tk_member_schedule tms
					LEFT JOIN
						tk_attendance ta
						ON `tms`.`mb_no` = `ta`.`mb_no`
							AND CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) = ta.att_date
					WHERE
						CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."'
						AND ((actual_in is null OR actual_in = '') AND (actual_out is null or actual_out = ''))
						AND tms.shift_id > 0
						AND (IFNULL((SELECT 1 FROM tk_lv_application tla WHERE tms.mb_no = tla.mb_no AND CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) BETWEEN tla.date_from and tla.date_to AND tla.status IN (1,3) LIMIT 1),0)) = 0
						AND (IFNULL((SELECT 1 FROM tk_obt_application toa WHERE tms.mb_no = toa.mb_no AND CONCAT(tms.year,'-',LPAD(tms.month, 2, '0'),'-',LPAD(tms.day, 2, '0')) = toa.date AND toa.status IN (1,3) GROUP BY toa.date),0)) = 0
					AND tms.mb_no = '".$att->mb_no."'";
		$record_res = $this->att_m->query($query);
		$awol_rec = $record_res->result();
		$column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		$cell = $column.$row;
		if(count($awol_rec)) {
		  $activeSheet->setCellValue($cell, count($awol_rec));
		  if($att->mb_3 == 'Local')
			$tl_awol+=count($awol_rec);
		  else
			$te_awol+=count($awol_rec);
		}
		else
		  $activeSheet->setCellValue($cell, 0);
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
	  }
	  
	  $row++;
	  $row++;
	  $c=-1;
	  foreach($headers as $key=>$header) {
	    $c++;
	    if($key < 3 )
		  continue;
		$column = PHPExcel_Cell::stringFromColumnIndex($c);
		$cell = $column.$row;
	    if($key == 3) 
		  $activeSheet->setCellValue($cell, "Total No. of Employees");
		else
		  $activeSheet->setCellValue($cell, $header);
	    $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
	  }
	
	  $row++;
	  $c=-1;
	  foreach($headers as $key=>$header) {
	    $c++;
	    if($key < 3)
		  continue;
		$column = PHPExcel_Cell::stringFromColumnIndex($c);
		$cell = $column.$row;
	    if($key == 3) 
		  $activeSheet->setCellValue($cell, "Local : ".$t_local);
		else if($key == 4) 
		  $activeSheet->setCellValue($cell, $tl_tardy);
		else if($key == 5) 
		  $activeSheet->setCellValue($cell, $tl_undertime);
		else if($key != count($headers)-1) {
		  $activeSheet->setCellValue($cell, $total_leaves['local'][$header]);
		}
		else
		  $activeSheet->setCellValue($cell, $tl_awol);
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
	  }
	  
	  $row++;
	  $c=-1;
	  foreach($headers as $key=>$header) {
	    $c++;
	    if($key < 3)
		  continue;
		$column = PHPExcel_Cell::stringFromColumnIndex($c);
		$cell = $column.$row;
	    if($key == 3) 
		  $activeSheet->setCellValue($cell, "Expat : ".$t_expat);
		else if($key == 4) 
		  $activeSheet->setCellValue($cell, $te_tardy);
		else if($key == 5) 
		  $activeSheet->setCellValue($cell, $te_undertime);
		else if($key != count($headers)-1) {
		  $activeSheet->setCellValue($cell, $total_leaves['expat'][$header]);
		}
		else
		  $activeSheet->setCellValue($cell, $te_awol);
	    $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
	  }
	  
	  $c = -1;
	  $total_columns = $c+21;
	  for ($col = 0; $col<$total_columns; $col++) {
		$column_start = PHPExcel_Cell::stringFromColumnIndex($col);
		$activeSheet->getColumnDimension($column_start)->setAutoSize(true); 
	  } 
	  
	  $file_name = "Attendance Summary Report (".$date_from->format("Y-m-d")." - ".$date_to->format("Y-m-d").").xlsx";
      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="' . $file_name . '"');
      header('Cache-Control: max-age=0');
      $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
      $objWriter->save('php://output');
	}
	/* End of Attendance List */
	
	/* Sched List */
	public function getEmpSchedGrid() {
	  $post 	= $this->input->post();
	  $limit 	= $this->input->post("limit");
	  $page 	= $this->input->post("page");
	  $status 	= $this->input->post("status");
	  $mb_no 	= $this->input->post("emp");
	  $offset 	= ($page - 1) * $limit;
	
	  $date_from = $this->input->post("dateFrom");
	  $date_from = new DateTime($date_from." 00:00:00");
	  
	  $date_to = $this->input->post("dateTo");
	  $date_to = new DateTime($date_to." 00:00:00");
	  
	  $having_str = "(( period_from BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."') OR (period_to BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."') OR ( '".$date_from->format("Y-m-d")."' BETWEEN period_from AND period_to) OR ( '".$date_to->format("Y-m-d")."' BETWEEN period_from AND period_to ))";
	  if(!empty($status)) {
	    $having_str .= " AND tsu.status = '".($status=="-1"?0:$status)."'";
	  }
	  if(!empty($dept_id)) {	    
	    $having_str .= " AND tsu.apprv_grp_id = '".$dept_id."' AND tag.enabled = 1";
	    $data = $this->shifts_m->getAllUploadsFiltered("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  else {
	    $data = $this->shifts_m->getAllUploadsFiltered("*",$having_str);
	    $all_leaves_count = count($data);
	  }
	  
	  $select_str = "*, gm.mb_id, gm.mb_lname, gm.mb_nick, gm.mb_fname, gm.mb_3, CASE tsu.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END tsu_status_lbl, tsu.status tk_status, DATE(tsu.updated_datetime) updated_date";
	  
	  $response_arr = array("Group", "Schedule", "Uploaded By", "Uploaded Date", "For Approval", "Status", "Action");
	  $widths_arr = array(100,200,200,120,300,160,108);
	  
	  $data_all = $this->shifts_m->getAllUploadsFiltered($select_str, $having_str, $offset, $limit, array("upload_id"=>"Desc"));
	  $return_arr = array();
	  foreach($data_all as $tk) {
	    $approver = "";
	    if(in_array($tk->tk_status,array(1))) {
	      $for_approval = $this->shifts_m->getAllForApprovalFiltered("tsa.approved_level, tsa.apprv_grp_id, tsa.upload_id", "apprv_grp_id = '".$tk->apprv_grp_id."' AND upload_id = '".$tk->upload_id."'");
		  if(count($for_approval)) {
		    $approvers = $this->shifts_m->getApprovalGroupApprover($tk->apprv_grp_id,"GROUP_CONCAT(gm.mb_nick) approvers",array("level"=>$for_approval[0]->approved_level));
		    if(count($approvers)) {
		      $approver = $approvers[0]->approvers;
			}
		  }
		}
	    $return_arr[] = array(
						  "group_code"	=> $tk->group_code,
						  "schedule"	=> $tk->period_from." ~ ".$tk->period_to,
						  "full_name"	=> $tk->mb_lname.", ".($tk->mb_3 =="Expat"?$tk->mb_nick:$tk->mb_fname),
						  "upload_date"	=> $tk->updated_date,
						  "approver"	=> $approver,
						  "status_lbl"	=> $tk->tsu_status_lbl,
						  "action"		=> '<div class="action-buttons">'.
											  '<a class="blue download-file" href="'.base_url($tk->file_path).'" download="'.$tk->org_file.'" title="Download"><i class="ace-icon fa fa-download bigger-130"></i></a>'.
											  (in_array($tk->tk_status,array(1,2,3,4))?'<a class="green request-view" href="#" data-id="'.$tk->upload_id.'" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>':'').
											'</div>'
						);
	  }
	  echo json_encode(array("data"=>$return_arr, "header"=>$response_arr, "width"=>$widths_arr, "total_count"=>count($return_arr), "page" => $page));
	}
	/* End of Sched List */
	
}
?>