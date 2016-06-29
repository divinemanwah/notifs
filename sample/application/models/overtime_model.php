<?php

class Overtime_model extends CI_Model {

	function __construct() {
        parent::__construct();
    }
	
	/* General Settings */
	public function getGeneralSettings() {
	  $this->db->select("*", false)
					->from('tk_general_setting tgs');
	  
	  return $this->db->get()->result();
	}
	
	public function updateGeneralSettings($data,$param) {
	  return $this->db->update("tk_general_setting",$data,$param);
	}
	/* End of General Settings */
	
	/* Approval Groups */
	public function getApprovalGroup($app_grp_id, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_ot_approval_group tag')
					->group_by('tag.ot_group_code')
					->order_by('tag.ot_group_code');

	  $this->db->where('tag.ot_apprv_grp_id', $app_grp_id);
		
	  return $this->db->get()->result();
	}
	
	public function getAllApprovalGroups($show_inactive = false, $select = '*') {
		
		$this->db->select($select, false)
					->from('tk_ot_approval_group tag')
					->group_by('tag.ot_group_code')
					->order_by('tag.ot_group_code');

		if(!$show_inactive)
			$this->db->where('tag.enabled', 1);
		
		return $this->db->get()->result();
	}
	
	public function getAllApprovalGroupsFiltered($show_inactive = false, $select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
		$this->db->select($select, false)
					->from('tk_ot_approval_group tag')
					->group_by('tag.ot_group_code');
		
		if(!empty($having)) {
		  $this->db->having($having);
		}
		
		if($limit > 0)
		  $this->db->limit($limit, $start);
					
		if(!$show_inactive)
			$this->db->where('tag.enabled', 1);
		
		if(!empty($order_arr)) {
		  foreach($order_arr as $field=>$dir)
			$this->db->order_by($field,$dir);
		}
		
		return $this->db->get()->result();
	}
	
	public function insertApprovalGroup($data) {
	  return $this->db->insert("tk_ot_approval_group",$data);
	}
	
	public function updateApprovalGroup($data,$param) {
	  return $this->db->update("tk_ot_approval_group",$data,$param);
	}
	
	public function deleteApprovalGroup($param) {
	  return $this->db->delete("tk_ot_approval_group",$param);
	}
	
	public function getApprovalGroupApprover($app_grp_id, $select = '*', $where= array()) {
	  $this->db->select($select, false)
					->from('tk_ot_apprv_grp_approver taga')
					->join('g4_member gm', 'taga.mb_id = gm.mb_no', 'left' )
					;

	  $this->db->where('taga.ot_apprv_grp_id', $app_grp_id);
	
	  foreach($where as $field=>$val) {
	    $this->db->where($field, $val);
	  }
	  return $this->db->get()->result();
	}
	
	public function insertApprovalGroupApprovers($data) {
	  return $this->db->insert("tk_ot_apprv_grp_approver",$data);
	}
	
	public function deleteApprovalGroupApprovers($param) {
	  return $this->db->delete("tk_ot_apprv_grp_approver",$param);
	}
	
	/* End of Approval Groups */
	
	/* Overtime Filing */
	public function getEmpOTApplication($select = '*', $where="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('tk_ot_application tla')
					->join('g4_member gm', 'tla.mb_no = gm.mb_no' , 'left')
					->join('dept d', 'gm.mb_deptno = d.dept_no' , 'left');
					
					
	  if(!empty($where)) {
		$this->db->where($where);
	  }
	  
	  if($limit > 0)
		  $this->db->limit($limit, $start);
	  
	  if(!empty($order_arr)) {
		foreach($order_arr as $field=>$dir)
		  $this->db->order_by($field,$dir);
	  }
	  
	  return $this->db->get()->result();
	}
	
	public function insertOTApplication($data) {
	  return $this->db->insert("tk_ot_application",$data);
	}
	
	public function updateOTApplication($data,$param) {
	  return $this->db->update("tk_ot_application",$data,$param);
	}
	
	public function deleteOTApplication($param) {
	  return $this->db->delete("tk_ot_application",$param);
	}
	/* End of Overtime Filing */
	
	/* General Functions */
	public function getEmpOTApplicationRemarks($select = '*', $having="") {
	   $this->db->select($select, false)
					->from('tk_ot_application_approval_hist tlah')
					->join('g4_member gm', 'tlah.created_by = gm.mb_no', 'left' )
					->order_by('tlah.created_datetime',"DESC");
					
	  if(!empty($having)) {
		$this->db->having($having);
	  }
	  
	  return $this->db->get()->result();
	}
	
	public function insertForApprovalOTApplication($data) {
	  return $this->db->insert("tk_ot_application_approval",$data);
	}
	
	public function updateForApprovalOTApplication($data,$param) {
	  return $this->db->update("tk_ot_application_approval",$data,$param);
	}
	
	public function deleteForApprovalOTApplication($param) {
	  return $this->db->delete("tk_ot_application_approval",$param);
	}
	
	public function insertForApprovalOTApplicationHist($data) {
	  return $this->db->insert("tk_ot_application_approval_hist",$data);
	}
	
	public function getApproverGroup($mb_no, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_ot_apprv_grp_approver taga')
					;

	  $this->db->where('taga.mb_id', $mb_no);
	  return $this->db->get()->result();
	}
	
	public function getAllForApprovalFiltered($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('tk_ot_application_approval tlaa')
					->join('tk_ot_application tla', 'tlaa.ot_app_id = tla.ot_app_id', 'left')
					->join('tk_ot_approval_group tag', 'tlaa.ot_apprv_grp_id = tag.ot_apprv_grp_id', 'left')
					->join('tk_ot_apprv_grp_approver taga', 'tlaa.ot_apprv_grp_id = taga.ot_apprv_grp_id AND taga.mb_id = "'.$this->session->userdata("mb_no").'"',"left")
					->join('g4_member sub', 'tlaa.submitted_by = sub.mb_no', 'left' )
					->join('g4_member apprv', 'tlaa.approved_by = apprv.mb_no', 'left' )
					->group_by('tag.ot_group_code, tlaa.ot_app_id DESC');
					
	  if(!empty($having)) {
		$this->db->having($having);
	  }
	  
	  
	  if($limit > 0)
		$this->db->limit($limit, $start);
					
	  if(!empty($order_arr)) {
		foreach($order_arr as $field=>$dir)
		  $this->db->order_by($field,$dir);
	  }
	  return $this->db->get()->result();
	}

	/* End of General Functions */

	public function lastID() {
	  return $this->db->insert_id();
	}

}