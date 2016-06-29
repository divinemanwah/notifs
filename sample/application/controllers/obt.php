<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Obt extends MY_Controller {

	private $month_list;

	function __construct() {
		parent::__construct();
		$this->load->model('employees_model', 'employees_m');
		$this->load->model('obt_model', 'obt_m');
		
		$this->load->library('ws', array('tag' => getenv('HTTP_HOST') == '10.120.10.139' ? 'hris' : 'hristest'));
	}
	
	/* Views */
	
	public function index() {
		// redirect("/timekeeping/summary");
	}
	
	public function approval_settings() {
	    $this->view_template('obt/approval_settings', 'OBT', array(
			'breadcrumbs' => array('Manage','Approval Settings'),
			'js' => array(
					'jquery.dataTables.min.js',
					'jquery.dataTables.bootstrap.js',
					'obt.approval_settings.js'
				)
		));
	}
	
	public function submit_obt() {
	  $this->view_template('obt/submit_obt', 'OBT', array(
			'breadcrumbs' 	=> array('Submit OBT'),
			'js' 			=> array(
								 'jquery.dataTables.min.js',
								 'jquery.dataTables.bootstrap.js',
								 'jquery.inputlimiter.1.3.1.min.js',
								 'jquery.maskedinput.min.js',
								 'jquery.validate.min.js',
								 'date-time/bootstrap-datepicker.min.js',
								 // 'date-time/bootstrap-timepicker.min.js',
								 'obt.filing.js'
							   ),
			// 'css' => array(
					// 'bootstrap-timepicker.css'
				// ),
			'emp_id'		=> $this->session->userdata("mb_no")
	  ));
	}
	
	public function manage_approval(){
	  $this->view_template('obt/manage_approval', 'OBT', array(
			'breadcrumbs' => array('OBT for Approval'),
			'js' => array(
					'jquery.dataTables.min.js',
					'jquery.dataTables.bootstrap.js',
					'jquery.inputlimiter.1.3.1.min.js',
					'date-time/bootstrap-datepicker.min.js',
					'obt.manage_approval.js'
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
	  
	  $approver_depts = $this->obt_m->getApproverGroup($this->session->userdata("mb_no"),"DISTINCT taga.obt_apprv_grp_id, taga.level");
	  $app_level = $lv_apprv_grp_id = 0;
	  $apprv_grp = "";
	  $apprv_level = "";
	  $search_str = "";
	  if(count($approver_depts)) {
	    foreach($approver_depts as $groups) {
		  $search_str .= (empty($search_str)?"":" OR ")."(tlaa.obt_apprv_grp_id = '".$groups->obt_apprv_grp_id."' AND tlaa.approved_level >= '".$groups->level."')";
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
	  
	  
	  $select_str = "tla.time_in, tla.time_out, tlaa.*, tag.obt_group_code, CONCAT(IF(sub.mb_3='Local',sub.mb_fname,sub.mb_nick),' ',sub.mb_lname) sender, CONCAT(IF(apprv.mb_3='Local',apprv.mb_fname,apprv.mb_nick),' ',apprv.mb_lname) approver, CASE tlaa.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'For Approval' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END status_lbl, taga.level user_level ";
	  
	  $data = $this->obt_m->getAllForApprovalFiltered($select_str,$search_str);
	  $all_approval_count = count($data);
	  
	  $data_all = $this->obt_m->getAllForApprovalFiltered($select_str, $having_str);
	  $all_filtered_count = count($data_all);
	  
	  $data = $this->obt_m->getAllForApprovalFiltered($select_str, $having_str, $post['start'], $post['length'], $order_arr);
	  
	  echo json_encode(array("data"=>$data,
							"draw"=>(int)$post["draw"],
							"recordsTotal"=>$all_approval_count,
							"recordsFiltered"=>$all_filtered_count));
	  
	}

	public function approveOBT() {
	  $date 			= new DateTime();
      $post 			= $this->input->post();
	  
	  $approver_dtl = $this->obt_m->getAllForApprovalFiltered("tlaa.*","tlaa.obt_app_id = '".$post['obt_app_id']."'");
	  if(count($approver_dtl)) {
		  $approver_dtl 	= $this->obt_m->getApprovalGroupApprover($post['grp_id'], "MIN(level) level", array("level >"=>$post['approval_level']));
		  
		  if(count($approver_dtl) && $approver_dtl[0]->level) {
			$success = $this->obt_m->updateForApprovalOBTApplication(array("approved_level"=>$approver_dtl[0]->level,"approved_by"=>$this->session->userdata("mb_no")), array("approval_id"=>$post['approval_id']));
			if($success) {
			  $this->obt_m->updateOBTApplication(array("dirty_bit_ind"=>1), array("obt_app_id"=>$post['obt_app_id']));
			  $data			= array("obt_app_id"		=> $post['obt_app_id'],
								"status"			=> 3,
								"remarks"			=> "Approved",
								"created_by"		=> $this->session->userdata("mb_no"),
								"created_datetime"	=> $date->format("Y-m-d H:i:s"));
			  $success = $this->obt_m->insertForApprovalOBTApplicationHist($data);
			  
			  $request_dtl 	= $this->obt_m->getEmpOBTApplication("tla.*", array("tla.obt_app_id"=>$post['obt_app_id']));
			  
			  // Notification - General
			  $this->load->model('notifications_model', 'notifications');
			  $this->notifications->create("application", 1, array("OBT","approved"), $request_dtl[0]->mb_no,0,"obt/submit_obt");
				  
			  // Notification
			  // $this->ws->load('notifs');
			  $approver_dtl 	= $this->obt_m->getApprovalGroupApprover($post['grp_id'], "taga.*", array("level"=>$approver_dtl[0]->level));
			  foreach($approver_dtl as $approver) {
				$recipient = $approver->mb_id;
				$date = new DateTime();
				NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "OBT",
					'count' => 1
				));
			  }
			  $approver_dtl 	= $this->obt_m->getApprovalGroupApprover($post['grp_id'], "taga.*", array("level"=>$post['approval_level']));
			  foreach($approver_dtl as $approver) {
				$recipient = $approver->mb_id;
				$date = new DateTime();
				NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "OBT",
					'count' => -1
				));
			  }
			  //
			}
		  }
		  else {
			$success = $this->obt_m->updateForApprovalOBTApplication(array("status"=>3,"approved_by"=>$this->session->userdata("mb_no")), array("approval_id"=>$post['approval_id']));
			if($success) {
			  $this->obt_m->updateOBTApplication(array("status"=>3,"dirty_bit_ind"=>1), array("obt_app_id"=>$post['obt_app_id']));
			  $data			= array("obt_app_id"		=> $post['obt_app_id'],
								"status"			=> 3,
								"remarks"			=> "Approved",
								"created_by"		=> $this->session->userdata("mb_no"),
								"created_datetime"	=> $date->format("Y-m-d H:i:s"));
			  $success = $this->obt_m->insertForApprovalOBTApplicationHist($data);
			  
			  $request_dtl 	= $this->obt_m->getEmpOBTApplication("tla.*", array("tla.obt_app_id"=>$post['obt_app_id']));
			  if(count($request_dtl)) {
			    $date = new DateTime($request_dtl[0]->date);
		        $date_str_from = $date_str_to = $date->format("Ymd");
			    $this->updateAttendance($request_dtl[0]->mb_no, $date_str_from, $date_str_to);
			  }
			  
			  // Notification - General
			  $this->load->model('notifications_model', 'notifications');
			  $this->notifications->create("application", 1, array("OBT","approved"), $request_dtl[0]->mb_no,0,"obt/submit_obt");
			  
			  // Notification
			  // $this->ws->load('notifs');
			  $approver_dtl 	= $this->obt_m->getApprovalGroupApprover($post['grp_id'], "taga.*", array("level"=>$post['approval_level']));
			  foreach($approver_dtl as $approver) {
				$recipient = $approver->mb_id;
				$date = new DateTime();
				NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "OBT",
					'count' => -1
				));
			  }
			  //
			}
		  }
		  echo json_encode(array("success"=>1,"msg"=>"OBT Approved!"));
		}
		 else {
		  echo json_encode(array("success"=>0,"msg"=>"OBT application does not exists."));
		}
	}
	
	public function rejectOBT() {
	  $date = new DateTime();
      $post = $this->input->post();
      
	  $approver_dtl = $this->obt_m->getAllForApprovalFiltered("tlaa.*","tlaa.obt_app_id = '".$post['obt_app_id']."'");
	  if(count($approver_dtl)) {
	      $this->obt_m->updateOBTApplication(array("status"=>2, "dirty_bit_ind"=>1), array("obt_app_id"=>$post['obt_app_id']));
		  $success = $this->obt_m->deleteForApprovalOBTApplication(array("obt_app_id"=>$post['obt_app_id']));
		  if($success) {
		    $request_dtl 	= $this->obt_m->getEmpOBTApplication("tla.*", array("tla.obt_app_id"=>$post['obt_app_id']));
			$data			= array("obt_app_id"			=> $post['obt_app_id'],
								"status"			=> 2,
								"remarks"			=> empty($post['remarks'])?"Rejected":$post['remarks'],
								"created_by"		=> $this->session->userdata("mb_no"),
								"created_datetime"	=> $date->format("Y-m-d H:i:s"));
			$success = $this->obt_m->insertForApprovalOBTApplicationHist($data);
			
			if(count($request_dtl)) {
			  $date = new DateTime($request_dtl[0]->date);
		      $date_str_from = $date_str_to = $date->format("Ymd");
			  $this->updateAttendance($request_dtl[0]->mb_no, $date_str_from, $date_str_to);
			}
			
			// Notification - General
			$this->load->model('notifications_model', 'notifications');
			$this->notifications->create("application", 1, array("OBT","rejected"), $request_dtl[0]->mb_no,0,"obt/submit_obt");
			
			// Notification
			// $this->ws->load('notifs');
			$approver_dtl 	= $this->obt_m->getApprovalGroupApprover($approver_dtl[0]->obt_apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->approved_level));
			foreach($approver_dtl as $approver) {
			  $recipient = $approver->mb_id;
			  $date = new DateTime();
			  NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "OBT",
					'count' => -1
			  ));
			}
			//
			
		  }
		  echo json_encode(array("success"=>1,"msg"=>"OBT Rejected!"));
		}
	    else {
		  echo json_encode(array("success"=>0,"msg"=>"OBT application does not exists."));
		}
	}

	/* End of Approval */
	
	/* Approval Groups */
	
	public function getApprovalGroup($apprv_id) {
	  $select_str = "tag.*, ".
					"IF(enabled,'Enabled','Disabled') enabled_lbl";
	  $data = $this->obt_m->getApprovalGroup($apprv_id,$select_str);
	  $data[0]->approvers = $this->obt_m->getApprovalGroupApprover($apprv_id, "mb_3, taga.level, taga.mb_id, gm.mb_nick, gm.mb_fname, gm.mb_lname");
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
	  
	  $data = $this->obt_m->getAllApprovalGroups($inactive,$select_str);
	  $all_approvers_count = count($data);
	  
	  $data_all = $this->obt_m->getAllApprovalGroupsFiltered($inactive, $select_str, $having_str);
	  $all_filtered_count = count($data_all);
	  
	  $data = $this->obt_m->getAllApprovalGroupsFiltered($inactive, $select_str, $having_str, $post['start'], $post['length'], $order_arr);
	  
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
	  
	  $app_group_dtl		= $this->obt_m->getAllApprovalGroupsFiltered(true,"tag.obt_apprv_grp_id", array("obt_group_code"=>$grp_code));
	  
	  if(count($app_group_dtl)) {
	    echo json_encode(array("success"=>0,"msg"=>"Group Code already exists!"));
		return;
	  }
	  else {
	    $success = $this->obt_m->insertApprovalGroup(array("obt_group_code"=>$grp_code, "enabled"=>$enabled, "created_datetime"=>$date->format("Y-m-d H:i:s"), "created_by"=>$this->session->userdata("mb_no"), "updated_datetime"=>$date->format("Y-m-d H:i:s"),"updated_by"=>$this->session->userdata("mb_no")));
	    if($success) {
		  $app_group_dtl	= $this->obt_m->getAllApprovalGroupsFiltered(true,"tag.obt_apprv_grp_id", array("obt_group_code"=>$grp_code));
		  $grp_id = $app_group_dtl[0]->obt_apprv_grp_id;
		  foreach($approver_list as $key=>$approver) {
	        $this->obt_m->insertApprovalGroupApprovers(array("obt_apprv_grp_id"=>$grp_id, "mb_id"=>$approver, "level"=>$approver_lvl_list[$key]));
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
	  
	  $app_group_dtl			= $this->obt_m->getApprovalGroup($grp_id,"*");
	  $approver_dtl 			= $this->obt_m->getApprovalGroupApprover($grp_id,"*, GROUP_CONCAT(taga.mb_id) approvers");
	  
	  $org_approvers 			= explode(",",$approver_dtl[0]->approvers);
	  $for_deletion_approvers 	= array_diff($approver_del_list, $approver_list);
	  $for_insert_approvers		= array_diff($approver_list, $org_approvers);
	  
	  $app_group_dtl		= $this->obt_m->getAllApprovalGroupsFiltered(true,"tag.obt_apprv_grp_id", array("obt_group_code"=>$grp_code));
	  
	  if(count($app_group_dtl) && $app_group_dtl[0]->obt_apprv_grp_id != $grp_id) {
	    echo json_encode(array("success"=>0,"msg"=>"Group Code already exists!"));
		return;
	  }
	  
	  $success = $this->obt_m->updateApprovalGroup(array("obt_group_code"=>$grp_code, "enabled"=>$enabled, "updated_datetime"=>$date->format("Y-m-d H:i:s"),"updated_by"=>$this->session->userdata("mb_no")), array("obt_apprv_grp_id"=>$grp_id));

	  foreach($for_insert_approvers as $key=>$approver) {
	    $this->obt_m->insertApprovalGroupApprovers(array("obt_apprv_grp_id"=>$grp_id, "mb_id"=>$approver, "level"=>$approver_lvl_list[$key]));
	  }
	  
	  foreach($for_deletion_approvers as $approver) {
	    $this->obt_m->deleteApprovalGroupApprovers(array("obt_apprv_grp_id"=>$grp_id, "mb_id"=>$approver));
	  }
	  if($success)
	    echo json_encode(array("success"=>1, "msg"=>"Record updated!"));
	  else
	    echo json_encode(array("success"=>0, "msg"=>"A database error occured. Please contact system administrator."));
	}
	
	public function deleteApprovalGroup() {
	  $post = $this->input->post();
	  $grp_id = $post['apprv_id'];
	  
	  $this->obt_m->deleteApprovalGroup(array("obt_apprv_grp_id"=>$grp_id));
	  $this->obt_m->deleteApprovalGroupApprovers(array("obt_apprv_grp_id"=>$grp_id));
	  
	  echo json_encode(array("success"=>1));
	}
	
	public function getApprovalGroupFields() {
	  $emp = $this->employees_m->getAll(false,"*",false);
	  echo json_encode(array("success"=>1,"emp"=>$emp));
	}

	/* End of Approval Groups */
	
	/* OBT Filing */
	
	public function getEmpOBTGrid($inactive = 0) {
	  $post = $this->input->post();
	  $limit = $post["limit"];
	  $page = $post["page"];
	  $offset = ($page - 1) * $limit;

	  $having_str = "tla.mb_no = '".$this->session->userdata("mb_no")."'";
	  $response_arr = array("Request ID", "Date", "Time From", "Time To", "For Approval", "Status", "Action");
	  $widths_arr = array(80,100,100,100,160,160,108);
	  
	  $select_str = "*, DATE(tla.date) obt_date, CASE tla.status WHEN 0 THEN 'Pending' WHEN 1 THEN 'Submitted' WHEN 2 THEN 'Rejected' WHEN 3 THEN 'Approved' WHEN 4 THEN 'Cancelled' END obt_status_lbl, tla.status obt_status";
	  
	  
	  $data = $this->obt_m->getEmpOBTApplication("*","tla.mb_no = '".$this->session->userdata("mb_no")."'");
	  $all_leaves_count = count($data);
	  
	  $data_all = $this->obt_m->getEmpOBTApplication($select_str, $having_str, $offset, $limit, array("obt_app_id"=>"Desc"));
	  
	  $return_arr = array();
	  $pending_count = 0;
	  $date = new DateTime();
	  foreach($data_all as $obt) {
	    if(in_array($obt->obt_status,array(0,1,2)))
	      $pending_count++;
		  
		$tmp_date = new DateTime($obt->obt_date);
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
		
		$allow_view = false;
		$allow_edit = false;
		$allow_submit = false;
		$allow_delete = false;
		$allow_cancel = false;
		
		switch($obt->status) {
		  case 0 :
				$allow_edit = true;
				$allow_submit = true;
				$allow_delete = true;
				break;
		  case 1 :
				$allow_view = true;
				if ($obt->dirty_bit_ind) { $allow_cancel = true; } else { $allow_delete = true; }
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
						  "obt_app_id"	=> $obt->obt_app_id,
						  "date_from"	=> $obt->obt_date,
						  "time_in"		=> $obt->time_in,
						  "time_out"	=> $obt->time_out,
						  "approver"	=> $approver,
						  "status_lbl"	=> $obt->obt_status_lbl,
						  "action"		=> '<div class="action-buttons">'.
											  ($allow_view?'<a class="green request-view" href="#" data-id="'.$obt->obt_app_id.'" title="View"><i class="ace-icon fa fa-search bigger-130"></i></a>':'').
											  /*($allow_edit?'<a class="blue request-edit" href="#" data-id="'.$obt->obt_app_id.'" title="View"><i class="ace-icon fa fa-edit bigger-130"></i></a>':'').*/
											  /*($allow_submit?'<a class="green request-submit" href="#" data-id="'.$obt->obt_app_id.'" title="Submit"><i class="ace-icon fa fa-share-square-o bigger-130"></i></a>':'').*/
											  ($allow_delete?'<a class="red request-remove" href="#" data-id="'.$obt->obt_app_id.'" title="Delete"><i class="ace-icon fa fa-trash-o bigger-130"></i></a>':'').
											  ($allow_cancel?'<a class="red request-cancel" href="#" data-id="'.$obt->obt_app_id.'" title="Cancel"><i class="ace-icon fa fa-close bigger-130"></i></a>':'').
											'</div>'
						);
	  }
	  
	  echo json_encode(array("data"=>$return_arr, "header"=>$response_arr, "width"=>$widths_arr, "total_count"=>$all_leaves_count, "pending_count"=>$pending_count, "page" => $page));
	}
	
	public function saveOBTApplication() {
	  $post				= $this->input->post();
	  $mb_no 			= $this->session->userdata("mb_no");
	  if($mb_no) {
		  $obt_app_id		= $post['request-id'];
		  $obt_date			= $post['obt-date'];
		  $time_from		= $post['obt-time-from'];
		  $time_to			= $post['obt-time-to'];
		  $reason			= $post['reason'];
		
		  try {
			$obt_from_tmp 	= new DateTime($obt_date." ".$time_from);
			$obt_to_tmp 	= new DateTime($obt_date." ".$time_to);
		  }
		  catch(Exception $e) { echo json_encode(array("success"=>0,"msg"=>"Invalid Time Specified")); return; }
		  $obt_from_hr   = $obt_from_tmp->format("H");
		  $obt_from_min  = $obt_from_tmp->format("m") * 1;
		  
		  $obt_from_tmp->format("Y-m-d H:i:s");
		  $obt_to_tmp->format("Y-m-d H:i:s");
		  if($obt_to_tmp <= $obt_from_tmp) {
			if($obt_from_hr > 20 && $obt_from_min == 0)
			  $obt_to_tmp->modify("+1 day");
			else {
			  echo json_encode(array("success"=>0,"msg"=>"Invalid Time Range Specified"));
			  return;
			}
		  }
		  $total_hours = ($obt_to_tmp->format("U") - $obt_from_tmp->format("U"))/3600;
		  $abs_hours = floor($total_hours);
		  $abs_min	= floor(($total_hours - $abs_hours) * 60)/100;
		  $dispay_hours = $abs_hours + $abs_min;
		  
		  $emp_dtl = $this->employees_m->get($mb_no);
		  $apprv_grp_id = 0;
		  if(count($emp_dtl)) {
			$apprv_grp_id = $emp_dtl['mb_obt_app_grp_id'];
		  }
		  
		  $date = new DateTime();
		  $obt_date = new DateTime($obt_date." 00:00:00");
		  
		  $record = $this->obt_m->getEmpOBTApplication("*",  "gm.mb_no = '".$mb_no."' AND `date` = '".$obt_date->format("Y-m-d")."' AND ((time_in > '".$time_from."' AND time_in < '".$time_to."') OR (time_out > '".$time_from."' AND time_out < '".$time_to."') OR ('".$time_from."' > time_in AND '".$time_from."' < time_out) OR ('".$time_to."' > time_in AND '".$time_to."' < time_out)) AND tla.status NOT IN (2,4) AND tla.obt_app_id <> '".$obt_app_id."'");
		  
		  if(count($record)) {
			echo json_encode(array("success"=>0,"msg"=>"Duplicate request. Please review."));
			return false;
		  }
		  else {
			$approver2_dtl = $this->obt_m->getApprovalGroupApprover($apprv_grp_id,"*",array("taga.mb_id"=>$mb_no));
			if(count($approver2_dtl))
			  $approver_dtl 	= $this->obt_m->getApprovalGroupApprover($approver2_dtl[0]->obt_apprv_grp_id, "MIN(level) level", array("level >"=>$approver2_dtl[0]->level));
			else
			  $approver_dtl 	= $this->obt_m->getApprovalGroupApprover($apprv_grp_id, "MIN(level) level");
			
			// Insert
			$success = $this->obt_m->insertOBTApplication(array(
													"mb_no"				=> $mb_no,
													"obt_apprv_grp_id"	=> $apprv_grp_id,
													"date"				=> $obt_date->format("Y-m-d"),
													"time_in"			=> $time_from,
													"time_out"			=> $time_to,
													"reason"			=> $reason,
													"status" 			=> 1,
													"created_datetime"	=> $date->format("Y-m-d H:i:s"),
													"submitted_datetime"=>$date->format("Y-m-d H:i:s")
												));
			$obt_app_id = $this->obt_m->lastID();
			if($success) {
			  if(count($approver_dtl) && $approver_dtl[0]->level) {
				$data 			= array("obt_app_id"		=> $obt_app_id,
										"obt_apprv_grp_id"	=> $apprv_grp_id,
										"date"				=> $obt_date->format("Y-m-d"),
										"approved_level"	=> $approver_dtl[0]->level,
										"submitted_by"		=> $mb_no,
										"status"			=> 1,
										"created_by"		=> $mb_no,
										"created_datetime"	=> $date->format("Y-m-d H:i:s"),
										"updated_by"		=> $mb_no,
										"updated_datetime"	=> $date->format("Y-m-d H:i:s"));
				$success 		= $this->obt_m->insertForApprovalOBTApplication($data);
				if($success) {
				  // Notification
				  $this->ws->load('notifs');
				  $approver_dtl 	= $this->obt_m->getApprovalGroupApprover($apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->level));
				  foreach($approver_dtl as $approver) {
					$recipient = $approver->mb_id;
					$date = new DateTime();
					NOTIFS::publish("APPROVAL_$recipient", array(
							'type' => "OBT",
							'count' => 1
						));
				  }
				  //
				  $data			= array("obt_app_id"		=> $obt_app_id,
										"status"			=> 1,
										"remarks"			=> "Submitted for approval",
										"created_by"		=> $mb_no,
										"created_datetime"	=> $date->format("Y-m-d H:i:s"));
				  $success = $this->obt_m->insertForApprovalOBTApplicationHist($data);
				  echo json_encode(array("success"=>1,"msg"=>"OBT Application Submitted!"));
				}
				else {
				  echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
				}
			  }
			  else {
				$this->obt_m->updateOBTApplication(array("status"=>3, "dirty_bit_ind"=>1), array("obt_app_id"=>$obt_app_id));
				$date_str_from = $date_str_to = $obt_date->format("Ymd");
				$this->updateAttendance($mb_no, $date_str_from, $date_str_to);
				echo json_encode(array("success"=>1,"msg"=>"OBT Application Submitted!", array($mb_no, $date_str_from, $date_str_to)));
			  }
			}
			else
			  echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
		  }
	  }
	  else
	    echo json_encode(array("success"=>0,"msg"=>"You have been logged out. Please reload the page."));
	}
	
	public function deleteOBTApplication() {
	  $request_id 	= $this->input->post("request_id");
	  $request_dtl 	= $this->obt_m->getEmpOBTApplication("tla.*", array("tla.obt_app_id"=>$request_id));
	  if(count($request_dtl)) {
	    $param 		= array("obt_app_id" => $request_id);
	  
	    if($request_dtl[0]->dirty_bit_ind)
		  $success  	= $this->obt_m->updateOBTApplication(array("status"=>4),$param);
	    else
	      $success  	= $this->obt_m->deleteOBTApplication($param);
	
	    if($success) {
		  $approver_dtl = $this->obt_m->getAllForApprovalFiltered("tlaa.*","tlaa.obt_app_id = '".$request_id."'");
	      $success = $this->obt_m->deleteForApprovalOBTApplication($param);
		  if($success && count($approver_dtl)) {
			// Notification
			$this->ws->load('notifs');
			$approver_dtl 	= $this->obt_m->getApprovalGroupApprover($request_dtl[0]->obt_apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->approved_level));
			foreach($approver_dtl as $approver) {
			  $recipient = $approver->mb_id;
			  $date = new DateTime();
			  NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "OBT",
					'count' => -1
				));
			}
			//
		  }
		  if($success) {
		    $date = new DateTime($request_dtl[0]->date);
		    $date_str_from = $date_str_to = $date->format("Ymd");
			$this->updateAttendance($this->session->userdata("mb_no"), $date_str_from, $date_str_to);
		  }
	      echo json_encode(array("success"=>1,"msg"=>"OBT Application Deleted!"));
	    }
	    else {
	      echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
	    }
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"OBT application does not exists. Please refresh."));
	  }
	}
	
	public function getOBTApplication() {
	  $obt_app_id = $this->input->post("request_id");
	  $data = $this->obt_m->getEmpOBTApplication("*, DATE(tla.date) `date`", "tla.obt_app_id = '".$obt_app_id."'");
	  $remarks = $this->obt_m->getEmpOBTApplicationRemarks("tlah.*,CONCAT(IF(gm.mb_3='Local',gm.mb_fname,gm.mb_nick),' ',gm.mb_lname) mb_nick", "tlah.obt_app_id = '".$obt_app_id."'");
	  echo json_encode(array("success"=>1,"data"=>$data, "remarks"=>$remarks));
	}
	
	public function cancelOBTApplication() {
	  $request_id 	= $this->input->post("request_id");
	  $remarks = $this->input->post("remarks");
	  $request_dtl 	= $this->obt_m->getEmpOBTApplication("tla.*", "tla.obt_app_id = '".$request_id."'");
	  if(count($request_dtl)) {
	    $param 			= array("obt_app_id" => $request_id);
	    $date			= new DateTime();
	    $success 		= $this->obt_m->updateOBTApplication(array("status"=>4), $param);
	    $data			= array("obt_app_id"			=> $request_id,
							"status"			=> 4,
							"remarks"			=> "Cancelled - ".$remarks,
							"created_by"		=> $this->session->userdata("mb_no"),
							"created_datetime"	=> $date->format("Y-m-d H:i:s"));
	    $success = $this->obt_m->insertForApprovalOBTApplicationHist($data);
	  
	    if($success) {
		  $approver_dtl = $this->obt_m->getAllForApprovalFiltered("tlaa.*","tlaa.obt_app_id = '".$request_id."'");
	      $success = $this->obt_m->deleteForApprovalOBTApplication($param);
		  if($success && count($approver_dtl)) {
			// Notification
			$this->ws->load('notifs');
			$approver_dtl 	= $this->obt_m->getApprovalGroupApprover($request_dtl[0]->obt_apprv_grp_id, "taga.*", array("level"=>$approver_dtl[0]->approved_level));
			foreach($approver_dtl as $approver) {
			  $recipient = $approver->mb_id;
			  $date = new DateTime();
			  NOTIFS::publish("APPROVAL_$recipient", array(
					'type' => "OBT",
					'count' => -1
				));
			}
			//
		  }
		  if($success) {
		    $date = new DateTime($request_dtl[0]->date);
		    $date_str_from = $date_str_to = $date->format("Ymd");
			$this->updateAttendance($this->session->userdata("mb_no"), $date_str_from, $date_str_to);
		  }
	      echo json_encode(array("success"=>1,"msg"=>"OBT Application Cancelled!"));
	    }
	    else {
	      echo json_encode(array("success"=>0,"msg"=>"A database error occured. Please contact system administrator."));
	    }
	  }
	  else {
	    echo json_encode(array("success"=>0,"msg"=>"OBT application does not exists. Please refresh."));
	  }
	}
	
	/* End of OBT Filing */
}
?>