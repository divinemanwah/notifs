<?php

class Leaves_model extends CI_Model {

	function __construct() {
        parent::__construct();
    }
	
	public function updateLeave($data,$param) {
	  return $this->db->update("tk_leave_code",$data,$param);
	}
	
	public function insertLeave($data) {
	  return $this->db->insert("tk_leave_code",$data);
	}
	
	public function getLeave($leave_id, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_leave_code tlc')
					->order_by('tlc.leave_code');

	  $this->db->where('tlc.leave_id', $leave_id);
		
	  return $this->db->get()->result();
	}
	
	public function getAllLeaves($show_inactive = false, $select = '*', $join_sub = false) {
		
		$this->db->select($select, false)
					->from('tk_leave_code l')
					->order_by('l.leave_code');

		if($join_sub)
		   $this->db->join('tk_leave_sub_categ tlsc', 'l.leave_id = tlsc.leave_id', 'left');
		if(!$show_inactive)
			$this->db->where('l.status', 1);
		
		return $this->db->get()->result();
	}

	public function getAllLeavesFiltered($show_inactive = false, $select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
		$this->db->select($select, false)
					->from('tk_leave_code l');
		
		if(!empty($having)) {
		  $this->db->having($having);
		}
		
		if($limit > 0)
		  $this->db->limit($limit, $start);
					
		if(!$show_inactive)
			$this->db->where('l.status', 1);
		
		if(!empty($order_arr)) {
		  foreach($order_arr as $field=>$dir)
			$this->db->order_by($field,$dir);
		}
		
		return $this->db->get()->result();
	}
	
	public function insertSubLeave($data) {
	  return $this->db->insert("tk_leave_sub_categ",$data);
	}
	
	public function deleteSubLeave($param) {
	  return $this->db->delete("tk_leave_sub_categ",$param);
	}
	
	public function getSubLeave($leave_id, $select = '*', $where="") {
	  $this->db->select($select, false)
					->from('tk_leave_sub_categ tlsc')
					->order_by('tlsc.sub_categ_code');

	  $this->db->where('tlsc.leave_id', $leave_id);
	
	  if(!empty($where)) {
		$this->db->where($where);
	  }
	  return $this->db->get()->result();
	}
	
	public function insertLeaveRules($data) {
	  return $this->db->insert("tk_leave_rules",$data);
	}

	public function deleteLeaveRules($param) {
	  return $this->db->delete("tk_leave_rules",$param);
	}

	public function getLeaveRules($leave_id, $select = '*', $where=array()) {
	  $this->db->select($select, false)
					->from('tk_leave_rules tlr')
					->join('tk_leave_sub_categ tlsc', 'tlr.sub_categ_id = tlsc.sub_categ_id', 'left')
					->order_by('tlr.rule_id');
			
	  foreach($where as $field=>$val) {
	    $this->db->where($field, $val);
	  }
	  
	  $this->db->where('tlr.leave_id', $leave_id);
		
	  return $this->db->get()->result();
	}

	public function insertDependentLeave($data) {
	  return $this->db->insert("tk_leave_dependents",$data);
	}

	public function deleteDependentLeave($param) {
	  return $this->db->delete("tk_leave_dependents",$param);
	}
	
	public function getDependentLeave($leave_id, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_leave_dependents tld')
					->join('tk_leave_code l', 'l.leave_id = tld.dependent_id', 'left')
					->order_by('tld.leave_dep_id');

	  $this->db->where('tld.leave_id', $leave_id);
		
	  return $this->db->get()->result();
	}
	
	public function updateGeneralSettings($data,$param) {
	  return $this->db->update("tk_general_setting",$data,$param);
	}
	
	public function getGeneralSettings() {
	  $this->db->select("*", false)
					->from('tk_general_setting tgs');
	  
	  return $this->db->get()->result();
	}

	public function getApprovalGroup($app_grp_id, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_lv_approval_group tag')
					->group_by('tag.lv_group_code')
					->order_by('tag.lv_group_code');

	  $this->db->where('tag.lv_apprv_grp_id', $app_grp_id);
		
	  return $this->db->get()->result();
	}
	
