<?php

class Obt_model extends CI_Model {

	function __construct() {
        parent::__construct();
    }
	
	/* End of Approval Groups */
	public function getApprovalGroup($app_grp_id, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_obt_approval_group tag')
					->group_by('tag.obt_group_code')
					->order_by('tag.obt_group_code');

	  $this->db->where('tag.obt_apprv_grp_id', $app_grp_id);
		
	  return $this->db->get()->result();
	}
	
	public function getApprovalGroupApprover($app_grp_id, $select = '*', $where= array()) {
	  $this->db->select($select, false)
					->from('tk_obt_apprv_grp_approver taga')
					->join('g4_member gm', 'taga.mb_id = gm.mb_no', 'left' )
					;

	  $this->db->where('taga.obt_apprv_grp_id', $app_grp_id);
	
	  foreach($where as $field=>$val) {
	    $this->db->where($field, $val);
	  }
	  return $this->db->get()->result();
	}
	
	public function getApproverGroup($mb_no, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_obt_apprv_grp_approver taga')
					;

	  $this->db->where('taga.mb_id', $mb_no);
	  return $this->db->get()->result();
	}
	
	public function getAllApprovalGroups($show_inactive = false, $select = '*') {
		
		$this->db->select($select, false)
					->from('tk_obt_approval_group tag')
					->group_by('tag.obt_group_code')
					->order_by('tag.obt_group_code');

		if(!$show_inactive)
			$this->db->where('tag.enabled', 1);
		
		return $this->db->get()->result();
	}
	
	public function getAllApprovalGroupsFiltered($show_inactive = false, $select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
		$this->db->select($select, false)
					->from('tk_obt_approval_group tag')
					->group_by('tag.obt_group_code');
		
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

	public function deleteApprovalGroup($param) {
	  return $this->db->delete("tk_obt_approval_group",$param);
	}
	
	public function insertApprovalGroup($data) {
	  return $this->db->insert("tk_obt_approval_group",$data);
	}
	
	public function updateApprovalGroup($data,$param) {
	  return $this->db->update("tk_obt_approval_group",$data,$param);
	}
	
	public function deleteApprovalGroupApprovers($param) {
	  return $this->db->delete("tk_obt_apprv_grp_approver",$param);
	}
	
	public function insertApprovalGroupApprovers($data) {
	  return $this->db->insert("tk_obt_apprv_grp_approver",$data);
	}
	/* End of Approval Groups */
	
	/* Filing */
	public function getEmpOBTApplication($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('tk_obt_application tla')
					->join('g4_member gm', 'tla.mb_no = gm.mb_no' , 'left')
					->join('dept d', 'gm.mb_deptno = d.dept_no' , 'left');
					
					
	  if(!empty($having)) {
		$this->db->having($having);
	  }
	  
	  if($limit > 0)
		  $this->db->limit($limit, $start);
	  
	  if(!empty($order_arr)) {
		foreach($order_arr as $field=>$dir)
		  $this->db->order_by($field,$dir);
	  }
	  // $this->db->get();
	  // echo $this->db->last_query();
	  return $this->db->get()->result();
	}
	
	public function updateOBTApplication($data,$param) {
	  return $this->db->update("tk_obt_application",$data,$param);
	}
	
	public function insertOBTApplication($data) {
	  return $this->db->insert("tk_obt_application",$data);
	}
	
	public function deleteOBTApplication($param) {
	  return $this->db->delete("tk_obt_application",$param);
	}
	/* End of Filing */
	
	/* General Functions */
	public function insertForApprovalOBTApplication($data) {
	  return $this->db->insert("tk_obt_application_approval",$data);
	}
	
	public function updateForApprovalOBTApplication($data,$param) {
	  return $this->db->update("tk_obt_application_approval",$data,$param);
	}
	
	public function deleteForApprovalOBTApplication($param) {
	  return $this->db->delete("tk_obt_application_approval",$param);
	}
	
	public function getAllForApprovalFiltered($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('tk_obt_application_approval tlaa')
					->join('tk_obt_application tla', 'tlaa.obt_app_id = tla.obt_app_id', 'left')
					->join('tk_obt_approval_group tag', 'tlaa.obt_apprv_grp_id = tag.obt_apprv_grp_id', 'left')
					->join('tk_obt_apprv_grp_approver taga', 'tlaa.obt_apprv_grp_id = taga.obt_apprv_grp_id AND taga.mb_id = "'.$this->session->userdata("mb_no").'"',"left")
					->join('g4_member sub', 'tlaa.submitted_by = sub.mb_no' , 'left')
					->join('g4_member apprv', 'tlaa.approved_by = apprv.mb_no', 'left' )
					->group_by('tag.obt_group_code, tlaa.obt_app_id');
					
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

	public function insertForApprovalOBTApplicationHist($data) {
	  return $this->db->insert("tk_obt_application_approval_hist",$data);
	}
	
	public function getEmpOBTApplicationRemarks($select = '*', $having="") {
	   $this->db->select($select, false)
					->from('tk_obt_application_approval_hist tlah')
					->join('g4_member gm', 'tlah.created_by = gm.mb_no', 'left' )
					->order_by('tlah.created_datetime',"DESC");
					
	  if(!empty($having)) {
		$this->db->having($having);
	  }
	  
	  return $this->db->get()->result();
	}
	/* End of General Functions */
	
	public function lastID() {
	  return $this->db->insert_id();
	}
}