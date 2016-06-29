<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Overtime extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('employees_model', 'employees_m');
		$this->load->model('overtime_model', 'overtime_m');
		
		$this->load->library('ws', array('tag' => getenv('HTTP_HOST') == '10.120.10.139' ? 'hris' : 'hristest'));
	}
	
	/* Views */
	
	public function index() {
		// redirect("/timekeeping/summary");
	}
	
	public function settings() {
	    $this->view_template('overtime/overtime_settings', 'Overtime', array(
			'breadcrumbs' => array('Manage','General Settings'),
			'js' => array(
					'overtime.settings.js'
				),
			'data' => $this->overtime_m->getGeneralSettings()
		));
	}
	
	public function approval_settings() {
	    $this->view_template('overtime/approval_settings', 'Overtime', array(
			'breadcrumbs' => array('Manage','Approval Settings'),
			'js' => array(
					'jquery.dataTables.min.js',
					'jquery.dataTables.bootstrap.js',
					'overtime.approval_settings.js'
				),
			'depts' => $this->employees_m->getDepts()
		));
	}
	
	public function submit_overtime() {
	  $this->view_template('overtime/submit_overtime', 'Overtime', array(
			'breadcrumbs' 	=> array('Submit Overtime'),
			'js' 			=> array(
								 'jquery.dataTables.min.js',
								 'jquery.dataTables.bootstrap.js',
								 'jquery.inputlimiter.1.3.1.min.js',
								 'jquery.maskedinput.min.js',
								 'jquery.validate.min.js',
								 'date-time/bootstrap-datepicker.min.js',
								 // 'date-time/bootstrap-timepicker.min.js',
								 'overtime.filing.js'
							   ),
			// 'css' => array(
					// 'bootstrap-timepicker.css'
				// ),
			'settings' 		=> $this->overtime_m->getGeneralSettings()
	  ));
	}
	
	public function manage_approval(){
	  $this->view_template('overtime/manage_approval', 'Overtime', array(
			'breadcrumbs' => array('Overtimes for Approval'),
			'js' => array(
					'jquery.dataTables.min.js',
					'jquery.dataTables.bootstrap.js',
					'jquery.inputlimiter.1.3.1.min.js',
					'date-time/bootstrap-datepicker.min.js',
					'overtime.manage_approval.js'
				)
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
	  
	  $approver_depts = $this->overtime_m->getApproverGroup($this->session->userdata("mb_no"),"DISTINCT taga.ot_apprv_grp_id, taga.level");
	  $app_level = $lv_apprv_grp_id = 0;
	  $apprv_grp = "";
	  $apprv_level = "";
	  $search_str = "";
	  if(count($approver_depts)) {
	    foreach($approver_depts as $groups) {
		  $search_str .= (empty($search_str)?"":" OR ")."(tlaa.ot_apprv_grp_id = '".$groups->ot_apprv_grp_id."' AND tlaa.approved_level >= '".$groups->level."')";
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
	  
	  $select_str = "tla.time_in, tla.time_out, tlaa.*, tag.ot_group_code, CONCAT(IF(sub.mb_3='Local',sub.mb_fname,sub.mb_nick),' ',sub.mb_lname) sender, CONCAT(IF(apprv.mb_3='Local',apprv.mb_fname,apprv.mb_nick),' ',apprv.mb_lname) approver, CASE tlaa.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'For Approval' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END status_lbl, taga.level user_level ";
	  
	  $data = $this->overtime_m->getAllForApprovalFiltered($select_str, $search_str);
	  $all_approval_count = count($data);
	  
	  $data_all = $this->overtime_m->getAllForApprovalFiltered($select_str, $having_str);
	  $all_filtered_count = count($data_all);
	  
	  $data = $this->overtime_m->getAllForApprovalFiltered($select_str, $having_str, $post['start'], $post['length'], $order_arr);
	  
	  echo json_encode(array("data"=>$data,
							"draw"=>(int)$post["draw"],
							"recordsTotal"=>$all_approval_count,
							"recordsFiltered"=>$all_filtered_count));
	  
	}

	public function approveOT() {
	  $mb_no = $this->session->userdata("mb_no");
	  if($mb_no) {
		  $date 			= new DateTime();
		  $post 			= $this->input->post();
		  
		  $approver_dtl = $this->overtime_m->getAllForApprovalFiltered("tlaa.*","tlaa.ot_app_id = '".$post['ot_app_id']."'");
		  if(count($approver_dtl)) {
			  $approver_dtl 	= $this->overtime_m->getApprovalGroupApprover($post['grp_id'], "MIN(level) level", array("level >"=>$post['approval_level']));
			  
			  if(count($approver_dtl) && $approver_dtl[0]->level) {
				$success = $this->overtime_m->updateForApprovalOTApplication(array("approved_level"=>$approver_dtl[0]->level,"approved_by"=>$mb_no), array("approval_id"=>$post['approval_id']));
				if($success) {
				  $this->overtime_m->updateOTApplication(array("dirty_bit_ind"=>1), array("ot_app_id"=>$post['ot_app_id']));
				  $data			= array("ot_app_id"		=> $post['ot_app_id'],
									"status"			=> 3,
									"remarks"			=> "Approved",
									"created_by"		=> $mb_no,
									"created_datetime"	=> $date->format("Y-m-d H:i:s"));
				  $success = $this->overtime_m->insertForApprovalOTApplicationHist($data);
				  
				  $request_dtl = $this->overtime_m->getEmpOTApplication("*", "tla.ot_app_id = '".$post['ot_app_id']."'");
				  
				  // Notification - General
				  $this->load->model('notifications_model', 'notifications');
				  $this->notifications->create("application", 1, array("Overtime","approved"), $request_dtl[0]->mb_no,0,"overtime/submit_overtime");
				  
				  // Notification
				  // $this->ws->load('notifs');
				  $approver_dtl 	= $this->overtime_m->getApprovalGroupApprover($post['grp_id'], "taga.*", array("level"=>$approver_dtl[0]->level));
				  foreach($approver_dtl as $approver) {
					$recipient = $approver->mb_id;
					$date = new DateTime();
					NOTIFS::publish("APPROVAL_$recipient", array(
						'type' => "OT",
						'count' => 1
					));
				  }
				  $approver_dtl 	= $this->overtime_m->getApprovalGroupApprover($post['grp_id'], "taga.*", array("level"=>$post['approval_level']));
				  foreach($approver_dtl as $approver) {
					$recipient = $approver->mb_id;
					$date = new DateTime();
					NOTIFS::publish("APPROVAL_$recipient", array(
						'type' => "OT",
						'count' => -1
					));
				  }
				  //
				  
				}
			  }
			  else {
				$success = $this->overtime_m->updateForApprovalOTApplication(array("status"=>3,"approved_by"=>$mb_no), array("approval_id"=>$post['approval_id']));
				if($success) {
				  $this->overtime_m->updateOTApplication(array("status"=>3,"dirty_bit_ind"=>1), array("ot_app_id"=>$post['ot_app_id']));
				  $data			= array("ot_app_id"		=> $post['ot_app_id'],
									"status"			=> 3,
									"remarks"			=> "Approved",
									"created_by"		=> $mb_no,
									"created_datetime"	=> $date->format("Y-m-d H:i:s"));
				  $success = $this->overtime_m->insertForApprovalOTApplicationHist($data);
				  
				  $request_dtl = $this->overtime_m->getEmpOTApplication("*", "tla.ot_app_id = '".$post['ot_app_id']."'");
				  
				  // Notification - General
				  $this->load->model('notifications_model', 'notifications');
				  $this->notifications->create("application", 1, array("Overtime","approved"), $request_dtl[0]->mb_no,0,"overtime/submit_overtime");
				  
				  // Notification
				  // $this->ws->load('notifs');
				  $approver_dtl 	= $this->overtime_m->getApprovalGroupApprover($post['grp_id'], "taga.*", array("level"=>$post['approval_level']));
				  foreach($approver_dtl as $approver) {
					$recipient = $approver->mb_id;
					$date = new DateTime();
					NOTIFS::publish("APPROVAL_$recipient", array(
						'type' => "OT",
						'count' => -1
					));
				  }
				  //
				}
			  }
			  echo json_encode(array("success"=>1,"msg"=>"Overtime Approved!"));
		  }
		  else {
			echo json_encode(array("success"=>0,"msg"=>"Overtime application does not exists."));
		  }
	  }
	  else {
	      echo json_encode(array("success"=>0,"msg"=>"You have been logged out. Please login again."));
	  }
	}

	public function rejectOT() {
	  $mb_no = $this->session->userdata("mb_no");
	  if($mb_no) {
	    $date = new DateTime();
        $post = $this->input->post();
	  
	    $approver_dtl = $this->overtime_m->getAllForApprovalFiltered("tlaa.*","tlaa.ot_app_id = '".$post['ot_app_id']."'");
	    if(count($approver_dtl)) {
          $this->overtime_m->updateOTApplication(array("status"=>2, "dirty_bit_ind"=>1), array("ot_app_id"=>$post['ot_app_id']));
	      $success = $this->overtime_m->deleteForApprovalOTApplication(array("ot_app_id"=>$post['ot_app_id']));
	      if($success) {
	          $request_dtl 	= $this->overtime_m->getEmpOTApplication("tla.*", array("tla.ot_app_id"=>$post['ot_app_id']));
	          $data			= array("ot_app_id"			=> $post['ot_app_id'],
							"status"			=> 2,
							"remarks"			=> empty($post['remarks'])?"Rejected":$post['remarks'],
							"created_by"		=> $mb_no,
							"created_datetime"	=> $date->format("Y-m-d H:i:s"));
	          $success = $this->overtime_m->insertForApprovalOTApplicationHist($data);
			  
			  // Notification - General
			  $this->load->model('notifications_model', 'notifications');
			  $this->notifications->create("application", 1, array("Overtime","rejected"), $request_dtl[0]->mb_no,0,"overtime/submit_overtime");
			  
			  // Notification
			  // $this->ws->load('notifs');
			  $approver_dtl 	= $this->overtime_m->getApprovalGroupApprover($approver_dtl[0]->ot_apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->approved_level));
			  foreach($approver_dtl as $approver) {
				$recipient = $approver->mb_id;
				$date = new DateTime();
				NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "OT",
					'count' => -1
				));
			  }
			  //
			  
	      }
	      echo json_encode(array("success"=>1,"msg"=>"Overtime Rejected!"));
	    }
	    else {
	      echo json_encode(array("success"=>0,"msg"=>"OBT application does not exists."));
	    }
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"You have been logged out. Please login again."));
	  }
	}

	/* End of Approval */
	
	/* Approval Groups */
	
	public function getApprovalGroup($apprv_id) {
	  $select_str = "tag.*, ".
					"IF(enabled,'Enabled','Disabled') enabled_lbl";
	  $data = $this->overtime_m->getApprovalGroup($apprv_id,$select_str);
	  $data[0]->approvers = $this->overtime_m->getApprovalGroupApprover($apprv_id, "mb_3, taga.level, taga.mb_id, gm.mb_nick, gm.mb_fname, gm.mb_lname");
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
	  
	  $data = $this->overtime_m->getAllApprovalGroups($inactive,$select_str);
	  $all_approvers_count = count($data);
	  
	  $data_all = $this->overtime_m->getAllApprovalGroupsFiltered($inactive, $select_str, $having_str);
	  $all_filtered_count = count($data_all);
	  
	  $data = $this->overtime_m->getAllApprovalGroupsFiltered($inactive, $select_str, $having_str, $post['start'], $post['length'], $order_arr);
	  
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
	  
	  $app_group_dtl		= $this->overtime_m->getAllApprovalGroupsFiltered(true,"tag.ot_apprv_grp_id", array("ot_group_code"=>$grp_code));
	  
	  if(count($app_group_dtl)) {
	    echo json_encode(array("success"=>0,"msg"=>"Group Code already exists!"));
		return;
	  }
	  else {
	    $success = $this->overtime_m->insertApprovalGroup(array("ot_group_code"=>$grp_code, "enabled"=>$enabled, "created_datetime"=>$date->format("Y-m-d H:i:s"), "created_by"=>$this->session->userdata("mb_no"), "updated_datetime"=>$date->format("Y-m-d H:i:s"),"updated_by"=>$this->session->userdata("mb_no")));
	    if($success) {
		  $app_group_dtl	= $this->overtime_m->getAllApprovalGroupsFiltered(true,"tag.ot_apprv_grp_id", array("ot_group_code"=>$grp_code));
		  $grp_id = $app_group_dtl[0]->ot_apprv_grp_id;
		  foreach($approver_list as $key=>$approver) {
	        $this->overtime_m->insertApprovalGroupApprovers(array("ot_apprv_grp_id"=>$grp_id, "mb_id"=>$approver, "level"=>$approver_lvl_list[$key]));
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
	  $dept_list				= isset($post['dept_list'])?$post['dept_list']:array();
	  $approver_list			= isset($post['approver'])?$post['approver']:array();
	  $approver_lvl_list		= isset($post['approver_lvl'])?$post['approver_lvl']:array();
	  $approver_del_list		= isset($post['approver_del_arr'])?$post['approver_del_arr']:array();
	  
	  $app_group_dtl			= $this->overtime_m->getApprovalGroup($grp_id,"*");
	  $approver_dtl 			= $this->overtime_m->getApprovalGroupApprover($grp_id,"*, GROUP_CONCAT(taga.mb_id) approvers");
	  
	  $org_approvers 			= explode(",",$approver_dtl[0]->approvers);
	  $for_deletion_approvers 	= array_diff($approver_del_list, $approver_list);
	  $for_insert_approvers		= array_diff($approver_list, $org_approvers);
	  
	  $app_group_dtl		= $this->overtime_m->getAllApprovalGroupsFiltered(true,"tag.ot_apprv_grp_id", array("ot_group_code"=>$grp_code));
	  
	  if(count($app_group_dtl) && $app_group_dtl[0]->ot_apprv_grp_id != $grp_id) {
	    echo json_encode(array("success"=>0,"msg"=>"Group Code already exists!"));
		return;
	  }
	  
	  $success = $this->overtime_m->updateApprovalGroup(array("ot_group_code"=>$grp_code, "enabled"=>$enabled, "updated_datetime"=>$date->format("Y-m-d H:i:s"),"updated_by"=>$this->session->userdata("mb_no")), array("ot_apprv_grp_id"=>$grp_id));

	  foreach($for_insert_approvers as $key=>$approver) {
	    $this->overtime_m->insertApprovalGroupApprovers(array("ot_apprv_grp_id"=>$grp_id, "mb_id"=>$approver, "level"=>$approver_lvl_list[$key]));
	  }
	  
	  foreach($for_deletion_approvers as $approver) {
	    $this->overtime_m->deleteApprovalGroupApprovers(array("ot_apprv_grp_id"=>$grp_id, "mb_id"=>$approver));
	  }
	  if($success)
	    echo json_encode(array("success"=>1, "msg"=>"Record updated!"));
	  else
	    echo json_encode(array("success"=>0, "msg"=>"A database error occured. Please contact system administrator."));
	}
	
	public function deleteApprovalGroup() {
	  $post = $this->input->post();
	  $grp_id = $post['apprv_id'];
	  
	  $this->overtime_m->deleteApprovalGroup(array("ot_apprv_grp_id"=>$grp_id));
	  $this->overtime_m->deleteApprovalGroupApprovers(array("ot_apprv_grp_id"=>$grp_id));
	  
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
	  $resp = $this->overtime_m->updateGeneralSettings(array("min_overtime_min"=>$post['min_ot']),array());
	  if($resp){
	    echo json_encode(array("success"=>1,"msg"=>"Record updated"));
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"A database error occured"));
	  }
	}

	/* End of Settings */
	
	/* Overtime Filing */
    public function getEmpOTGrid($inactive = 0) {
	  $post = $this->input->post();
	  $limit = $post["limit"];
	  $page = $post["page"];
	  $offset = ($page - 1) * $limit;

	  $having_str = "tla.mb_no = '".$this->session->userdata("mb_no")."'";
	  $response_arr = array("Request ID", "Date", "Time In", "Time Out", "For Approval", "Status", "Action");
	  $widths_arr = array(80, 100, 110, 110, 160, 160,108);
	  
	  $select_str = "*, DATE(tla.date) ot_date, CASE tla.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END ot_status_lbl, tla.status ot_status";
	  
	  
	  $data = $this->overtime_m->getEmpOTApplication("*","tla.mb_no = '".$this->session->userdata("mb_no")."'");
	  $all_ot_count = count($data);
	  
	  $data_all = $this->overtime_m->getEmpOTApplication($select_str, $having_str, $offset, $limit, array("ot_app_id"=>"Desc"));
	  
	  $return_arr = array();
	  $pending_count = 0;
	  $date = new DateTime();
	  foreach($data_all as $overtime) {
	    if(in_array($overtime->ot_status,array(0,1,2)))
	      $pending_count++;
		  
		$tmp_date = new DateTime($overtime->ot_date);
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
		
		$allow_view = false;
		$allow_edit = false;
		$allow_submit = false;
		$allow_delete = false;
		$allow_cancel = false;
		
		switch($overtime->ot_status) {
		  case 0 :
				$allow_edit = true;
				$allow_submit = true;
				$allow_delete = true;
				break;
		  case 1 :
				$allow_view = true;
				if ($overtime->dirty_bit_ind) { $allow_cancel = true; } else { $allow_delete = true; }
		        break;
		  case 2 :
		        // $allow_edit = true;
				// $allow_submit = true;
				// $allow_cancel = true;
				$allow_view = true;
				break;
		  case 3 :
		        $allow_view = true;
				$allow_cancel = true;
				break;
		  case 4 :
				$allow_view = true;
				break;
		}
		
	    $return_arr[] = array(
						  "ot_app_id"	=> $overtime->ot_app_id,
						  "ot_date"		=> $overtime->ot_date,
						  "time_in"		=> $overtime->time_in,
						  "time_out"	=> $overtime->time_out,
						  "approver"	=> $approver,
						  "status_lbl"	=> $overtime->ot_status_lbl,
						  "action"		=> '<div class="action-buttons">'.
											  ($allow_view?'<a class="green request-view" href="#" data-id="'.$overtime->ot_app_id.'" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>':'').
											  /*($allow_edit?'<a class="blue request-edit" href="#" data-id="'.$overtime->ot_app_id.'" title="View"><i class="ace-icon fa fa-edit bigger-130"></i></a>':'').*/
											  /*($allow_submit?'<a class="green request-submit" href="#" data-id="'.$overtime->ot_app_id.'" title="Submit"><i class="ace-icon fa fa-share-square-o bigger-130"></i></a>':'').*/
											  ($allow_delete?'<a class="red request-remove" href="#" data-id="'.$overtime->ot_app_id.'" title="Delete"><i class="ace-icon fa fa-trash-o bigger-130"></i></a>':'').
											  ($allow_cancel?'<a class="red request-cancel" href="#" data-id="'.$overtime->ot_app_id.'" title="Cancel"><i class="ace-icon fa fa-close bigger-130"></i></a>':'').
											'</div>'
						);
	  }
	  
	  echo json_encode(array("data"=>$return_arr, "header"=>$response_arr, "width"=>$widths_arr, "total_count"=>$all_ot_count, "pending_count"=>$pending_count, "page" => $page));
	}
	
	public function saveOTApplication() {
	  $post				= $this->input->post();
	  $mb_no 			= $this->session->userdata("mb_no");
	  if($mb_no) {
		  $ot_app_id		= $post['request-id'];
		  $date_from		= $post['ot-date-from'];
		  $time_from		= $post['ot-time-from'];
		  $time_to			= $post['ot-time-to'];
		  $reason			= $post['reason'];
		
		  $ot_set 		= $this->overtime_m->getGeneralSettings();
		  $min_ot_min 	= $ot_set[0]->min_overtime_min;
		  
		  try {
			$ot_from_tmp 	= new DateTime($date_from." ".$time_from); 
			$ot_to_tmp 	= new DateTime($date_from." ".$time_to);
		  }
		  catch(Exception $e) { echo json_encode(array("success"=>0,"msg"=>"Invalid Time Specified")); return; }
		  $ot_from_hr   = $ot_from_tmp->format("H");
		  $ot_from_min  = $ot_from_tmp->format("i") *1;
		  
		  $ot_from_tmp->format("Y-m-d H:i:s");
		  $ot_to_tmp->format("Y-m-d H:i:s");
		  if($ot_to_tmp <= $ot_from_tmp) {
			if($ot_from_hr > 17 && $ot_from_min == 0)
			  $ot_to_tmp->modify("+1 day");
			else {
			  echo json_encode(array("success"=>0,"msg"=>"Invalid Time Range Specified"));
			  return;
			}
		  }
		  $total_hours = ($ot_to_tmp->format("U") - $ot_from_tmp->format("U"))/3600;
		  $abs_hours = floor($total_hours);
		  $abs_min	= floor(($total_hours - $abs_hours) * 60)/100;
		  $dispay_hours = $abs_hours + $abs_min;
		  
		  if($total_hours < $min_ot_min/60 && $min_ot_min > 0) {
			echo json_encode(array("success"=>0,"msg"=>"Total overtime minutes should be greater than ".$min_ot_min));
			return;
		  }
		  
		  $emp_dtl = $this->employees_m->get($mb_no);
		  $apprv_grp_id = 0;
		  if(count($emp_dtl)) {
			$apprv_grp_id = $emp_dtl['mb_ot_app_grp_id'];
		  }
		  
		  $date = new DateTime();
		  $date_from = new DateTime($date_from." 00:00:00");
		  
		  $record = $this->overtime_m->getEmpOTApplication("*",  "gm.mb_no = '".$mb_no."' AND `date` = '".$date_from->format("Y-m-d")."' AND ((time_in > '".$time_from."' AND time_in < '".$time_to."') OR (time_out > '".$time_from."' AND time_out < '".$time_to."') OR ('".$time_from."' > time_in AND '".$time_from."' < time_out) OR ('".$time_to."' > time_in AND '".$time_to."' < time_out) OR ('".$time_from."' = time_in AND '".$time_to."' = time_out)) AND tla.status NOT IN (2,4) AND tla.ot_app_id <> '".$ot_app_id."'");
		  
		  if(count($record)) {
			echo json_encode(array("success"=>0,"msg"=>"Duplicate request. Please review."));
			return false;
		  }
		  else {
			$approver2_dtl = $this->overtime_m->getApprovalGroupApprover($apprv_grp_id,"*",array("taga.mb_id"=>$mb_no));
			
			if(count($approver2_dtl))
			  $approver_dtl 	= $this->overtime_m->getApprovalGroupApprover($approver2_dtl[0]->ot_apprv_grp_id, "MIN(level) level", array("level >"=>$approver2_dtl[0]->level));
			else
			  $approver_dtl 	= $this->overtime_m->getApprovalGroupApprover($apprv_grp_id, "MIN(level) level");
			
			// Insert
			$success = $this->overtime_m->insertOTApplication(array(
													"mb_no"				=> $mb_no,
													"ot_apprv_grp_id"	=> $apprv_grp_id,
													"date"				=> $date_from->format("Y-m-d"),
													"time_in"			=> $time_from,
													"time_out"			=> $time_to,
													"reason"			=> $reason,
													"status" 			=> 1,
													"created_datetime"	=> $date->format("Y-m-d H:i:s"),
													"submitted_datetime"=>$date->format("Y-m-d H:i:s")
												));
			$ot_app_id = $this->overtime_m->lastID();
		  }
		  
		  if($success) {
			// TO DO
			if(count($approver_dtl) && $approver_dtl[0]->level) {
			  $data 			= array("ot_app_id"			=> $ot_app_id,
										"ot_apprv_grp_id"	=> $apprv_grp_id,
										"date"				=> $date_from->format("Y-m-d"),
										"approved_level"	=> $approver_dtl[0]->level,
										"submitted_by"		=> $mb_no,
										"status"			=> 1,
										"created_by"		=> $mb_no,
										"created_datetime"	=> $date->format("Y-m-d H:i:s"),
										"updated_by"		=> $mb_no,
										"updated_datetime"	=> $date->format("Y-m-d H:i:s"));
			  $success 		= $this->overtime_m->insertForApprovalOTApplication($data);
			  if($success) {
				// Notification
				$this->ws->load('notifs');
				$approver_dtl 	= $this->overtime_m->getApprovalGroupApprover($apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->level));
				foreach($approver_dtl as $approver) {
				  $recipient = $approver->mb_id;
				  $date = new DateTime();
				  NOTIFS::publish("APPROVAL_$recipient", array(
						'type' => "OT",
						'count' => 1
					   ));
				}
				//
				$data		= array("ot_app_id"			=> $ot_app_id,
										"status"			=> 1,
										"remarks"			=> "Submitted for approval",
										"created_by"		=> $mb_no,
										"created_datetime"	=> $date->format("Y-m-d H:i:s"));
				$success = $this->overtime_m->insertForApprovalOTApplicationHist($data);
				echo json_encode(array("success"=>1,"msg"=>"Overtime Application Submitted!"));
			  }
			  else {
				echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
			  }
			}
			else {
			  $this->overtime_m->updateOTApplication(array("status"=>3, "dirty_bit_ind"=>1), array("ot_app_id"=>$ot_app_id));
			  echo json_encode(array("success"=>1,"msg"=>"Overtime Application Submitted!"));
			}
		  }
		  else {
			echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
		  }
	  }
	  else
	    echo json_encode(array("success"=>0,"msg"=>"You have been logged out. Please reload the page."));
	}
	
	public function deleteOTApplication() {
	  $request_id 	= $this->input->post("request_id");
	  $request_dtl 	= $this->overtime_m->getEmpOTApplication("tla.*", array("tla.ot_app_id"=>$request_id));
	  if(count($request_dtl)) {
	    $param 		= array("ot_app_id" => $request_id);
	  
	    if($request_dtl[0]->dirty_bit_ind)
		  $success  	= $this->overtime_m->updateOTApplication(array("status"=>4),$param);
	    else
	      $success  	= $this->overtime_m->deleteOTApplication($param);
	
	    if($success) {
		  $approver_dtl = $this->overtime_m->getAllForApprovalFiltered("tlaa.*","tlaa.ot_app_id = '".$request_id."'");
	      $success = $this->overtime_m->deleteForApprovalOTApplication($param);
		  if($success && count($approver_dtl)) {
			// Notification
			$this->ws->load('notifs');
			$approver_dtl 	= $this->overtime_m->getApprovalGroupApprover($request_dtl[0]->ot_apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->approved_level));
			foreach($approver_dtl as $approver) {
			  $recipient = $approver->mb_id;
			  $date = new DateTime();
			  NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "OT",
					'count' => -1
				));
			}
			//
		  }
	      echo json_encode(array("success"=>1,"msg"=>"Overtime Application Deleted!"));
	    }
	    else {
	      echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
	    }
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"Overtime application does not exists. Please refresh."));
	  }
	}
	
	public function getOTApplication() {
	  $ot_app_id = $this->input->post("request_id");
	  $data = $this->overtime_m->getEmpOTApplication("*, DATE(tla.date) `date`", "tla.ot_app_id = '".$ot_app_id."'");
	  $remarks = $this->overtime_m->getEmpOTApplicationRemarks("tlah.*,CONCAT(IF(gm.mb_3='Local',gm.mb_fname,gm.mb_nick),' ',gm.mb_lname) mb_nick", "tlah.ot_app_id = '".$ot_app_id."'");
	  echo json_encode(array("success"=>1,"data"=>$data, "remarks"=>$remarks));
	}
	
	public function cancelOTApplication() {
	  $request_id 	= $this->input->post("request_id");
	  $remarks = $this->input->post("remarks");
	  $request_dtl 	= $this->overtime_m->getEmpOTApplication("tla.*", "tla.ot_app_id = '".$request_id."'");
	  if(count($request_dtl)) {
	    $param 			= array("ot_app_id" => $request_id);
	    $date			= new DateTime();
	    $success 		= $this->overtime_m->updateOTApplication(array("status"=>4), $param);
	    $data			= array("ot_app_id"			=> $request_id,
							"status"			=> 4,
							"remarks"			=> "Cancelled - ".$remarks,
							"created_by"		=> $this->session->userdata("mb_no"),
							"created_datetime"	=> $date->format("Y-m-d H:i:s"));
	    $success = $this->overtime_m->insertForApprovalOTApplicationHist($data);
	  
	    if($success) {
		  $approver_dtl = $this->overtime_m->getAllForApprovalFiltered("tlaa.*","tlaa.ot_app_id = '".$request_id."'");
	      $success = $this->overtime_m->deleteForApprovalOTApplication($param);
		  if($success && count($approver_dtl)) {
			// Notification
			$this->ws->load('notifs');
			$approver_dtl 	= $this->overtime_m->getApprovalGroupApprover($request_dtl[0]->ot_apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->approved_level));
			foreach($approver_dtl as $approver) {
			  $recipient = $approver->mb_id;
			  $date = new DateTime();
			  NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "OT",
					'count' => -1
				));
			}
			//
		  }
	      echo json_encode(array("success"=>1,"msg"=>"Overtime Application Cancelled!"));
	    }
	    else {
	      echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
	    }
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"Overtime application does not exists. Please refresh."));
	  }
	}
	/* End of Overtime Filing */
	
	
}
?>