	public function getApprovalGroupApprover($app_grp_id, $select = '*', $where= array()) {
	  $this->db->select($select, false)
					->from('tk_lv_apprv_grp_approver taga')
					->join('g4_member gm', 'taga.mb_id = gm.mb_no', 'left' )
					;

	  $this->db->where('taga.lv_apprv_grp_id', $app_grp_id);
	
	  foreach($where as $field=>$val) {
	    $this->db->where($field, $val);
	  }
	  return $this->db->get()->result();
	}
	
	public function getApproverDepartment($mb_no, $select = '*', $dept_id=0) {
	  $this->db->select($select, false)
					->from('tk_lv_apprv_grp_approver taga')
					->join('tk_lv_apprv_grp_dept tagd', 'taga.lv_apprv_grp_id = tagd.lv_apprv_grp_id', "left" )
					;

	  $this->db->where('taga.mb_id', $mb_no);
	  if(!empty($dept_id)) {
	    $this->db->where('tagd.dept_id', $dept_id);
	  }
	  return $this->db->get()->result();
	}

	public function getApproverGroup($mb_no, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_lv_apprv_grp_approver taga')
					;

	  $this->db->where('taga.mb_id', $mb_no);
	  return $this->db->get()->result();
	}
	
	public function getAllApprovalGroups($show_inactive = false, $select = '*') {
		
		$this->db->select($select, false)
					->from('tk_lv_approval_group tag')
					// ->join('tk_lv_apprv_grp_dept tagd', 'tag.lv_apprv_grp_id = tagd.lv_apprv_grp_id', 'left' )
					// ->join('dept d', 'd.dept_no = tagd.dept_id', 'left' )
					->group_by('tag.lv_group_code')
					->order_by('tag.lv_group_code');

		if(!$show_inactive)
			$this->db->where('tag.enabled', 1);
		
		return $this->db->get()->result();
	}
	
	public function getAllApprovalGroupsFiltered($show_inactive = false, $select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
		$this->db->select($select, false)
					->from('tk_lv_approval_group tag')
					// ->join('tk_lv_apprv_grp_dept tagd', 'tag.lv_apprv_grp_id = tagd.lv_apprv_grp_id', 'left' )
					// ->join('dept d', 'd.dept_no = tagd.dept_id', 'left' )
					->group_by('tag.lv_group_code');
		
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
	  return $this->db->delete("tk_lv_approval_group",$param);
	}
	
	public function insertApprovalGroup($data) {
	  return $this->db->insert("tk_lv_approval_group",$data);
	}
	
	public function updateApprovalGroup($data,$param) {
	  return $this->db->update("tk_lv_approval_group",$data,$param);
	}
	
	public function deleteApprovalGroupDepts($param) {
	  return $this->db->delete("tk_lv_apprv_grp_dept",$param);
	}
	
	public function insertApprovalGroupDepts($data) {
	  return $this->db->insert("tk_lv_apprv_grp_dept",$data);
	}
	
	public function deleteApprovalGroupApprovers($param) {
	  return $this->db->delete("tk_lv_apprv_grp_approver",$param);
	}
	
	public function insertApprovalGroupApprovers($data) {
	  return $this->db->insert("tk_lv_apprv_grp_approver",$data);
	}

	public function getEmpLeaveBalances($emp_id, $select="*", $leave_id = 0, $year = null) {
	  if($year == null) {
	    $curDate = new DateTime();
	    $year = $curDate->format("Y");
	  }
	  $this->db->select($select, false)
					->from('tk_lv_balance tlb')
					->join('tk_leave_code tlc', 'tlb.leave_id = tlc.leave_id', 'left' );

	  $this->db->where('tlb.mb_no', $emp_id);
	  $this->db->where('tlb.year', $year);
	  if(!empty($leave_id))
	    $this->db->where('tlb.leave_id', $leave_id);
		
	  return $this->db->get()->result();
	}
	
	public function updateEmpLeaveBalances($data,$param) {
	  return $this->db->update("tk_lv_balance",$data,$param);
	}
	
