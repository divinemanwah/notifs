<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave extends MY_Controller {

	private $month_list;

	function __construct() {
		parent::__construct();
		$this->load->model('employees_model', 'employees_m');
		$this->load->model('leaves_model', 'leaves_m');
		$this->load->model('shifts_model', 'shifts_m');
		$this->load->model('attendance_model', 'att_m');
		
		$this->load->library('ws', array('tag' => getenv('HTTP_HOST') == '10.120.10.139' ? 'hris' : 'hristest'));
	}
	
	/* Views */
	
	public function index() {
		// redirect("/timekeeping/summary");
	}
	
	public function settings() {
	    $this->view_template('leave/leave_settings', 'Leave', array(
			'breadcrumbs' => array('Manage','General Settings'),
			'js' => array(
					'jquery.dataTables.min.js',
					'jquery.dataTables.bootstrap.js',
					'leave.settings.js'
				),
			'data' => $this->leaves_m->getGeneralSettings()
		));
	}
	
	public function approval_settings() {
	    $this->view_template('leave/approval_settings', 'Leave', array(
			'breadcrumbs' => array('Manage','Approval Settings'),
			'js' => array(
					'jquery.dataTables.min.js',
					'jquery.dataTables.bootstrap.js',
					'leave.approval_settings.js'
				),
			'depts' => $this->employees_m->getDepts()
		));
	}
	
	public function submit_leave() {
	  // $date = new DateTime();
	  // $emp_rec = $this->employees_m->get($this->session->userdata("mb_no"));
	  // if(isset($emp_rec["mb_commencement"]) && !empty($emp_rec["mb_commencement"])) {
	    // $commencement  = new DateTime($emp_rec["mb_commencement"]." ".$date->format("H:i:s"));
		// if($commencement < $date) {
		  // echo "Asdasd";
		// }
	  // }
	  
	  $curDate = new DateTime();
	  $curYear = $curDate->format("Y");
	  $initYear = 2015;
	  if($initYear < $curYear-1) {
	    $initYear = $curYear-1;
	  }
	  
	  $this->view_template('leave/submit_leave', 'Leave', array(
			'breadcrumbs' 	=> array('Submit Leave'),
			'js' 			=> array(
								 'jquery.dataTables.min.js',
								 'jquery.dataTables.bootstrap.js',
								 'jquery.inputlimiter.1.3.1.min.js',
								 'jquery.validate.min.js',
								 'date-time/bootstrap-datepicker.min.js',
								 'leave.filing.js'
							   ),
			'emp_id'		=> $this->session->userdata("mb_no"),
			'settings' 		=> $this->leaves_m->getGeneralSettings(),
			'start_year'	=> $initYear,
			'end_year'		=> $curYear
	  ));
	}
	
	public function manage_approval(){
	  $this->view_template('leave/manage_approval', 'Leave', array(
			'breadcrumbs' => array('Leaves for Approval'),
			'js' => array(
					'jquery.dataTables.min.js',
					'jquery.dataTables.bootstrap.js',
					'jquery.inputlimiter.1.3.1.min.js',
					'date-time/bootstrap-datepicker.min.js',
					'leave.manage_approval.js'
				)
	  ));
	}
	
	public function balances() {
	  $emp_list = $this->employees_m->getAll(false,"*",false);
	  $allow_search = false;
	  if(in_array($this->session->userdata("mb_deptno"),array(24)) || in_array($this->session->userdata("mb_no"),array(114,229))) {
	    $allow_search = true;
	  }
	  
	  $curDate = new DateTime();
	  $curYear = $curDate->format("Y");
	  $initYear = 2015;
	  if($initYear < $curYear-1) {
	    $initYear = $curYear-1;
	  }
	  
	  $this->view_template('leave/balances', 'Leave', array(
			'breadcrumbs' => array('Manage','Balances'),
			'js' => array(
					'jquery.dataTables.min.js',
					'jquery.dataTables.bootstrap.js',
					'jquery.validate.min.js',
					'leave.balances.js'
				),
			'depts' 		=> $this->employees_m->getDepts(),
			'emp_list'		=> $emp_list,
			'allow_search'	=> $allow_search,
		    'emp_dept'		=> $this->session->userdata("mb_deptno"),
		    'emp_id'		=> $this->session->userdata("mb_no"),
			'start_year'	=> $initYear,
			'end_year'		=> $curYear
	  ));
	}
	
	public function mc() {
	  $emp_list = $this->employees_m->getAll(false,"*",false);
	  $allow_search = false;
	  if(in_array($this->session->userdata("mb_deptno"),array(24)) || in_array($this->session->userdata("mb_no"),array(114,229))) {
	    $allow_search = true;
	  }
	  $this->view_template('leave/mc', 'Leave', array(
			'breadcrumbs' => array('Medical Certificates'),
			'js' => array(
					'jquery.dataTables.min.js',
					'jquery.dataTables.bootstrap.js',
					'jquery.validate.min.js',
					'leave.mc.js'
				),
			'depts' 		=> $this->employees_m->getDepts(),
			'emp_list'		=> $emp_list,
			'allow_search'	=> $allow_search,
		    'emp_dept'		=> $this->session->userdata("mb_deptno")
	  ));
	}
	
	
	/* End of Views */

	
	/* Approval */
	
	public function getAllForApproval() {
	  $post = $this->input->post();
	  $order_arr = array();
	  if(is_array($post['order'])){
		foreach($post['order'] as $orderDtl) {
		  $order_arr[$post['columns'][$orderDtl['column']]['data']] = $orderDtl['dir'];
		}
	  }
	  
	  $approver_depts = $this->leaves_m->getApproverGroup($this->session->userdata("mb_no"),"DISTINCT taga.lv_apprv_grp_id, taga.level");
	  $app_level = $lv_apprv_grp_id = 0;
	  $apprv_grp = "";
	  $apprv_level = "";
	  $search_str = "";
	  if(count($approver_depts)) {
	    foreach($approver_depts as $groups) {
		  $search_str .= (empty($search_str)?"":" OR ")."(tlaa.lv_apprv_grp_id = '".$groups->lv_apprv_grp_id."' AND tlaa.approved_level >= '".$groups->level."')";
		}
	  }
	  else {
	    $search_str .= "tlaa.approval_id < 0";
	  }
	  
	  if(!empty($post["status"])) {
	    $search_str = "(".$search_str.") AND tlaa.status = '".$post["status"]."'";
	  }
	  
	  $having_str = "";
	  if(!empty($post['search']['value'])) {
	    foreach($post['columns'] as $column) {
	      if($column['searchable'] == "true") {
		    $having_str .= (empty($having_str)?"":" OR ").$column['data']." LIKE '%".$post['search']['value']."%' ";
		  }
	    }
	  }
	  $having_str = empty($having_str)?$search_str:"(".$search_str.") AND (".$having_str.")";
	  
	  $select_str = " tlaa.approval_id, tlaa.lv_app_id, tlaa.lv_apprv_grp_id, tag .lv_group_code,
					tlaa.date_from, tlaa.date_to, tlc.leave_code, CONCAT(IF(sub.mb_3='Local',sub.mb_fname,sub.mb_nick),' ',sub.mb_lname) creator, CONCAT(IF(apprv.mb_3='Local',apprv.mb_fname,apprv.mb_nick),' ',apprv.mb_lname) approver,
					tlaa.approved_level, tlaa.status app_status, taga.level user_level,
					case tlaa.status
					  when '1' then 'For Approval'
					  when '2' then 'Rejected'
					  when '3' then 'Approved'
					  when '4' then 'Cancelled'
					end status_lbl";
	  
	  $data = $this->leaves_m->getAllForApprovalFiltered($select_str, $search_str);
	  $all_approval_count = count($data);
	  
	  $data_all = $this->leaves_m->getAllForApprovalFiltered($select_str, $having_str);
	  $all_filtered_count = count($data_all);
	  
	  $data = $this->leaves_m->getAllForApprovalFiltered($select_str, $having_str, $post['start'], $post['length'], $order_arr);
	  
	  echo json_encode(array("data"=>$data,
							"draw"=>(int)$post["draw"],
							"recordsTotal"=>$all_approval_count,
							"recordsFiltered"=>$all_filtered_count));
	  
	}

	public function approveLeave() {
	  $mb_no = $this->session->userdata("mb_no");
	  if($mb_no) {
		  $date 			= new DateTime();
		  $post 			= $this->input->post();
		  
		  $leave_app = $this->leaves_m->getEmpLeaveApplication("*",array("lv_app_id"=>$post['lv_app_id']));
		  if(count($leave_app)) {
			$date_from = new DateTime($leave_app[0]->date_from);
			$date_to = new DateTime($leave_app[0]->date_to);
			
			$date_from_tmp 	= new DateTime($date_from->format("Y-m-d 00:00:00"));
			$date_to_tmp 	= new DateTime($date_to->format("Y-m-d 00:00:00"));
			$total_days 	= $this->calculateActualLeaveDays($date_from_tmp,$date_to_tmp,$mb_no);
			$leave_bal 		= $this->leaves_m->getEmpLeaveBalances($leave_app[0]->mb_no,"*",$leave_app[0]->leave_id,$date_from->format("Y"));
		
			if(count($leave_bal)) {
				if($leave_bal[0]->bal - $total_days < 0) {
					echo json_encode(array("success"=>0,"msg"=>"Not enough leave credits.<br/>Leave Balance for <u>".$date_from->format("Y")."</u> : <b>".($leave_bal[0]->bal)."</b><br/>Filed Days : <b>".$total_days."</b>"));
					return;
				}
			}
			else {
				echo json_encode(array("success"=>0,"msg"=>"Not enough leave credits.<br/>Leave Balance for <u>".$date_from->format("Y")."</u> : <b>0</b><br/>Filed Days : <b>".$total_days."</b>"));
				return;
			}
		  }
		  else {
			echo json_encode(array("success"=>0,"msg"=>"Leave application does not exists."));
			return;
		  }
		  
		  $approver_dtl = $this->leaves_m->getAllForApprovalFiltered("tlaa.*","tlaa.lv_app_id = '".$post['lv_app_id']."'");
		  if(count($approver_dtl)) {
			  $approver_dtl 	= $this->leaves_m->getApprovalGroupApprover($post['grp_id'], "MIN(level) level", array("level >"=>$post['approval_level']));
			  
			  if(count($approver_dtl) && $approver_dtl[0]->level) {
				$success = $this->leaves_m->updateForApprovalLeaveApplication(array("approved_level"=>$approver_dtl[0]->level,"approved_by"=>$this->session->userdata("mb_no")), array("approval_id"=>$post['approval_id']));
				if($success) {
				 
				  $this->leaves_m->updateLeaveApplication(array("dirty_bit_ind"=>1), array("lv_app_id"=>$post['lv_app_id']));
				  $data			= array("lv_app_id"		=> $post['lv_app_id'],
									"status"			=> 3,
									"remarks"			=> "Approved",
									"created_by"		=> $this->session->userdata("mb_no"),
									"created_datetime"	=> $date->format("Y-m-d H:i:s"));
				  $success = $this->leaves_m->insertForApprovalLeaveApplicationHist($data);
				  
				  $leave_app = $this->leaves_m->getEmpLeaveApplication("*",array("lv_app_id"=>$post['lv_app_id']));
				  
				  // Notification - General
				  $this->load->model('notifications_model', 'notifications');
				  $this->notifications->create("application", 1, array("Leave","approved"), $leave_app[0]->mb_no,0,"leave/submit_leave");
				  
				  // Notification
				  // $this->ws->load('notifs');
				  $approver_dtl 	= $this->leaves_m->getApprovalGroupApprover($post['grp_id'], "taga.*", array("level"=>$approver_dtl[0]->level));
				  foreach($approver_dtl as $approver) {
					$recipient = $approver->mb_id;
					$date = new DateTime();
					NOTIFS::publish("APPROVAL_$recipient", array(
						'type' => "LV",
						'count' => 1
					));
				  }
				  $approver_dtl 	= $this->leaves_m->getApprovalGroupApprover($post['grp_id'], "taga.*", array("level"=>$post['approval_level']));
				  foreach($approver_dtl as $approver) {
					$recipient = $approver->mb_id;
					$date = new DateTime();
					NOTIFS::publish("APPROVAL_$recipient", array(
						'type' => "LV",
						'count' => -1
					));
				  }
				  //
				}
			  }
			  else {
				$success = $this->leaves_m->updateForApprovalLeaveApplication(array("status"=>3,"approved_by"=>$this->session->userdata("mb_no")), array("approval_id"=>$post['approval_id']));
				if($success) {
				 
				  $this->leaves_m->updateLeaveApplication(array("status"=>3,"dirty_bit_ind"=>1), array("lv_app_id"=>$post['lv_app_id']));
				  $data			= array("lv_app_id"		=> $post['lv_app_id'],
									"status"			=> 3,
									"remarks"			=> "Approved",
									"created_by"		=> $this->session->userdata("mb_no"),
									"created_datetime"	=> $date->format("Y-m-d H:i:s"));
				  $success = $this->leaves_m->insertForApprovalLeaveApplicationHist($data);
				  $leave_app = $this->leaves_m->getEmpLeaveApplication("*",array("lv_app_id"=>$post['lv_app_id']));
				
				  if(count($leave_app)){
				    $date_from = new DateTime($leave_app[0]->date_from);
				    $date_to = new DateTime($leave_app[0]->date_to);
				    $leave_bal = $this->leaves_m->getEmpLeaveBalances($leave_app[0]->mb_no,"*",$leave_app[0]->leave_id,$date_from->format("Y"));
				    $total_days = $this->calculateActualLeaveDays($date_from,$date_to,$leave_app[0]->mb_no);
				    $date_from = new DateTime($leave_app[0]->date_from);
				    $date_to = new DateTime($leave_app[0]->date_to);
				    //$days = (($date_to->format("U") - $date_from->format("U"))/86400)+1;
				    if(count($leave_bal)) {  
					  $this->leaves_m->updateEmpLeaveBalances(array("pending"=>($leave_bal[0]->pending * 1) - ($leave_app[0]->pending * 1)),array("leave_id"=>$leave_app[0]->leave_id,"mb_no"=>$leave_app[0]->mb_no,"year"=>$date_from->format("Y")));
				    }
				    $allocated 	= $total_days;
				    $used 		= 0;
				    while($date_from <= $date_to) {
					  $param		= array(
										"year"	=> $date_from->format("Y"),
										"month"	=> $date_from->format("n"),
										"day"	=> $date_from->format("j"),
										"mb_no"	=> $leave_app[0]->mb_no
								  );
					  $data		= array(
										"leave_id"	=> $leave_app[0]->leave_id,
										"lv_app_id"	=> $leave_app[0]->lv_app_id
								  );
					
					  $emp_sched = $this->shifts_m->getEmployeeSchedules("tms.*", $param);
					
					  if(count($emp_sched)) {
					    if(is_null($emp_sched[0]->shift_id) && (is_null($emp_sched[0]->lv_app_id) || $emp_sched[0]->lv_app_id == 0)) {
						  $this->shifts_m->updateMemberSchedule($data,$param);
					    }
					    else if($emp_sched[0]->shift_id > 0 && (is_null($emp_sched[0]->lv_app_id) || $emp_sched[0]->lv_app_id == 0)) {
						  $this->shifts_m->updateMemberSchedule($data,$param);
						  $allocated--;
						  $used++;
					    }
					  }
					  else
					    $this->shifts_m->insertMemberSchedule(array_merge($data,$param));
					  $date_from->modify("+1 day");
				    }
				    $this->leaves_m->updateLeaveApplication(array("pending"=>0,"allocated"=>$allocated,"used"=>$used), array("lv_app_id"=>$leave_app[0]->lv_app_id));
				    $leave_bal = $this->leaves_m->getEmpLeaveBalances($leave_app[0]->mb_no,"*",$leave_app[0]->leave_id,$date_from->format("Y"));
				    if(count($leave_bal)) { 
					  // if($leave_bal[0]->leave_code == "LWOP") {
					    // $this->leaves_m->updateEmpLeaveBalances(array("allocated"=>$leave_bal[0]->allocated+$allocated, "used"=>$leave_bal[0]->used+$used),array("leave_id"=>$leave_app[0]->leave_id,"mb_no"=>$leave_app[0]->mb_no));
					  // }
					  // else {
					    $this->leaves_m->updateEmpLeaveBalances(array("allocated"=>$leave_bal[0]->allocated+$allocated, "used"=>$leave_bal[0]->used+$used, "bal"=>$leave_bal[0]->bal-$used-$allocated),array("leave_id"=>$leave_app[0]->leave_id,"mb_no"=>$leave_app[0]->mb_no,"year"=>$date_from->format("Y")));
					  // }
					}
				  }
				
				  // Notification - General
				  $this->load->model('notifications_model', 'notifications');
				  $this->notifications->create("application", 1, array("Leave","approved"), $leave_app[0]->mb_no,0,"leave/submit_leave");
				  
				  // Notification
				  // $this->ws->load('notifs');
				  $approver_dtl 	= $this->leaves_m->getApprovalGroupApprover($post['grp_id'], "taga.*", array("level"=>$post['approval_level']));
				  foreach($approver_dtl as $approver) {
					$recipient = $approver->mb_id;
					$date = new DateTime();
					NOTIFS::publish("APPROVAL_$recipient", array(
						'type' => "LV",
						'count' => -1
					));
				  }
				  //
				}
			  }
			  echo json_encode(array("success"=>1,"msg"=>"Leave Approved!"));
		  }
		  else {
		    echo json_encode(array("success"=>0,"msg"=>"Leave application does not exists."));
		  }
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"You have been logged out. Please login again."));
	  }
	}
	
	public function rejectLeave() {
	  $mb_no = $this->session->userdata("mb_no");
	  if($mb_no) {
		$date = new DateTime();
		$post = $this->input->post();
		  
		$approver_dtl = $this->leaves_m->getAllForApprovalFiltered("tlaa.*","tlaa.lv_app_id = '".$post['lv_app_id']."'");
		if(count($approver_dtl)) {
		  $this->leaves_m->updateLeaveApplication(array("status"=>2, "dirty_bit_ind"=>1), array("lv_app_id"=>$post['lv_app_id']));
		  $success = $this->leaves_m->deleteForApprovalLeaveApplication(array("lv_app_id"=>$post['lv_app_id']));
		  if($success) {
		    
		    $request_dtl 	= $this->leaves_m->getEmpLeaveApplication("tla.*", array("tla.lv_app_id"=>$post['lv_app_id']));
		    $data			= array("lv_app_id"			=> $post['lv_app_id'],
								"status"			=> 2,
								"remarks"			=> empty($post['remarks'])?"Rejected":$post['remarks'],
								"created_by"		=> $this->session->userdata("mb_no"),
								"created_datetime"	=> $date->format("Y-m-d H:i:s"));
		    $success = $this->leaves_m->insertForApprovalLeaveApplicationHist($data);
			
		    $date_from = new DateTime($request_dtl[0]->date_from);
		    $date_to = new DateTime($request_dtl[0]->date_to);
		    $leave_bal = $this->leaves_m->getEmpLeaveBalances($request_dtl[0]->mb_no,"*",$request_dtl[0]->leave_id,$date_from->format("Y"));
		    $total_days = $this->calculateActualLeaveDays($date_from,$date_to,$request_dtl[0]->mb_no);
		    // $days = (($date_to->format("U") - $date_from->format("U"))/86400)+1;
			
		    if(count($leave_bal)) {  
			  $this->leaves_m->updateEmpLeaveBalances(array("pending"=>($leave_bal[0]->pending * 1) - $total_days),array("leave_id"=>$request_dtl[0]->leave_id,"mb_no"=>$request_dtl[0]->mb_no,"year"=>$date_from->format("Y")));
		    }
			
			// Notification - General
			$this->load->model('notifications_model', 'notifications');
			$this->notifications->create("application", 1, array("Leave","rejected"), $request_dtl[0]->mb_no,0,"leave/submit_leave");
			
			// Notification
			// $this->ws->load('notifs');
			$approver_dtl 	= $this->leaves_m->getApprovalGroupApprover($approver_dtl[0]->lv_apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->approved_level));
			foreach($approver_dtl as $approver) {
			  $recipient = $approver->mb_id;
			  $date = new DateTime();
			  NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "LV",
					'count' => -1
			  ));
			}
			//
			
		  }
		  echo json_encode(array("success"=>1,"msg"=>"Leave Rejected!"));
		}
	    else {
	      echo json_encode(array("success"=>0,"msg"=>"Leave application does not exists."));
	    }
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"You have been logged out. Please login again."));
	  }
	}

	/* End of Approval */
	
	/* Leave */
	
	public function getLeave($leave_id) {
	  $select_str = "tlc.*";
	  $data = $this->leaves_m->getLeave($leave_id,$select_str);
	  $dependents = $this->leaves_m->getDependentLeave($leave_id, "*");
	  $subleave = $this->leaves_m->getSubLeave($leave_id, "*");
	  $rules = $this->leaves_m->getLeaveRules($leave_id, "tlr.*,tlsc.sub_categ_code, tlsc.sub_categ_name");
	  echo json_encode(array("success"=>1,"data"=>$data,"dependents"=>$dependents,"subleave"=>$subleave,"rules"=>$rules));
	}
	
	public function getAllLeaves($inactive = 0) {
	  $post = $this->input->post();
	  $order_arr = array();
	  if(is_array($post['order'])){
		foreach($post['order'] as $orderDtl) {
		  $order_arr[$post['columns'][$orderDtl['column']]['data']] = $orderDtl['dir'];
		}
	  }
	  
	  $having_str = "";
	  if(!empty($post['search']['value'])) {
	    foreach($post['columns'] as $column) {
	      if($column['searchable'] == "true") {
		    $having_str .= (empty($having_str)?"":" OR ").$column['data']." LIKE '%".$post['search']['value']."%' ";
		  }
	    }
	  }
	  
	  $select_str = "l.*, IF(status,'Enabled','Disabled') enabled_lbl";
	  
	  
	  $data = $this->leaves_m->getAllLeaves($inactive);
	  $all_leaves_count = count($data);
	  
	  $data_all = $this->leaves_m->getAllLeavesFiltered($inactive, $select_str, $having_str);
	  $all_filtered_count = count($data_all);
	  
	  $data = $this->leaves_m->getAllLeavesFiltered($inactive, $select_str, $having_str, $post['start'], $post['length'], $order_arr);
	  
	  echo json_encode(array("data"=>$data,
							"draw"=>(int)$post["draw"],
							"recordsTotal"=>$all_leaves_count,
							"recordsFiltered"=>$all_filtered_count));
	}
	
	public function getLeaves($inactive=0) {
	  $data = $this->leaves_m->getAllLeaves($inactive);
	  echo json_encode($data);
	}
	
	public function getSubLeaves() {
	  $leave_id = $this->input->post("lv_id");
	  $subleave = array();
	  if($leave_id)
	    $subleave = $this->leaves_m->getSubLeave($leave_id, "*");
	  echo json_encode($subleave);
	}
	
	public function insertLeave() {
	  $date 				= new DateTime();
	  $post 				= $this->input->post();
	  $data					= array();
	  
	  $leave_code			= $post['add-leave-code'];
	  $leave_status			= $post['add-leave-status'];
	  $leave_name			= $post['add-leave-name'];
	  $leave_desc			= $post['add-leave-desc'];
	  $local_expat			= $post['add-leave-le'];
	  $gender				= $post['add-leave-gender'];
	  $staggered			= $post['add-leave-staggered'];
	  $full_consume			= $post['add-leave-full-consume'];
	  $forfeit				= $post['add-leave-forfeit'];
	  $emp_type				= $post['add-emp-type'];
	  $req_mc				= $post['add-leave-req-mc'];
	  $max_advanced_days    = $post['add-max-advanced-days'];
	  $has_entitlement		= isset($post['leave-entitlement-enabled'])?1:0;
	  $has_sub_category		= isset($post['leave-sub-category-enabled'])?1:0;
	  $has_leave_dependency	= isset($post['leave-dependencies-enabled'])?1:0;
	  $entitlement_type 	= isset($post['add-leave-entitlement-type'])?$post['add-leave-entitlement-type']:"";
	  $has_rules			= isset($post['leave-rules-enabled'])?1:0;
	  $is_manual_entitlement = $is_fixed_entitlement = 
	  $is_computed_entitlement = 0;
	  if($has_entitlement) {
	    switch($entitlement_type) {
		  case "manual"	 : $is_manual_entitlement = 1;
		                 break;
		  case "fixed"	 : $is_fixed_entitlement = 1;
		                   $fixed_entitlement =$post['add-leave-entitlement-credit']*1;
						   $data['fixed_entitlement'] = $fixed_entitlement;
					     break;
	      case "formula" : $is_computed_entitlement = 1;
						   $start_date = $post['add-leave-entitlement-date'];
						   $adjustment = $post['add-leave-entitlement-add-date']*1;
						   $total_days = $post['add-leave-entitlement-divisor']*1;
						   $max_entitlement = $post['add-leave-max-entitlement']*1;
						   $data['start_date'] 		= $start_date;
						   $data['adjustment_day'] 	= $adjustment;
						   $data['total_days'] 		= $total_days;
						   $data['max_entitlement'] = $max_entitlement;
						 break;
		}
	  }
	  
	  $leave_dtl = $this->leaves_m->getAllLeavesFiltered(true,"l.leave_id, l.leave_code, l.leave_name", "l.leave_code = '".$leave_code."' AND l.leave_name = '".$leave_name."'");
	  
	  if(count($leave_dtl)) { 
	    echo json_encode(array("success"=>0,"msg"=>"Leave Code/Name already exists!"));
		return;
	  }
	  else {
	    $data['leave_code'] 				= $leave_code;
	    $data['leave_name'] 				= $leave_name;
	    $data['leave_desc'] 				= $leave_desc;
	    $data['local_expat'] 				= $local_expat;
	    $data['gender'] 					= $gender;
	    $data['staggered'] 					= $staggered;
	    $data['full_consume'] 				= $full_consume;
	    $data['forfeit_excess'] 			= $forfeit;
	    $data['has_sub_category'] 			= $has_sub_category;
	    $data['has_leave_dependency'] 		= $has_leave_dependency;
	    $data['has_entitlement'] 			= $has_entitlement;
		$data['has_rules'] 					= $has_rules;
	    $data['is_manual_entitlement'] 		= $is_manual_entitlement;
	    $data['is_fixed_entitlement'] 		= $is_fixed_entitlement;
	    $data['is_computed_entitlement'] 	= $is_computed_entitlement;
	    $data['status'] 					= $leave_status;
		$data['emp_type'] 					= $emp_type;
		$data['req_mc'] 					= $req_mc;
		$data['max_advanced_days']    		= $max_advanced_days;
	    $success = $this->leaves_m->insertLeave($data);
		
		if($success) {
		  $leave_dtl = $this->leaves_m->getAllLeavesFiltered(false,"*","leave_code = '".$data['leave_code'] ."'");
		
		  $leave_id = $leave_dtl[0]->leave_id;
		  if($has_sub_category) {
		    $sub_categ_codes = $this->input->post("add-subcateg-codes");
		    if($sub_categ_codes != null) {
		      $sub_categ_names 	= $this->input->post("add-subcateg-names");
			  $sub_categ_mcs 	= $this->input->post("add-subcateg-mcs");
		      foreach($sub_categ_codes as $ind=>$sub_categ_code) {
		        $sub_categ_name = $sub_categ_names[$ind];
				$sub_categ_mc 	= $sub_categ_mcs[$ind];
		        $categ_data = array(
						        "sub_categ_code"=>$sub_categ_code,
						        "sub_categ_name"=>$sub_categ_name,
								"req_mc"=>$sub_categ_mc,
						        "leave_id"=>$leave_id
				 	           );
		        $success = $this->leaves_m->insertSubLeave($categ_data);
		        if(!$success) break;
	          }
		    }
		  }
		  if($has_leave_dependency) {
		    $dependents = $this->input->post("add-dependencies-leaves");
		    if($dependents != null) {
		      foreach($dependents as $ind=>$dependent) {
		        $dependent_data = array(
						        "leave_id"=>$leave_id,
						        "dependent_id"=>$dependent
				 	           );
		        $success = $this->leaves_m->insertDependentLeave($dependent_data);
		        if(!$success) break;
	          }
		    }
		  }
		  if($has_rules) {
		    $add_days = $this->input->post("add-days");
		    if($add_days != null) {
		      $add_prior 		= $this->input->post("add-prior");
			  $add_later 		= $this->input->post("add-later");
			  $add_sub_categ 	= $this->input->post("add-sub-categs");
		      foreach($add_days as $ind=>$days) {
		        $rules_data = array(
						      "max_days"=>$days,
						      "days_prior"=>$add_prior[$ind],
						      "days_after"=>$add_later[$ind],
							  "sub_categ_id"=>$add_sub_categ[$ind],
							  "leave_id"=>$leave_id
					        );
			    $success = $this->leaves_m->insertLeaveRules($rules_data);
		        if(!$success) break;
			  }
		    }
		  }
		  if($success) {
		    echo json_encode(array("success"=>1,"msg"=>"Record Saved!"));
		  }
		  else {
		    echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
		  }
		}
		else {
		  echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
		}
	  }
	}
	
	public function updateLeave() {
	  $date 				= new DateTime();
	  $post 				= $this->input->post();
	  $data					= array();
	  
	  $leave_id			= $post['leave_id'];
	  $leave_code			= $post['add-leave-code'];
	  $leave_status			= $post['add-leave-status'];
	  $leave_name			= $post['add-leave-name'];
	  $leave_desc			= $post['add-leave-desc'];
	  $local_expat			= $post['add-leave-le'];
	  $gender				= $post['add-leave-gender'];
	  $staggered			= $post['add-leave-staggered'];
	  $full_consume			= $post['add-leave-full-consume'];
	  $forfeit				= $post['add-leave-forfeit'];
	  $emp_type				= $post['add-emp-type'];
	  $req_mc				= $post['add-leave-req-mc'];
	  $max_advanced_days    = $post['add-max-advanced-days'];
	  $has_entitlement		= isset($post['leave-entitlement-enabled'])?1:0;
	  $has_sub_category		= isset($post['leave-sub-category-enabled'])?1:0;
	  $has_leave_dependency	= isset($post['leave-dependencies-enabled'])?1:0;
	  $entitlement_type 	= isset($post['add-leave-entitlement-type'])?$post['add-leave-entitlement-type']:"";
	  $has_rules			= isset($post['leave-rules-enabled'])?1:0;
	  $is_manual_entitlement = $is_fixed_entitlement = 
	  $is_computed_entitlement = 0;
	  if($has_entitlement) {
	    switch($entitlement_type) {
		  case "manual"	 : $is_manual_entitlement = 1;
		                 break;
		  case "fixed"	 : $is_fixed_entitlement = 1;
		                   $fixed_entitlement =$post['add-leave-entitlement-credit']*1;
						   $data['fixed_entitlement'] = $fixed_entitlement;
					     break;
	      case "formula" : $is_computed_entitlement = 1;
						   $start_date = $post['add-leave-entitlement-date'];
						   $adjustment = $post['add-leave-entitlement-add-date']*1;
						   $total_days = $post['add-leave-entitlement-divisor']*1;
						   $max_entitlement = $post['add-leave-max-entitlement']*1;
						   $data['start_date'] 		= $start_date;
						   $data['adjustment_day'] 	= $adjustment;
						   $data['total_days'] 		= $total_days;
						   $data['max_entitlement'] = $max_entitlement;
						 break;
		}
	  }
	  
	  $leave_dtl = $this->leaves_m->getAllLeavesFiltered(true,"l.leave_id, l.leave_code, l.leave_name", "l.leave_code = '".$leave_code."' AND l.leave_name = '".$leave_name."' AND l.leave_id <> '".$leave_id."'");
	  
	  if(count($leave_dtl)) { 
	    echo json_encode(array("success"=>0,"msg"=>"Leave Code/Name already exists!"));
		return;
	  }
	  else {
	    $data['leave_code'] 				= $leave_code;
	    $data['leave_name'] 				= $leave_name;
	    $data['leave_desc'] 				= $leave_desc;
	    $data['local_expat'] 				= $local_expat;
	    $data['gender'] 					= $gender;
	    $data['staggered'] 					= $staggered;
	    $data['full_consume'] 				= $full_consume;
	    $data['forfeit_excess'] 			= $forfeit;
	    $data['has_sub_category'] 			= $has_sub_category;
	    $data['has_leave_dependency'] 		= $has_leave_dependency;
	    $data['has_entitlement'] 			= $has_entitlement;
		$data['has_rules'] 					= $has_rules;
	    $data['is_manual_entitlement'] 		= $is_manual_entitlement;
	    $data['is_fixed_entitlement'] 		= $is_fixed_entitlement;
	    $data['is_computed_entitlement'] 	= $is_computed_entitlement;
	    $data['status'] 					= $leave_status;
		$data['emp_type'] 					= $emp_type;
		$data['req_mc'] 					= $req_mc;
		$data['max_advanced_days']    		= $max_advanced_days;
		$param['leave_id']					= $leave_id;
		
	    $success = $this->leaves_m->updateLeave($data, $param);
		
		if($success) {
		
		  if($has_sub_category) {
		    $sub_categ_list 		= isset($post['add-subcateg-codes'])?$post['add-subcateg-codes']:array();
		    $sub_categ_del_list 	= isset($post['sub_categs_del_arr'])?$post['sub_categs_del_arr']:array();
		
		    $sub_categ_dtl 			= $this->leaves_m->getSubLeave($leave_id,"*, GROUP_CONCAT(sub_categ_code) categs");
	  
		    $org_categs 			= explode(",",$sub_categ_dtl[0]->categs);
	        $for_insert_categs 		= array_diff($sub_categ_list, $org_categs);
	  
			$sub_categ_names = $this->input->post("add-subcateg-names");
			$sub_categ_mcs 	= $this->input->post("add-subcateg-mcs");			
		    foreach($sub_categ_del_list as $categ_id) {
	          $this->leaves_m->deleteSubLeave(array("sub_categ_id"=>$categ_id));
		    }

		    foreach($for_insert_categs as $ind=>$sub_categ_code) {
			   $this->leaves_m->insertSubLeave(array("sub_categ_code"=>$sub_categ_code,
			   "sub_categ_name"=>$sub_categ_names[$ind],
			   "req_mc"=>$sub_categ_mcs[$ind],
			   "leave_id"=>$leave_id));
		    }
		  }
		  
		  if($has_leave_dependency) {
		    $dependents_list 					= isset($post['add-dependencies-leaves'])?$post['add-dependencies-leaves']:array();
		    $dependents_del_list 				= isset($post['dependents_del_arr'])?$post['dependents_del_arr']:array();
		
		    $dependents_dtl 					= $this->leaves_m->getDependentLeave($leave_id,"*, GROUP_CONCAT(dependent_id) dependents");
	  
		    $org_dependents 					= explode(",",$dependents_dtl[0]->dependents);
	        $for_insert_dependents 				= array_diff($dependents_list, $org_dependents);

		    
			foreach($dependents_del_list as $leave_dep_id) {
	          $this->leaves_m->deleteDependentLeave(array("leave_dep_id"=>$leave_dep_id));
		    }

		    foreach($for_insert_dependents as $ind=>$dependent_id) {
			   $this->leaves_m->insertDependentLeave(array(
						"dependent_id"=>$dependent_id,
						"leave_id"=>$leave_id));
		    }
		  }
		  
		  
		  if($has_rules) {
		    $add_prior_list 					= isset($post['add-prior'])?$post['add-prior']:array();
		    $add_later 							= isset($post['add-later'])?$post['add-later']:array();
			$add_sub_categ 						= isset($post['add-sub-categs'])?$post['add-sub-categs']:array();
		  
		    $rules_list 						= isset($post['add-days'])?$post['add-days']:array();
		    $rules_del_list 					= isset($post['rules_del_arr'])?$post['rules_del_arr']:array();
		  
		    $rules_dtl 							= $this->leaves_m->getLeaveRules($leave_id,"*, GROUP_CONCAT(max_days) max_days");
		    $for_insert_rules 					= $rules_list;
		  
		   foreach($rules_del_list as $rule_id) {
	          $this->leaves_m->deleteLeaveRules(array("rule_id"=>$rule_id));
		    }

		    foreach($for_insert_rules as $ind=>$max_days) {
			   $this->leaves_m->insertLeaveRules(array(
						"max_days"		=> $max_days,
						"days_prior"	=> $add_prior_list[$ind],
						"days_after"	=> $add_later[$ind],
						"sub_categ_id"	=> $add_sub_categ[$ind],
						"leave_id"		=> $leave_id));
		    }
		  }
		  
		  if($success) {
		    echo json_encode(array("success"=>1,"msg"=>"Record Saved!"));
		  }
		  else {
		    echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
		  }
		}
		else {
		  echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
		}
	  }
	}
	
	/* End of Leave */
	
	/* Approval Groups */
	
	public function getApprovalGroup($apprv_id) {
	  $select_str = "tag.*, ".
					"IF(enabled,'Enabled','Disabled') enabled_lbl";
	  $data = $this->leaves_m->getApprovalGroup($apprv_id,$select_str);
	  $data[0]->approvers = $this->leaves_m->getApprovalGroupApprover($apprv_id, "mb_3, taga.level, taga.mb_id, gm.mb_nick, gm.mb_fname, gm.mb_lname");
	  $emp = $this->employees_m->getAll(false,"*",false);
	  echo json_encode(array("success"=>1,"data"=>$data,"emp"=>$emp));
	}
	
	public function getAllApprovalGroups($inactive = 0) {
	  $post = $this->input->post();
	  $order_arr = array();
	  if(is_array($post['order'])){
		foreach($post['order'] as $orderDtl) {
		  $order_arr[$post['columns'][$orderDtl['column']]['data']] = $orderDtl['dir'];
		}
	  }
	  
	  $having_str = "";
	  if(!empty($post['search']['value'])) {
	    foreach($post['columns'] as $column) {
	      if($column['searchable'] == "true") {
		    $having_str .= (empty($having_str)?"":" OR ").$column['data']." LIKE '%".$post['search']['value']."%' ";
		  }
	    }
	  }
	  
	  $select_str = "tag.*, ".
					"IF(tag.enabled,'Enabled','Disabled') enabled_lbl";
	  
	  $data = $this->leaves_m->getAllApprovalGroups($inactive,$select_str);
	  $all_approvers_count = count($data);
	  
	  $data_all = $this->leaves_m->getAllApprovalGroupsFiltered($inactive, $select_str, $having_str);
	  $all_filtered_count = count($data_all);
	  
	  $data = $this->leaves_m->getAllApprovalGroupsFiltered($inactive, $select_str, $having_str, $post['start'], $post['length'], $order_arr);
	  
	  echo json_encode(array("data"=>$data,
							"draw"=>(int)$post["draw"],
							"recordsTotal"=>$all_approvers_count,
							"recordsFiltered"=>$all_filtered_count));
	}
	
	public function insertApprovalGroup() {
	  $date					= new DateTime();
	  $post 				= $this->input->post();
	  $grp_code 			= $post['add-apprv-grp-code'];
	  $enabled 				= $post['add-apprv-grp-status'];
	  $approver_list		= isset($post['add-approver'])?$post['add-approver']:array();
	  $approver_lvl_list	= isset($post['add-approver_lvl'])?$post['add-approver_lvl']:array();
	  
	  $app_group_dtl		= $this->leaves_m->getAllApprovalGroupsFiltered(true,"tag.lv_apprv_grp_id", array("lv_group_code"=>$grp_code));
	  
	  if(count($app_group_dtl)) {
	    echo json_encode(array("success"=>0,"msg"=>"Group Code already exists!"));
		return;
	  }
	  else {
	    $success = $this->leaves_m->insertApprovalGroup(array("lv_group_code"=>$grp_code, "enabled"=>$enabled, "created_datetime"=>$date->format("Y-m-d H:i:s"), "created_by"=>$this->session->userdata("mb_no"), "updated_datetime"=>$date->format("Y-m-d H:i:s"),"updated_by"=>$this->session->userdata("mb_no")));
	    if($success) {
		  $app_group_dtl	= $this->leaves_m->getAllApprovalGroupsFiltered(true,"tag.lv_apprv_grp_id", array("lv_group_code"=>$grp_code));
		  $grp_id = $app_group_dtl[0]->lv_apprv_grp_id;
		  foreach($approver_list as $key=>$approver) {
	        $this->leaves_m->insertApprovalGroupApprovers(array("lv_apprv_grp_id"=>$grp_id, "mb_id"=>$approver, "level"=>$approver_lvl_list[$key]));
	      }
		  echo json_encode(array("success"=>1,"msg"=>"Record Saved!"));
		}
		else {
		  echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
		}
	  }
	}

	public function updateApprovalGroup() {
	  $date						= new DateTime();
	  $post 					= $this->input->post();
	  $grp_id 					= $post['apprv-grp-id'];
	  $grp_code 				= $post['apprv-grp-code'];
	  $enabled 					= $post['apprv-grp-status'];
	  $approver_list			= isset($post['approver'])?$post['approver']:array();
	  $approver_lvl_list		= isset($post['approver_lvl'])?$post['approver_lvl']:array();
	  $approver_del_list		= isset($post['approver_del_arr'])?$post['approver_del_arr']:array();
	  
	  $app_group_dtl			= $this->leaves_m->getApprovalGroup($grp_id,"*");
	  $approver_dtl 			= $this->leaves_m->getApprovalGroupApprover($grp_id,"*, GROUP_CONCAT(taga.mb_id) approvers");
	  
	  $org_approvers 			= explode(",",$approver_dtl[0]->approvers);
	  $for_deletion_approvers 	= array_diff($approver_del_list, $approver_list);
	  $for_insert_approvers		= array_diff($approver_list, $org_approvers);
	  
	  $app_group_dtl		= $this->leaves_m->getAllApprovalGroupsFiltered(true,"tag.lv_apprv_grp_id", array("lv_group_code"=>$grp_code));
	  
	  if(count($app_group_dtl) && $app_group_dtl[0]->lv_apprv_grp_id != $grp_id) {
	    echo json_encode(array("success"=>0,"msg"=>"Group Code already exists!"));
		return;
	  }
	  
	  $success = $this->leaves_m->updateApprovalGroup(array("lv_group_code"=>$grp_code, "enabled"=>$enabled, "updated_datetime"=>$date->format("Y-m-d H:i:s"),"updated_by"=>$this->session->userdata("mb_no")), array("lv_apprv_grp_id"=>$grp_id));

	  foreach($for_insert_approvers as $key=>$approver) {
	    $this->leaves_m->insertApprovalGroupApprovers(array("lv_apprv_grp_id"=>$grp_id, "mb_id"=>$approver, "level"=>$approver_lvl_list[$key]));
	  }
	  
	  foreach($for_deletion_approvers as $approver) {
	    $this->leaves_m->deleteApprovalGroupApprovers(array("lv_apprv_grp_id"=>$grp_id, "mb_id"=>$approver));
	  }
	  if($success)
	    echo json_encode(array("success"=>1, "msg"=>"Record updated!"));
	  else
	    echo json_encode(array("success"=>0, "msg"=>"A database error occured. Please contact system administrator."));
	}
	
	public function deleteApprovalGroup() {
	  $post = $this->input->post();
	  $grp_id = $post['apprv_id'];
	  
	  $this->leaves_m->deleteApprovalGroup(array("lv_apprv_grp_id"=>$grp_id));
	  $this->leaves_m->deleteApprovalGroupApprovers(array("lv_apprv_grp_id"=>$grp_id));
	  
	  echo json_encode(array("success"=>1));
	}
	
	public function getApprovalGroupFields() {
	  $emp = $this->employees_m->getAll(false,"*",false);
	  echo json_encode(array("success"=>1,"emp"=>$emp));
	}

	/* End of Approval Groups */
	
	/* Settings */
	
	public function updateGeneralSettings() {
	  $post = $this->input->post();
	  $resp = $this->leaves_m->updateGeneralSettings(array("max_pending_leave"=>$post['max_pending']),array());
	  if($resp){
	    echo json_encode(array("success"=>1,"msg"=>"Record updated"));
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"A database error occured"));
	  }
	}

	/* End of Settings */
	
	/* Leave Filing */
	
    public function getEmpLeaveBalancesGrid() {
	  $mb_no = $this->input->post("emp");
	  $year = $this->input->post("year");
	  $employee = (object) $this->employees_m->get($mb_no);
	  $leaves_arr = $this->leaves_m->getAllLeaves(false,"*");
	  $response_arr = array("Leave", "Leave Name", "Balance", "Pending", "Allocated", "Used", "Paid");
	  $widths_arr = array(50,170,70,70,70,50,50);	
	  $return_arr = array();
	  
	  foreach($leaves_arr as $leave) {
		if(($leave->gender != "b" && $leave->gender != strtolower($employee->mb_sex)) || ($leave->local_expat != "b" && $leave->local_expat != strtolower(substr($employee->mb_3,0,1)))) {
		  continue;
		}

	    $emp_balance 		= $this->leaves_m->getEmpLeaveBalances($mb_no,"*",$leave->leave_id,$year);
		
		$leave->bal 		= (count($emp_balance))?$emp_balance[0]->bal*1:0;
		$leave->used 		= (count($emp_balance))?$emp_balance[0]->used*1:0;
		$leave->allocated 	= (count($emp_balance))?$emp_balance[0]->allocated*1:0;
		$leave->pending		= (count($emp_balance))?$emp_balance[0]->pending*1:0;
		$leave->paid		= (count($emp_balance))?$emp_balance[0]->paid*1:0;
		if($leave->bal || $leave->used || $leave->allocated || $leave->pending) {
		  $emp_data = array(
							"leave_code"=>$leave->leave_code,
							"leave_name"=>$leave->leave_name,
							"balance"=>(count($emp_balance))?$emp_balance[0]->bal*1:0,
							"pending"=>(count($emp_balance))?$emp_balance[0]->pending*1:0,
							"allocated"=>(count($emp_balance))?$emp_balance[0]->allocated*1:0,
							"used"=>(count($emp_balance))?$emp_balance[0]->used*1:0,
							"paid"=>(count($emp_balance))?$emp_balance[0]->paid*1:0
						  );
	      $return_arr[] = $emp_data;
		}
	  }
	  
	  $awol_record = $this->att_m->getAllAWOL("a.*,gm.mb_nick", "mb_no ='".$mb_no."' AND is_awol ='1' AND YEAR(att_date) = '".$year."'");
	  $el_record = $this->att_m->getAllAWOL("a.*,gm.mb_nick", "mb_no ='".$mb_no."' AND is_el ='1' AND YEAR(att_date) = '".$year."'");
	  
	   echo json_encode(array("data"=>$return_arr, "header"=>$response_arr, "width"=>$widths_arr, "total_count"=>count($return_arr)?1:0, "page" => 1, "awol_cnt"=>count($awol_record), "el_cnt"=>count($el_record)));
	}
	
	public function getEmpLeavesGrid($inactive = 0) {
	  $date = new DateTime();
	  $post = $this->input->post();
	  $limit = $post["limit"];
	  $page = $post["page"];
	  $offset = ($page - 1) * $limit;
	  $mb_no = $this->session->userdata("mb_no");
	  $year = $post["year"];
	  
	  $having_str = "tla.mb_no = '".$mb_no."' AND (YEAR(tla.date_from) = '".$year."' OR YEAR(tla.date_to) = '".$year."')";
	  $response_arr = array("Request ID", "Date From", "Date To", "Type", "Allocated", "Used", "For Approval", "Status", "Action");
	  $widths_arr = array(80, 90,90,60,65,50,100,80,108);
	  
	  $select_str = "*, DATE(tla.date_from) date_from, DATE(tla.date_to) date_to, CASE tla.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END lv_status_lbl, tla.status lv_status";
	  
	  
	  $data = $this->leaves_m->getEmpLeaveApplication("*", $having_str);
	  $all_leaves_count = count($data);
	  
	  $data_all = $this->leaves_m->getEmpLeaveApplication($select_str, $having_str, $offset, $limit, array("lv_app_id"=>"Desc"));
	  
	  $return_arr = array();
	  $pending_count = 0;
	  foreach($data_all as $leave) {
	    // if(in_array($leave->lv_status,array(0,1,2)) && ($leave->leave_id == 3 || ($leave->leave_id == 1 && $leave->sub_categ_id == 2)))
	      // $pending_count++;
		$date_from 	= new DateTime($leave->date_from." 00:00:00");
		$date_to 	= new DateTime($leave->date_to." 23:59:59");
		
		$allow_view = false;
		$allow_edit = false;
		$allow_submit = false;
		$allow_delete = false;
		$allow_cancel = false;
		
		$att = $this->att_m->getAllAttendanceFiltered("*","att_date BETWEEN '".$leave->date_from."' AND '".$leave->date_to."' AND mb_no = '".$mb_no."'");
		
		switch($leave->lv_status) {
		  case 0 :
				$allow_edit = true;
				$allow_submit = true;
				$allow_delete = true;
				break;
		  case 1 :
				$allow_view = true;
				if ($leave->dirty_bit_ind) { $allow_cancel = true; } else { $allow_delete = true; }
		        break;
		  case 2 :
		        // $allow_edit = true;
				// $allow_submit = true;
				// $allow_cancel = true;
				$allow_view = true;
				break;
		  case 3 :
		        $allow_view = true;
				if($date_from > $date || count($att))
				  $allow_cancel = true;
				break;
		  case 4 :
				$allow_view = true;
				break;
		}
		
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
						  "date_from"	=> $leave->date_from,
						  "date_to"		=> $leave->date_to,
						  "leave_code"	=> $leave->leave_code,
						  "allocated"	=> $leave->allocated,
						  "used"		=> $leave->used,
						  "approver"	=> $approver,
						  "status_lbl"	=> $leave->lv_status_lbl,
						  "action"		=> '<div class="action-buttons">'.
											  ($allow_view?'<a class="green request-view" href="#" data-id="'.$leave->lv_app_id.'" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>':'').
											  /*($allow_edit?'<a class="blue request-edit" href="#" data-id="'.$leave->lv_app_id.'" title="Edit"><i class="ace-icon fa fa-edit bigger-130"></i></a>':'').*/
											  /*($allow_submit?'<a class="green request-submit" href="#" data-id="'.$leave->lv_app_id.'" title="Submit"><i class="ace-icon fa fa-share-square-o bigger-130"></i></a>':'').*/
											  ($allow_delete?'<a class="red request-remove" href="#" data-id="'.$leave->lv_app_id.'" title="Delete"><i class="ace-icon fa fa-trash-o bigger-130"></i></a>':'').
											  ($allow_cancel?'<a class="red request-cancel" href="#" data-id="'.$leave->lv_app_id.'" title="Cancel"><i class="ace-icon fa fa-close bigger-130"></i></a>':'').
											'</div>'
						);
	  }
	  
	  echo json_encode(array("data"=>$return_arr, "header"=>$response_arr, "width"=>$widths_arr, "total_count"=>$all_leaves_count, "pending_count"=>$pending_count, "page" => $page));
	}
	
	public function saveLeaveApplication() {
	  $mb_no = $this->session->userdata("mb_no");
	  $post	= $this->input->post();
	  if($mb_no) {
	    if($post['lv-type'] == 3 || ($post['lv-type'] == 1 && $post['lv-sub-type'] == 2)) {
	      if($post['lv-type'] == 3) {
		    $param = array(
							"tla.mb_no"		=>$this->session->userdata("mb_no"),
							"tla.status < " => 2,
							"tla.leave_id"		=> 3 //VL
						  );
			if(!empty($post['request-id']))
			  $param['tla.lv_app_id <> '] = $post['request-id'];
	        $leaves 		= $this->leaves_m->getEmpLeaveApplication("tla.*",$param);
	        $leaves_count = count($leaves);
		  }
		  else if($post['lv-type'] == 1 && $post['lv-sub-type'] == 2) {
		    $param = array(
							"tla.mb_no"		=>$this->session->userdata("mb_no"),
							"tla.status < " => 2,
							"tla.leave_id"		=> 1, //AL
							"tla.sub_categ_id"		=> 2 //AL
						  );
			if(!empty($post['request-id']))
			  $param['tla.lv_app_id <> '] = $post['request-id'];
		    $leaves 		= $this->leaves_m->getEmpLeaveApplication("tla.*",$param);
	        $leaves_count = count($leaves);
		  }
		  
		  $leave_set 	= $this->leaves_m->getGeneralSettings();
	      $max_pending_leave = $leave_set[0]->max_pending_leave;
		  // Temporary only for filing one leave
	      if($leaves_count >= $max_pending_leave && $max_pending_leave > 0) {
	        echo json_encode(array("success"=>0,"msg"=>"Cannot file more than ".$max_pending_leave." leave".($max_pending_leave>1?"s":"").". Please notify supervisor."));
		    return;
		  }
		}
		
	    $mb_no 				= $this->session->userdata("mb_no");
	    $lv_app_id			= $post['request-id'];
	    $leave_id			= $post['lv-type'];
	    $leave_categ_id 	= $post['lv-sub-type'];
	    $date_from_tmp		= $post['lv-date-from'];
	    $date_to_tmp		= $post['lv-date-to'];
	    $reason				= $post['reason'];
	    $mc_control			= $post['lv-mc'];
		
	    $emp_dtl = $this->employees_m->get($mb_no);
	    $apprv_grp_id = 0;
	    if(count($emp_dtl)) {
	      $apprv_grp_id = $emp_dtl['mb_lv_app_grp_id'];
	    }

	    $date = new DateTime();
	  
	    $date_from = new DateTime($date_from_tmp." 00:00:00");
	    $date_to = new DateTime($date_to_tmp." 00:00:00");
		
		$from_year = $date_from->format("Y");
		$to_year = $date_to->format("Y");
		
		if($from_year != $to_year) {
		  echo json_encode(array("success"=>0,"msg"=>"Invalid Dates. Kindly file <b>different years</b> separately."));
		  return false;
		}
		
		if($date_to < $date_from) {
		  echo json_encode(array("success"=>0,"msg"=>"Invalid Dates. <b>Date From</b> must be <b><i>earlier</i></b> than <b>Date To</b>."));
		  return false;
		}
		
		$leave_code = $this->leaves_m->getLeave($leave_id, "*");
		if(!count($leave_code)) {
		  echo json_encode(array("success"=>0,"msg"=>"Invalid Leave Code. Please reload page."));
		  return false;
		}
		
		$tmp_leave_code = $leave_code;
		if($leave_categ_id) {
		  $tmp_leave_code = $this->leaves_m->getSubLeave($leave_id, "*",array("sub_categ_id"=>$leave_categ_id));
		  if(!count($tmp_leave_code)) {
		    echo json_encode(array("success"=>0,"msg"=>"Invalid Leave Code. Please reload page."));
		    return false;
		  }
		}
		
		/* December filing - Need to have setup */
		$dec_allow_from = new DateTime("2015-11-01 00:00:00");
		$dec_allow_to = new DateTime("2015-11-07 23:59:59");
		$dec_first_day = new DateTime("2015-12-01 00:00:00");
		$dec_last_day = new DateTime("2015-12-31 23:59:59");
		
		if(
			(($dec_first_day <= $date_from && $date_from <= $dec_last_day) || ($dec_first_day <=  $date_to && $date_to  <= $dec_last_day)) &&
			(($leave_code[0]->leave_code == "AL" && $tmp_leave_code[0]->sub_categ_code == "AL") ||
			($leave_code[0]->leave_code == "VL"))
		) {
			if($date < $dec_allow_from || $dec_allow_to < $date) {
				echo json_encode(array("success"=>0,"msg"=>"Cannot file for the month of <b>December</b>. Can only file from <b>".$dec_allow_from->format("Y-m-d")."</b> to <b>".$dec_allow_to->format("Y-m-d")."</b>"));
				return false;
			}
		}
		/* End of December */
		
		$med_cert = array();
		if($tmp_leave_code[0]->req_mc) {
		  if($mc_control) {
		    $med_cert = $this->leaves_m->getMC("*","tlm.control_id = '".$mc_control."' AND tlm.mb_no = '".$mb_no."' AND tlm.lv_app_id = 0");
			if(!count($med_cert)) {
			  echo json_encode(array("success"=>0,"msg"=>"Medical Certificate Number is <b>not valid</b>."));
		      return false;
			}
		  }
		  else{
		    echo json_encode(array("success"=>0,"msg"=>"Medical Certificate Number is <b>not valid</b>."));
		    return false;
		  }
		}
		
		/* need to have this as config to the system */
		$allow_cny = false;
		if(strtolower($emp_dtl['mb_3']) == "expat" && $leave_code[0]->leave_code == "AL" && $tmp_leave_code[0]->sub_categ_code == "AL") {
			
			$cny_batch1_from = new DateTime("2016-01-31 00:00:00");
			$cny_batch1_to = new DateTime("2016-02-10 23:59:59");
			$cny_batch2_from = new DateTime("2016-02-11 00:00:00");
			$cny_batch2_to = new DateTime("2016-02-21 23:59:59");
			
			$cny_allowed_date_from = new DateTime("2015-10-15 00:00:00");
			$cny_allowed_date_to = new DateTime("2015-10-30 23:59:59");
			
			$today = new DateTime();
			if(($cny_batch1_from <= $date_from && $date_from <= $cny_batch2_to) || ($cny_batch1_from <= $date_to && $date_to <= $cny_batch2_to)) {
				if($cny_allowed_date_from <= $today && $today <= $cny_allowed_date_to && $today->format("Y") < $cny_batch1_from->format("Y") && $today->format("Y") != $date_from->format("Y") ) {
					/*if($cny_batch1_from <= $date_from && $date_from <= $cny_batch2_to &&
					   $cny_batch1_from <= $date_to && $date_to <= $cny_batch2_to) {*/
					if((($cny_batch1_from <= $date_from && $date_from <= $cny_batch1_to && $cny_batch1_from <= $date_to && $date_to <= $cny_batch1_to) || ($cny_batch2_from <= $date_from && $date_from <= $cny_batch2_to && $cny_batch2_from <= $date_to && $date_to <= $cny_batch2_to)) || in_array($emp_dtl['mb_deptno'],array(22))) {
						$allow_cny = true;
					}
					else {
						echo json_encode(array("success"=>0,"msg"=>"Chinese New Year leave is not valid.<br/>Batch 1 leave dates <b>from</b> and <b>to</b> should be between <b>".$cny_batch1_from->format("Y-m-d")."</b> and <b>".$cny_batch1_to->format("Y-m-d")."</b>.<br/>Batch 2 leave dates <b>from</b> and <b>to</b> should be between <b>".$cny_batch2_from->format("Y-m-d")."</b> and <b>".$cny_batch2_to->format("Y-m-d")."</b>."));
						return false;
					}
				}
				else if($today->format("Y") == $cny_batch1_from->format("Y") && (($cny_batch1_from <= $date_from && $date_from <= $cny_batch2_to) || ($cny_batch1_from <= $date_to && $date_to <= $cny_batch2_to) || ( $date_from <= $cny_batch1_from && $cny_batch1_from <= $date_to) || ( $date_from <= $cny_batch2_to && $cny_batch2_to <= $date_to))) {
					echo json_encode(array("success"=>0,"msg"=>"Chinese New Year leave is already closed for filing.<br/>Please ask HR for any inquiries."));
					return false;
				}
			}
		}
		
		/* ends here */
		
		if(!$allow_cny && $leave_code[0]->max_advanced_days) {
		  $today = new DateTime();
		  $max_date  = new DateTime($today->format("Y-m-d 23:59:59"));
		  $max_date->modify("+".$leave_code[0]->max_advanced_days." days");
		  
		  if($date_from > $max_date) {
		    echo json_encode(array("success"=>0,"msg"=>"Maximum of <b>".$leave_code[0]->max_advanced_days." days</b> are allowed for future leaves."));
		    return false;
		  }
		}
		
		$where = array();
		if($leave_categ_id)
		  $where = array("tlsc.sub_categ_id"=>$leave_categ_id);
		
	    $leave_rules = $this->leaves_m->getLeaveRules($leave_id, "*", $where);
		
	    if(count($leave_rules) && $leave_code[0]->has_rules) {
	      $max_days	= 0;
	      $days_prior = 0;
		  $days_later = 0;
		  $total_days = $this->calculateActualLeaveDays($date_from,$date_to,$this->session->userdata("mb_no"));
		  if($total_days < 1) {
		    echo json_encode(array("success"=>0,"msg"=>"Cannot process. You do not have working schedule on the filed dates."));
			return false;
		  }
	      $date_from = new DateTime($date_from_tmp." 00:00:00");
	      foreach($leave_rules as $rule) {
	        if($rule->max_days) {
		      if($rule->max_days <= $total_days && $rule->max_days > $max_days) {
			    $max_days = $rule->max_days;
			    $days_prior = $rule->days_prior;
			    $days_later = $rule->days_after;
			  }
		    }
		    else if($max_days==0) {
		      $days_prior = $rule->days_prior;
			  $days_later = $rule->days_after;
		    }
		  }
		  if($days_prior) {
		    $date_from->modify("-".$days_prior." day");
		    if($date_from->format("Ymd") < $date->format("Ymd")) {
		      echo json_encode(array("success"=>0,"msg"=>"Cannot process. Must be filed <b>".$days_prior." days(s) before</b>."));
		      return false;
		    }
		  }
		  if($days_later) {
		    $date_from->modify("+".$days_later." day");
		    if($date_from->format("Ymd") < $date->format("Ymd") || $date_from->format("Ymd") > $date->format("Ymd")) {
		      echo json_encode(array("success"=>0,"msg"=>"Cannot process. Must be filed <b>".$days_later." days(s) later</b>."));
		      return false;
		    }
		  }
		  $date_from = new DateTime($date_from_tmp." 00:00:00");
	    }
	  
	    $record = $this->leaves_m->getEmpLeaveApplication("*",  "gm.mb_no = '".$mb_no."' AND (date_from BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."' OR date_to BETWEEN '".$date_from->format("Y-m-d")."' AND '".$date_to->format("Y-m-d")."' OR '".$date_from->format("Y-m-d")."' BETWEEN date_from AND date_to OR '".$date_to->format("Y-m-d")."' BETWEEN date_from AND date_to) AND tla.status NOT IN (2,4) AND tla.lv_app_id <> '".$lv_app_id."'");
	  
	    if(count($record)) {
	      echo json_encode(array("success"=>0,"msg"=>"Already filed leave on dates specified. Please review."));
		  return false;
	    }
	    else {
		  // Check total number of days vs balance
	      $date_from_tmp 	= new DateTime($date_from->format("Y-m-d 00:00:00"));
		  $date_to_tmp 		= new DateTime($date_to->format("Y-m-d 00:00:00"));
	      $total_days 		= $this->calculateActualLeaveDays($date_from_tmp,$date_to_tmp,$mb_no);
		  $leave_bal 		= $this->leaves_m->getEmpLeaveBalances($mb_no,"*",$leave_id,$from_year);
		
		  if(count($leave_bal)) {
		    if($leave_bal[0]->bal - $leave_bal[0]->pending < $total_days) {
		      echo json_encode(array("success"=>0,"msg"=>"Not enough leave credits.<br/>Leave Balance for <u>".$from_year."</u> : <b>".($leave_bal[0]->bal - $leave_bal[0]->pending)."</b><br/>Filed Days : <b>".$total_days."</b>"));
			  return;
			}
		  }
		  else {
		    echo json_encode(array("success"=>0,"msg"=>"Not enough leave credits.<br/>Leave Balance for <u>".$from_year."</u> : <b>0</b><br/>Filed Days : <b>".$total_days."</b>"));
			return;
		  }
		
		  // Check the approvers
		  $approver2_dtl = $this->leaves_m->getApprovalGroupApprover($apprv_grp_id,"*",array("taga.mb_id"=>$mb_no));
		  if(count($approver2_dtl))
		    $approver_dtl 	= $this->leaves_m->getApprovalGroupApprover($apprv_grp_id, "MIN(level) level", array("level >"=>$approver2_dtl[0]->level));
		  else
		    $approver_dtl 	= $this->leaves_m->getApprovalGroupApprover($apprv_grp_id, "MIN(level) level");
		
		  // Insert
		  $total_days = $this->calculateActualLeaveDays($date_from,$date_to,$mb_no);
		  $success = $this->leaves_m->insertLeaveApplication(array(
												"mb_no"				=> $mb_no,
												"status" 			=> 1,
												"lv_apprv_grp_id"	=> $apprv_grp_id,
												"date_from"			=> $date_from->format("Y-m-d"),
												"date_to"			=> $date_to->format("Y-m-d"),
												"leave_id"			=> $leave_id,
												"sub_categ_id"		=> $leave_categ_id*1,
												"reason"			=> $reason,
												"control_id"		=> $mc_control,
												"pending"			=> $total_days,
												"created_datetime"	=> $date->format("Y-m-d H:i:s"),
												"submitted_datetime"=>$date->format("Y-m-d H:i:s")
											));
		  $lv_app_id = $this->leaves_m->lastID();
		  
		  if(count($med_cert))
			$this->leaves_m->updateMC(array("lv_app_id" => $lv_app_id), array("mc_id" => $med_cert[0]->mc_id));
	    }

	    if($success) {
		  if(count($approver_dtl) && $approver_dtl[0]->level) {
		    $data 			= array("lv_app_id"			=> $lv_app_id,
									"lv_apprv_grp_id"	=> $apprv_grp_id,
									"date_from"			=> $date_from->format("Y-m-d"),
									"date_to"			=> $date_to->format("Y-m-d"),
									"approved_level"	=> $approver_dtl[0]->level,
									"submitted_by"		=> $mb_no,
									"status"			=> 1,
									"created_by"		=> $mb_no,
									"created_datetime"	=> $date->format("Y-m-d H:i:s"),
									"updated_by"		=> $mb_no,
									"updated_datetime"	=> $date->format("Y-m-d H:i:s"));
		    $success 		= $this->leaves_m->insertForApprovalLeaveApplication($data);
			if($success) {
			  // Notification
			  $this->ws->load('notifs');
			  $approver_dtl 	= $this->leaves_m->getApprovalGroupApprover($apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->level));
			  foreach($approver_dtl as $approver) {
			    $recipient = $approver->mb_id;
			    $date = new DateTime();
			    NOTIFS::publish("APPROVAL_$recipient", array(
				  'type' => "LV",
				  'count' => 1
				));
			  }
			  //
			  $data			= array("lv_app_id"			=> $lv_app_id,
									"status"			=> 1,
									"remarks"			=> "Submitted for approval",
									"created_by"		=> $mb_no,
									"created_datetime"	=> $date->format("Y-m-d H:i:s"));
			  $success = $this->leaves_m->insertForApprovalLeaveApplicationHist($data);
		      if(count($leave_bal))
				$this->leaves_m->updateEmpLeaveBalances(array("pending" => ($leave_bal[0]->pending * 1) + $total_days), array("leave_id" => $leave_id, "mb_no" => $mb_no, "year" => $from_year));
		      else
				$this->leaves_m->insertEmpLeaveBalances(array("pending" => $total_days, "leave_id" => $leave_id, "mb_no" => $mb_no, "year" => $from_year));
		    }
		  }
		  else {
		    $this->leaves_m->updateLeaveApplication(array("status" => 3, "pending" => 0, "dirty_bit_ind"=>1), array("lv_app_id" => $lv_app_id));
		    $allocated 	= $total_days;
		    $used 		= 0;
		    while($date_from <= $date_to) {
		      $param	= array(
							"year"	=> $date_from->format("Y"),
							"month"	=> $date_from->format("n"),
							"day"	=> $date_from->format("j"),
							"mb_no"	=> $mb_no
						  );
			  $data	= array(
							"leave_id"	=> $leave_id,
							"lv_app_id"	=> $lv_app_id
			              );
			
			  $emp_sched = $this->shifts_m->getEmployeeSchedules("tms.*", $param);
			  if(count($emp_sched))
			    if(is_null($emp_sched[0]->shift_id))
			      $this->shifts_m->updateMemberSchedule($data,$param);
			    else if($emp_sched[0]->shift_id > 0) {
				  $this->shifts_m->updateMemberSchedule($data,$param);
			      $allocated--;
				  $used++;
			    }
			  else
		        $this->shifts_m->insertMemberSchedule(array_merge($data,$param));
			  $date_from->modify("+1 day");
		    }
		    $this->leaves_m->updateLeaveApplication(array("allocated" => $allocated, "used" => $used), array("lv_app_id" => $lv_app_id));
		    $leave_bal = $this->leaves_m->getEmpLeaveBalances($mb_no, "*", $leave_id,$from_year);
		    if(count($leave_bal))
			  $this->leaves_m->updateEmpLeaveBalances(array("allocated"=>$leave_bal[0]->allocated + $allocated, "used"=>$leave_bal[0]->used + $used, "bal"=>$leave_bal[0]->bal - $used - $allocated),array("leave_id" => $leave_id, "mb_no" => $mb_no, "year" => $from_year));
		  }
		  echo json_encode(array("success"=>1,"msg"=>"Leave Application Submitted!"));
	    }
	    else
		  echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
	  }
	  else
	    echo json_encode(array("success"=>0,"msg"=>"You have been logged out. Please reload the page."));
	}
	
	public function deleteLeaveApplication() {
	  $request_id 	= $this->input->post("request_id");
	  $request_dtl 	= $this->leaves_m->getEmpLeaveApplication("tla.*, tlc.*, DATE(tla.date_from) date_from, DATE(tla.date_to) date_to, tla.status", array("tla.lv_app_id"=>$request_id));
	  if(count($request_dtl)) {
	    $param 		= array("lv_app_id" => $request_id);
	    $date_from = new DateTime($request_dtl[0]->date_from);
	    $date_to = new DateTime($request_dtl[0]->date_to);
	    $leave_bal = $this->leaves_m->getEmpLeaveBalances($request_dtl[0]->mb_no,"*",$request_dtl[0]->leave_id,$date_from->format("Y"));
		$total_days = $this->calculateActualLeaveDays($date_from,$date_to,$this->session->userdata("mb_no"));
	    // $days = (($date_to->format("U") - $date_from->format("U"))/86400)+1;
	
	    if(count($leave_bal)) {  
	      if(in_array($request_dtl[0]->status,array(1,3))) {
	        if($request_dtl[0]->status == 3) {
			  // if($request_dtl[0]->leave_code != "LWOP") {
		        $set_arr = array("used"=>($leave_bal[0]->used * 1) - ($request_dtl[0]->used * 1), "allocated"=>($leave_bal[0]->allocated * 1) - ($request_dtl[0]->allocated * 1), "bal"=>($leave_bal[0]->bal * 1) + ($request_dtl[0]->used * 1) + ($request_dtl[0]->allocated * 1));
			  // }
			  // else {
			    // $set_arr = array("used"=>($leave_bal[0]->used * 1) - ($request_dtl[0]->used * 1), "allocated"=>($leave_bal[0]->allocated * 1) - ($request_dtl[0]->allocated * 1));
			  // }
	        }
	        else if($request_dtl[0]->status == 1) {
		      $set_arr = array("pending"=>($leave_bal[0]->pending * 1) - ($request_dtl[0]->pending * 1));
	        }
	        $this->leaves_m->updateEmpLeaveBalances($set_arr, array("leave_id"=>$request_dtl[0]->leave_id,"mb_no"=>$request_dtl[0]->mb_no,"year"=>$date_from->format("Y")));
	      }
	    }

	  
	    if($request_dtl[0]->dirty_bit_ind)
		  $success  	= $this->leaves_m->updateLeaveApplication(array("pending"=>0,"status"=>4),$param);
	    else
	      $success  	= $this->leaves_m->deleteLeaveApplication($param);
		  
	    $this->leaves_m->updateMC(array("lv_app_id"=>0),array("lv_app_id"=>$request_id));
	    if($success) {
		  $approver_dtl = $this->leaves_m->getAllForApprovalFiltered("tlaa.*","tlaa.lv_app_id = '".$request_id."'");
	      $success = $this->leaves_m->deleteForApprovalLeaveApplication($param);
		  if($success && count($approver_dtl)) {
			// Notification
			$this->ws->load('notifs');
			$approver_dtl 	= $this->leaves_m->getApprovalGroupApprover($request_dtl[0]->lv_apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->approved_level));
			foreach($approver_dtl as $approver) {
			  $recipient = $approver->mb_id;
			  $date = new DateTime();
			  NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "LV",
					'count' => -1
				));
			}
			//
		  }
	      echo json_encode(array("success"=>1,"msg"=>"Leave Application Deleted!"));
	    }
	    else {
	      echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
	    }
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"Leave application does not exists. Please refresh."));
	  }
	}
	
	public function getLeaveApplication() {
	  $lv_app_id = $this->input->post("request_id");
	  $data = $this->leaves_m->getEmpLeaveApplication("*, DATE(tla.date_from) date_from, DATE(tla.date_to) date_to", "tla.lv_app_id = '".$lv_app_id."'");
	  $remarks = $this->leaves_m->getEmpLeaveApplicationRemarks("tlah.*,CONCAT(IF(gm.mb_3='Local',gm.mb_fname,gm.mb_nick),' ',gm.mb_lname) mb_nick", "tlah.lv_app_id = '".$lv_app_id."'");
	  $leave_types = $this->getLeaveTypePerEmployee(false);
	  echo json_encode(array("success"=>1,"data"=>$data, "remarks"=>$remarks, "leave_types"=>$leave_types));
	}
	
	public function cancelLeaveApplication() {
	  $request_id 	= $this->input->post("request_id");
	  $remarks = $this->input->post("remarks");
	  $request_dtl 	= $this->leaves_m->getEmpLeaveApplication("tla.*, tla.status, DATE(tla.date_from) date_from, DATE(tla.date_to) date_to", "tla.lv_app_id = '".$request_id."'");
	  if(count($request_dtl)) {
	    $param 			= array("lv_app_id" => $request_id);
	    $date			= new DateTime();
	    $success 		= $this->leaves_m->updateLeaveApplication(array("status"=>4), $param);
		$this->leaves_m->updateMC(array("lv_app_id"=>0),array("lv_app_id"=>$request_id));
	    $data			= array("lv_app_id"			=> $request_id,
							"status"			=> 4,
							"remarks"			=> "Cancelled - ".$remarks,
							"created_by"		=> $this->session->userdata("mb_no"),
							"created_datetime"	=> $date->format("Y-m-d H:i:s"));
	    $success = $this->leaves_m->insertForApprovalLeaveApplicationHist($data);
	  
        $date_from = new DateTime($request_dtl[0]->date_from);
	    $date_to = new DateTime($request_dtl[0]->date_to);
	    $leave_bal = $this->leaves_m->getEmpLeaveBalances($request_dtl[0]->mb_no,"*",$request_dtl[0]->leave_id,$date_from->format("Y"));
		$total_days = $this->calculateActualLeaveDays($date_from,$date_to,$this->session->userdata("mb_no"));
	    // $days = (($date_to->format("U") - $date_from->format("U"))/86400)+1;
		
	    if(count($leave_bal)) {
		  if(in_array($request_dtl[0]->status,array(1,3))) {
		    if($request_dtl[0]->status == 3) {
		      // if($leave_bal[0]->leave_code != "LWOP") 
			    $set_arr = array("allocated"=>($leave_bal[0]->allocated * 1) - $request_dtl[0]->allocated, "used"=>($leave_bal[0]->used * 1) - $request_dtl[0]->used, "bal"=>($leave_bal[0]->bal * 1) + ($request_dtl[0]->used * 1) + ($request_dtl[0]->allocated * 1));
			  // else
			    // $set_arr = array("allocated"=>($leave_bal[0]->allocated * 1) - $request_dtl[0]->allocated, "used"=>($leave_bal[0]->used * 1) - $request_dtl[0]->used);
		    }
		    else if($request_dtl[0]->status == 1) {
		      $set_arr = array("pending"=>($leave_bal[0]->pending * 1) - $total_days);
		    }
		    $this->leaves_m->updateEmpLeaveBalances($set_arr, array("leave_id"=>$request_dtl[0]->leave_id,"mb_no"=>$request_dtl[0]->mb_no,"year"=>$date_from->format("Y")));
		  }
	    }
	  
	    $this->shifts_m->updateMemberSchedule(array("leave_id"=>0,"lv_app_id"=>0),array("lv_app_id"=>$request_dtl[0]->lv_app_id,"mb_no"=>$request_dtl[0]->mb_no));
	    if($success) {
		  $approver_dtl = $this->leaves_m->getAllForApprovalFiltered("tlaa.*","tlaa.lv_app_id = '".$request_id."'");
	      $success = $this->leaves_m->deleteForApprovalLeaveApplication($param);
		  if($success && count($approver_dtl)) {
			// Notification
			$this->ws->load('notifs');
			$approver_dtl 	= $this->leaves_m->getApprovalGroupApprover($request_dtl[0]->lv_apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->approved_level));
			foreach($approver_dtl as $approver) {
			  $recipient = $approver->mb_id;
			  $date = new DateTime();
			  NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "LV",
					'count' => -1
				));
			}
			//
		  }
	      echo json_encode(array("success"=>1,"msg"=>"Leave Application Cancelled!"));
	    }
	    else {
	      echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
	    }
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"Leave application does not exists. Please refresh."));
	  }
	}
	
	public function getLeaveTypePerEmployee($display=true) {
	  $mb_no = $this->session->userdata("mb_no");
	  $year = $this->input->post("year");
	  $employee = (object) $this->employees_m->get($mb_no);
	  $leaves_arr = $this->leaves_m->getAllLeaves(false,"*");
	  
	  $return_arr = array();
	  foreach($leaves_arr as $leave) {
	  
	    $subcateg = $this->leaves_m->getSubLeave($leave->leave_id,"*");
	  
	    $invalid_gender = ($leave->gender != "b" && $leave->gender != strtolower($employee->mb_sex));
		$invalid_emp_type = ($leave->local_expat != "b" && $leave->local_expat != strtolower(substr($employee->mb_3,0,1)));
		
		$leaves_balance = $this->leaves_m->getEmpLeaveBalances($mb_no,"*",$leave->leave_id,$year);
		$no_balance = false;
		if($leave->has_entitlement) {
		  if(count($leaves_balance)) {
		    if($leaves_balance[0]->bal)
			  $no_balance = false;
			else
			  $no_balance = true;
		  }
		  else
		    $no_balance = true;
		}
		
		if($invalid_gender || $invalid_emp_type || $no_balance) {
		  continue;
		}
		$leave->subs = array();
		if(count($subcateg)) {
		  foreach($subcateg as $sub) {
		    $leave->subs[] =$sub;
		  }
		}
		
		$return_arr[] = $leave;
	  }
	  if($display)
	    echo json_encode(array("success"=>1, "data"=>$return_arr));
	  else
	    return $return_arr;
	}
	
	/* End of Leave Filing */
	
	/* Leave Balances */
	
	public function getAllLeaveBalances() {
	  $limit = $this->input->post("limit");
	  $page = $this->input->post("page");
	  $dept_id = $this->input->post("department");
	  $mb_no = $this->input->post("emp");
	  $emp_stat = $this->input->post("emp_type");
	  $year = $this->input->post("year");
	  $offset = ($page - 1) * $limit;
	
	  $date_from = $this->input->post("dateFrom");
	  $date_from = new DateTime($date_from." 00:00:00");
	  
	  $date_to = $this->input->post("dateTo");
	  $date_to = new DateTime($date_to." 00:00:00");
	  $type 	= $this->input->post("type");
		  
	  if($mb_no) {
	    $where_arr = array();
	    if($mb_no)
		  $where_arr["mb_no"] = $mb_no;
		$employees = $this->employees_m->getAll(false, "*, CASE mb_employment_status WHEN 1 THEN 'Probationary' WHEN 2 THEN 'Confirmed' END employment_label", 0, 0,0,array(),$where_arr);;
		$total_count = count($employees);
	  }
	  else {
	    $where_arr = array();
	    if($type)
		  $where_arr["mb_3"] = $type;
		if($emp_stat)
		  $where_arr["mb_employment_status"] = $emp_stat;
		$employees = $this->employees_m->getAll(false, "*, CASE mb_employment_status WHEN 1 THEN 'Probationary' WHEN 2 THEN 'Confirmed' END employment_label", $dept_id, 0,0,array(),$where_arr);
		$total_count = count($employees);
	  
		$employees = $this->employees_m->getAll(false, "*, CASE mb_employment_status WHEN 1 THEN 'Probationary' WHEN 2 THEN 'Confirmed' END employment_label", $dept_id, 0,0, array("d.dept_name"=>"ASC","mb_lname"=>"ASC"),$where_arr);
	  }
	  
	  $response_arr = $return_arr = array();
	  $response_arr = array("ID", "Name", "Department");
	  $leaves_arr 	= $this->leaves_m->getAllLeaves(false,"*");
	  foreach($leaves_arr as $leave) {
	    $response_arr[] = $leave->leave_name;
	  }
	  $response_arr[] = "Action";
	  foreach($employees as $employee) {
	    $tmp_date = new DateTime($date_from->format("Y-m-d H:i:s"));
		$emp_data = array(
							"mb_id"=>$employee->mb_id,
							"mb_nick"=>$employee->mb_lname.", ".($employee->mb_3 =="Expat"?$employee->mb_nick:$employee->mb_fname),
							"dept_name"=>$employee->dept_name
						  );
	    foreach($leaves_arr as $leave) {
		  $emp_balance = $this->leaves_m->getEmpLeaveBalances($employee->mb_no,"*",$leave->leave_id, $year);
		  $balance = (count($emp_balance))?$emp_balance[0]->bal:0;

	      $emp_data[$leave->leave_code] = $balance;
	    }
		
		$emp_data["action"]			= '<div class="action-buttons">'.
								        '<a class="blue request-edit" href="#" data-id="'.$employee->mb_no.'" data-year="'.$year.'" title="edit"><i class="ace-icon fa fa-edit bigger-130"></i></a>'.
									  '</div>';
		$return_arr[] = $emp_data;
	  }
	  echo json_encode(array("data"=>$return_arr, "header"=>$response_arr, "total_count"=>$total_count, "page" => $page));
	}
	
	public function getEmpLeaveBalances() {
	  $mb_no = $this->input->post("emp_id");
	  $year = $this->input->post("year");
	  $employee = (object) $this->employees_m->get($mb_no);
	  $leaves_arr = $this->leaves_m->getAllLeaves(false,"*");
	  
	  $awol_record = $this->att_m->getAllAWOL("a.*,gm.mb_nick", "mb_no ='".$mb_no."' AND is_awol ='1' AND YEAR(att_date) = '".$year."'");
	  $el_record = $this->att_m->getAllAWOL("a.*,gm.mb_nick", "mb_no ='".$mb_no."' AND is_el ='1' AND YEAR(att_date) = '".$year."'");
	  
	  foreach($leaves_arr as $leave) {
		if(($leave->gender != "b" && $leave->gender != strtolower($employee->mb_sex)) || ($leave->local_expat != "b" && $leave->local_expat != strtolower(substr($employee->mb_3,0,1)))) {
		  continue;
		}

	    $emp_balance 		= $this->leaves_m->getEmpLeaveBalances($mb_no,"*",$leave->leave_id,$year);
		$leave->bal 		= (count($emp_balance))?$emp_balance[0]->bal*1:0; 
		$leave->used 		= (count($emp_balance))?$emp_balance[0]->used*1:0;
		$leave->pending		= (count($emp_balance))?$emp_balance[0]->pending*1:0;
		$leave->allocated	= (count($emp_balance))?$emp_balance[0]->allocated*1:0;
		$leave->paid		= (count($emp_balance))?$emp_balance[0]->paid*1:0;
		$leave->forfeited	= (count($emp_balance))?$emp_balance[0]->forfeited*1:0;
	    $return_arr[] = $leave;
	  }
	  
	  $return_arr[]		= (object) array("leave_id"=>"", "leave_code"=>"T-EL", "leave_name"=>"Tagged as EL", "bal"=>"", "used"=>count($el_record), "pending"=>"", "allocated"=>"", "paid"=>"", "forfeited"=>"");
	  $return_arr[]		= (object) array("leave_id"=>"", "leave_code"=>"T-AWoL", "leave_name"=>"Tagged as AWoL", "bal"=>"", "used"=>count($awol_record), "pending"=>"", "allocated"=>"", "paid"=>"", "forfeited"=>"");
	  
	  $employee = array(0 => (object) $this->employees_m->get($mb_no));
	  echo json_encode(array("data"=>$return_arr, "success"=>1, "emp"=>$employee, "year"=>$year));
	}
	
	public function saveLeaveBalance() {
	  $post			= $this->input->post();
	  $mb_no        = $post['mb-no'];
	  $year        	= $post['lv-year'];
	  $bal_arr      = $post['leave_bal'];
	  $paid_arr     = $post['leave_paid'];
	  $forfeit_arr  = $post['leave_forfeit'];
	  $leave_arr    = $post['leave_id'];
	  foreach($leave_arr as $key=>$leave_id) {
	    $bal_dtl = $this->leaves_m->getEmpLeaveBalances($mb_no,"*",$leave_id,$year);
		if(count($bal_dtl)) {
		  $data 	= array("bal"=>$bal_arr[$key], "paid"=>$paid_arr[$key], "forfeited"=>$forfeit_arr[$key]);
		  $param 	= array("mb_no"=>$mb_no,"leave_id"=>$leave_id,"year"=>$year);
		  $success = $this->leaves_m->updateEmpLeaveBalances($data,$param);
		}
		else {
		  $data 	= array("bal"=>$bal_arr[$key], "paid"=>$paid_arr[$key], "forfeited"=>$forfeit_arr[$key], "mb_no"=>$mb_no, "leave_id"=>$leave_id,"year"=>$year);
		  $success = $this->leaves_m->insertEmpLeaveBalances($data);
		}
	  }
	  
	  if($success) {
		echo json_encode(array("success"=>1,"msg"=>"Record Saved!"));
	  }
	  else {
		echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
	  }
	}
	
	public function exportBalances() {
	  $this->load->library('excel');
	  $activeSheetInd = -1;
	  $file_name = "Leave Balances Report.xls";
	  $headers = array("ID","Name","Nickname","Department","Local/Expat","Status");
	  $leave_types = $this->leaves_m->getAllLeaves(false,'*',false); // Do not include ASL - Total AL only for expat
	  foreach($leave_types as $leave) {
	    $headers[] = $leave->leave_code;
	  }
	  
	  $dept_id 	= $this->input->post("export-dept");
	  $mb_no 	= $this->input->post("export-emp");
	  $emp_stat = $this->input->post("export-emp-stat");
	  $type 	= $this->input->post("export-type");
	  $year 	= $this->input->post("export-year");

	  $dateToday = new DateTime();
	  
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
						      'font'=> array('bold'=>true, 'size'=> "10"));
	  
	  if($mb_no) {
	    $where_arr = array();
	    if($mb_no)
		  $where_arr["mb_no"] = $mb_no;
	    $emp_record = $this->employees_m->getAll(false, "*, CASE mb_employment_status WHEN 1 THEN 'Probationary' WHEN 2 THEN 'Confirmed' END employment_label", 0, 0,0,array(),$where_arr);;
		if(count($emp_record))
		  $employees = $emp_record;
		else 
		  $employees = array();
		$total_count = count($employees);
	  }
      else {
	    $where_arr = array();
	    if($type)
		  $where_arr["mb_3"] = $type;
		if($emp_stat)
		  $where_arr["mb_employment_status"] = $emp_stat;
		$employees = $this->employees_m->getAll(false, "*, CASE mb_employment_status WHEN 1 THEN 'Probationary' WHEN 2 THEN 'Confirmed' END employment_label", $dept_id, 0,0,array(),$where_arr);
		$total_count = count($employees);
	  
		$employees = $this->employees_m->getAll(false, "*, CASE mb_employment_status WHEN 1 THEN 'Probationary' WHEN 2 THEN 'Confirmed' END employment_label", $dept_id, 0,0, array("d.dept_name"=>"ASC","mb_employment_status"=>"ASC","mb_lname"=>"ASC"),$where_arr);
	  }
	  
	  $activeSheet = $this->excel->setActiveSheetIndex(++$activeSheetInd);
	  if(count($employees)) {
		$row = 0;  
		$c = 0;
		$row++;
		$column = PHPExcel_Cell::stringFromColumnIndex($c);
		$cell=$column.$row;
		$column2 = PHPExcel_Cell::stringFromColumnIndex($c+17);
		$cell2=$column2.$row;
		$activeSheet->mergeCells($cell.":".$cell2); 
		$activeSheet->setCellValue($cell, "Leave Balance Summary for ".$year);
		$activeSheet->getStyle($cell.":".$cell2)->applyFromArray($headerStyle);
		$row++;
		$cell=$column.$row;
		$column2 = PHPExcel_Cell::stringFromColumnIndex($c+17);
		$cell2=$column2.$row;
		$activeSheet->mergeCells($cell.":".$cell2); 
		$activeSheet->setCellValue($cell, "As of ".$dateToday->format("Y-m-d H:i"));
		$activeSheet->getStyle($cell.":".$cell2)->applyFromArray($headerStyle);
		$c=0;
		$row++;
		foreach($headers as $header){
		  $column = PHPExcel_Cell::stringFromColumnIndex($c++);
		  $cell = $column.$row;
		  $activeSheet->setCellValue($cell, $header);
	      $activeSheet->getStyle($cell)->applyFromArray($tblHeaderStyle);
		}
		
		foreach($employees as $employee) {
		  $row++;
	      $c = -1;
		  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		  $cell = $column.$row;
		  $activeSheet->setCellValue($cell, $employee->mb_id);
	      $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		  $cell = $column.$row;
		  $activeSheet->setCellValue($cell, $employee->mb_lname.", ".$employee->mb_fname);
	      $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		  $cell = $column.$row;
		  $activeSheet->setCellValue($cell, $employee->mb_nick);
	      $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		  $cell = $column.$row;
		  $activeSheet->setCellValue($cell, $employee->dept_name);
	      $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		  $cell = $column.$row;
		  $activeSheet->setCellValue($cell, ucfirst($employee->mb_3));
	      $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		  
		  $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		  $cell = $column.$row;
		  $activeSheet->setCellValue($cell, ucfirst($employee->employment_label));
	      $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
		  
		  
		  foreach($leave_types as $leave) {
		    $emp_balance = $this->leaves_m->getEmpLeaveBalances($employee->mb_no,"*",$leave->leave_id,$year);
		    $balance = (count($emp_balance))?$emp_balance[0]->bal:0;

	        $column = PHPExcel_Cell::stringFromColumnIndex(++$c);
		    $cell = $column.$row;
		    $activeSheet->setCellValue($cell, $balance);
	        $activeSheet->getStyle($cell)->applyFromArray($tblDataStyle);
	      }
		  
		}
	  }
	  else {
	    $activeSheet = $this->excel->setActiveSheetIndex(++$activeSheetInd);
	    $activeSheet->setCellValue("A1", "No Record Found");
	  }

	  $column_start='A';
	  $total_columns = count($headers);
	  for ($col = 0; $col<$total_columns; $col++) {
		$column_start = PHPExcel_Cell::stringFromColumnIndex($col);
		$activeSheet->getColumnDimension($column_start)->setAutoSize(true); 
		//$column_start++; 
	  } 
	  
	  $activeSheet = $this->excel->setActiveSheetIndex(0);
	  $activeSheet->setTitle('Leave Balance Summary Report');

      header('Content-Type: application/vnd.ms-excel');
      header('Content-Disposition: attachment;filename="' . $file_name . '"');
      header('Cache-Control: max-age=0');
      $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
      $objWriter->save('php://output');
	  
	}
	
	/* End of Leave Balances */
	
	/* Medical Certificates */
	
	public function getAllMC($inactive = 0) {
	  $post 	= $this->input->post();
	  $limit 	= $post["limit"];
	  $page 	= $post["page"];
	  $mb_no 	= $post["mb_no"];
	  $offset	= ($page - 1) * $limit;

	  $having_str = "";
	  if($mb_no)
	    $having_str = "tlm.mb_no = '".$mb_no."'";
	  $response_arr = array("ID", "Control Number", "Employee Name", "Department", "Submitted Date", "Received By", "Leave ID", "Action");
	  $widths_arr = array(60, 120,220,90,165,220,80,108);
	  
	  $select_str = "tlm.*, CONCAT(IF(gm.mb_3 = 'Local',gm.mb_fname,gm.mb_nick),' ',gm.mb_lname) mb_mc, d.dept_name, CONCAT(IF(creator.mb_3 = 'Local',creator.mb_fname,creator.mb_nick),' ',creator.mb_lname) creator ";
	  
	  
	  $data = $this->leaves_m->getMC("*",$having_str);
	  $all_leaves_count = count($data);
	  
	  $data_all = $this->leaves_m->getMC($select_str, $having_str, $offset, $limit, array("mc_id"=>"Desc"));
	  
	  $return_arr = array();
	  $pending_count = 0;
	  foreach($data_all as $mc) {
	    $return_arr[] = array(
						  "mc_id"			=> $mc->mc_id,
						  "control_id"		=> $mc->control_id,
						  "mb_name"			=> $mc->mb_mc,
						  "dept_name"		=> $mc->dept_name,
						  "date_submitted"	=> $mc->date_submitted,
						  "creator"			=> $mc->creator,
						  "lv_app_id"		=> ($mc->lv_app_id?$mc->lv_app_id:""),
						  "action"			=> '<div class="action-buttons">'.
											  (($mc->lv_app_id)?'<a class="green request-view" href="#" data-id="'.$mc->mc_id.'" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>':'').
											  (empty($mc->lv_app_id)?'<a class="blue request-edit" href="#" data-id="'.$mc->mc_id.'" title="View"><i class="ace-icon fa fa-edit bigger-130"></i></a>':'').
											  (empty($mc->lv_app_id)?'<a class="red request-remove" href="#" data-id="'.$mc->mc_id.'" title="Delete"><i class="ace-icon fa fa-trash-o bigger-130"></i></a>':'').
											'</div>'
						);
	  }
	  
	  echo json_encode(array("data"=>$return_arr, "header"=>$response_arr, "width"=>$widths_arr, "total_count"=>$all_leaves_count, "pending_count"=>$pending_count, "page" => $page));
	}
	
	public function saveMC() {
	  $date 	= new DateTime();
	  $mb_no 	= $this->session->userdata("mb_no");
	  $post		= $this->input->post();
	  if($mb_no) {
	    $success = false;
		$mc_no 		= $this->input->post("request-id");
		$control_no	= $this->input->post("control-no");
		$notes		= $this->input->post("notes");
		$mc_mb_no  	= $this->input->post("emp-no");
		$date_sub  	= $this->input->post("date-submitted");

		if($mc_no) {
		  $data 	= array("mb_no"				=> $mc_mb_no,
							"date_submitted"	=> $date_sub,
							"remarks"			=> $notes,
							"updated_datetime"	=> $date->format("Y-m-d H:i:s"),
							"updated_by"		=> $mb_no);
		  $param 	= array("mc_id"=>$mc_no,"control_id"=>$control_no);
		  $success = $this->leaves_m->updateMC($data,$param);
		}
		else {
		  $employee = (object) $this->employees_m->get($mc_mb_no);
		  $emp = explode("-", $employee->mb_id);
		  $data 	= array("mb_no"				=> $mc_mb_no,
							"date_submitted"	=> $date_sub,
							"remarks"			=> $notes,
							"created_datetime"	=> $date->format("Y-m-d H:i:s"),
							"created_by"		=> $mb_no,
							"updated_datetime"	=> $date->format("Y-m-d H:i:s"),
							"updated_by"		=> $mb_no,
							"lv_app_id"			=> 0);
		  
		  $success 	= $this->leaves_m->insertMC($data);
		  $mc_no 	= $this->leaves_m->lastID();
		  
		  $data 	= array("control_id"		=> "MC-".(str_pad($emp[1],4,"0",STR_PAD_LEFT))."-".(str_pad($mc_no,5,"0",STR_PAD_LEFT)));
		  $param 	= array("mc_id"				=> $mc_no,);
		  $success 	= $this->leaves_m->updateMC($data,$param);
		}
		
	    if($success) {
		  echo json_encode(array("success"=>1,"msg"=>"Record Saved!","mc_no"=>"MC-".(str_pad($emp[1],4,"0",STR_PAD_LEFT))."-".(str_pad($mc_no,5,"0",STR_PAD_LEFT))));
	    }
	    else {
		  echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
	    }
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"You have been logged out. Please reload the page."));
	  }
	}
	
	public function deleteMC() {
	  $request_id 	= $this->input->post("request_id");
	  $request_dtl 	= $this->leaves_m->getMC("tlm.*", array("tlm.mc_id"=>$request_id));
	  if(count($request_dtl)) {
	    $param 		= array("mc_id" => $request_id);
	    $success  	= $this->leaves_m->deleteMC($param);
		if($success) {
		  echo json_encode(array("success"=>1,"msg"=>"Medical Certificate Deleted!"));
		}
		else {
		  echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
		}
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"Medical Certificate does not exists. Please refresh."));
	  }
	}
	
	public function getMC() {
	  $mc_id = $this->input->post("request_id");
	  $data = $this->leaves_m->getMC("tlm.*", "tlm.mc_id = '".$mc_id."'");
	  echo json_encode(array("success"=>1,"data"=>$data));
	}
	
	/* End of Medical Certificates */
	
	private function calculateActualLeaveDays($date_from,$date_to,$mb_id) {
	  $days = 0;
	  $date_from_tmp = new DateTime($date_from->format("Y-m-d H:i:s"));
	  while($date_from_tmp <= $date_to) {
		$param	= array(
					"year"	=> $date_from_tmp->format("Y"),
					"month"	=> $date_from_tmp->format("n"),
					"day"	=> $date_from_tmp->format("j"),
					"mb_no"	=> $mb_id
				  );
		$emp_sched = $this->shifts_m->getEmployeeSchedules("tms.*", $param);
			
		if(count($emp_sched)) {
		  if((is_null($emp_sched[0]->shift_id) || $emp_sched[0]->shift_id > 0) && (is_null($emp_sched[0]->lv_app_id) || $emp_sched[0]->lv_app_id == 0)) {
			$days++;
		  }
		}
		else
		  $days++;
	    $date_from_tmp->modify("+1 day");
	  }
	  return $days;
	}
	
}
?>