        public function setupdateEmpLeaveBalances($data,$param){
            $lvupdate = $this->db->where($param,NULL,FALSE); 
            foreach($data as $key => $val) $lvupdate = $lvupdate->set($key,$val,FALSE);
            $lvupdate = $lvupdate->update("tk_lv_balance");
                return $lvupdate;
        }
        
	public function insertEmpLeaveBalances($data) {
	  return $this->db->insert("tk_lv_balance",$data);
	}
	
	public function getEmpLeaveApplication($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('tk_lv_application tla')
					->join('g4_member gm', 'tla.mb_no = gm.mb_no' , 'left')
					->join('dept d', 'gm.mb_deptno = d.dept_no' , 'left')
					->join('tk_leave_sub_categ tlsc', 'tla.sub_categ_id = tlsc.sub_categ_id' , 'left')
					->join('tk_leave_code tlc', 'tlc.leave_id = tla.leave_id' , 'left');
					
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
	
	public function updateLeaveApplication($data,$param) {
	  return $this->db->update("tk_lv_application",$data,$param);
	}
        
	public function setupdateLeaveApplication($data,$param){
            $lvupdate = $this->db->where($param,NULL,FALSE); 
            foreach($data as $key => $val) $lvupdate = $lvupdate->set($key,$val,FALSE);
            $lvupdate = $lvupdate->update("tk_lv_application");
                return $lvupdate;
        }
        
	public function insertLeaveApplication($data) {
	  return $this->db->insert("tk_lv_application",$data);
	}
	
	public function deleteLeaveApplication($param) {
	  return $this->db->delete("tk_lv_application",$param);
	}
	
	public function insertForApprovalLeaveApplication($data) {
	  return $this->db->insert("tk_lv_application_approval",$data);
	}
	
	public function updateForApprovalLeaveApplication($data,$param) {
	  return $this->db->update("tk_lv_application_approval",$data,$param);
	}
	
	public function deleteForApprovalLeaveApplication($param) {
	  return $this->db->delete("tk_lv_application_approval",$param);
	}

	public function getAllForApprovalFiltered($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('tk_lv_application_approval tlaa')
					->join('tk_lv_application tla', 'tlaa.lv_app_id = tla.lv_app_id', 'left')
					->join('tk_leave_code tlc', 'tlc.leave_id = tla.leave_id' , 'left')
					->join('tk_lv_approval_group tag', 'tlaa.lv_apprv_grp_id = tag.lv_apprv_grp_id', 'left')
					->join('tk_lv_apprv_grp_approver taga', 'tlaa.lv_apprv_grp_id = taga.lv_apprv_grp_id AND taga.mb_id = "'.$this->session->userdata("mb_no").'"',"left")
					->join('g4_member sub', 'tlaa.submitted_by = sub.mb_no' , 'left')
					->join('g4_member apprv', 'tlaa.approved_by = apprv.mb_no', 'left' )
					->group_by('tag.lv_group_code, tlaa.lv_app_id DESC');
					
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

	public function insertForApprovalLeaveApplicationHist($data) {
	  return $this->db->insert("tk_lv_application_approval_hist",$data);
	}
	
	public function getEmpLeaveApplicationRemarks($select = '*', $having="") {
	   $this->db->select($select, false)
					->from('tk_lv_application_approval_hist tlah')
					->join('g4_member gm', 'tlah.created_by = gm.mb_no', 'left' )
					->order_by('tlah.created_datetime',"DESC");
					
	  if(!empty($having)) {
		$this->db->having($having);
	  }
	  
	  return $this->db->get()->result();
	}
	
	
	public function insertMC($data){
	  return $this->db->insert("tk_lv_mc",$data);
	}
	
	public function updateMC($data,$param){
	  return $this->db->update("tk_lv_mc",$data,$param);
	}
	
	public function getMC($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('tk_lv_mc tlm')
					->join('g4_member gm', 'tlm.mb_no = gm.mb_no' , 'left')
					->join('dept d', 'gm.mb_deptno = d.dept_no' , 'left')
					->join('g4_member creator', 'tlm.created_by = creator.mb_no' , 'left');
					
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
	
	public function deleteMC($param){
	  return $this->db->delete("tk_lv_mc",$param);
	}
	
	public function lastID() {
	  return $this->db->insert_id();
	}
}