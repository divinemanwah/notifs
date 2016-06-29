<?php

class Shifts_model extends CI_Model {

	function __construct() {
        parent::__construct();
    }
	
	/* Shift Code */
	
	public function getAll($show_inactive = false, $select = '*', $where = "") {
		
		$this->db->select($select, false)
					->from('tk_shift_code s')
					->order_by('s.shift_code');

		if(!$show_inactive)
			$this->db->where('s.enabled', 1);
		
		if(!empty($where)) {
		  $this->db->where($where);
	    }
		
		return $this->db->get()->result();
	}

	public function getAllFiltered($show_inactive = false, $select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
		$this->db->select($select, false)
					->from('tk_shift_code s');
		
		if(!empty($having)) {
		  $this->db->having($having);
		}
		
		if($limit > 0)
		  $this->db->limit($limit, $start);
					
		if(!$show_inactive)
			$this->db->where('s.enabled', 1);
		
		if(!empty($order_arr)) {
		  foreach($order_arr as $field=>$dir)
			$this->db->order_by($field,$dir);
		}
		
		return $this->db->get()->result();
	}
	
	public function updateShift($data,$param) {
	  return $this->db->update("tk_shift_code",$data,$param);
	}
	
	public function insertShift($data) {
	  return $this->db->insert("tk_shift_code",$data);
	}
	
	/* End of Shift Code */
	
	public function getAllUploads($select = '*') {
		
		$this->db->select($select, false)
					->from( 'tk_sched_upload tsu' )
					->join( 'tk_approval_group tag', 'tsu.apprv_grp_id = tag.apprv_grp_id' )
					->join( 'g4_member gm', 'tsu.updated_by = gm.mb_no' );
		
		return $this->db->get()->result();
	}

	public function getAllUploadsFiltered($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
		$this->db->select($select, false)
					->from('tk_sched_upload tsu')
					->join( 'tk_approval_group tag', 'tsu.apprv_grp_id = tag.apprv_grp_id' )
					->join( 'g4_member gm', 'tsu.updated_by = gm.mb_no' );
		
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
	
	public function getAllForApprovalFiltered($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('tk_sched_approval tsa')
					->join('tk_sched_upload tsu', 'tsa.upload_id = tsu.upload_id')
					->join('tk_approval_group tag', 'tsa.apprv_grp_id = tag.apprv_grp_id')
					->join('tk_apprv_grp_approver taga', 'tsa.apprv_grp_id = taga.apprv_grp_id AND taga.mb_id = "'.$this->session->userdata("mb_no").'"',"left")
					->join('g4_member sub', 'tsa.submitted_by = sub.mb_no' )
					->join('g4_member apprv', 'tsa.approved_by = apprv.mb_no', 'left' )
					->group_by('tag.group_code, tsa.upload_id DESC');
					
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
	
	public function getApprovalGroup($app_grp_id, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_approval_group tag')
					->order_by('tag.group_code');

	  $this->db->where('tag.apprv_grp_id', $app_grp_id);
		
	  return $this->db->get()->result();
	}
	
	public function getAllMemberScheduleFiltered($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('tk_member_schedule tsa');
	  
	  if(!empty($having)) {
		$this->db->having($having);
	  }
	
	  return $this->db->get()->result();
	}
	
	public function getUploaderDepartment($mb_no, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_apprv_grp_uploader tagu')
					->join('tk_apprv_grp_dept tagd', 'tagu.apprv_grp_id = tagd.apprv_grp_id',"left" )
					->join('tk_approval_group tag', 'tagu.apprv_grp_id = tag.apprv_grp_id',"left" )
					;

	  $this->db->where('tagu.mb_id', $mb_no);
		
	  return $this->db->get()->result();
	}
	
	public function getUploaderGroup($mb_no, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_apprv_grp_uploader tagu')
					->join('tk_approval_group tag', 'tagu.apprv_grp_id = tag.apprv_grp_id',"left" )
					;

	  $this->db->where('tagu.mb_id', $mb_no);
	  return $this->db->get()->result();
	}
	
	public function getApproverGroup($mb_no, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_apprv_grp_approver taga')
					;

	  $this->db->where('taga.mb_id', $mb_no);
	  return $this->db->get()->result();
	}
	
	public function getApprovalGroupUploader($app_grp_id, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_apprv_grp_uploader tagu')
					->join('g4_member gm', 'tagu.mb_id = gm.mb_no', 'inner' )
					;

	  $this->db->where('tagu.apprv_grp_id', $app_grp_id);
		
	  return $this->db->get()->result();
	}
	
	public function getApprovalGroupApprover($app_grp_id, $select = '*', $where= array()) {
	  $this->db->select($select, false)
					->from('tk_apprv_grp_approver taga')
					->join('g4_member gm', 'taga.mb_id = gm.mb_no', 'inner' )
					;

	  $this->db->where('taga.apprv_grp_id', $app_grp_id);
	
	  foreach($where as $field=>$val){
	    $this->db->where($field, $val);
	  }
	
	  return $this->db->get()->result();
	}
	
	public function getAllApprovalGroups($show_inactive = false, $select = '*') {
		
		$this->db->select($select, false)
					->from('tk_approval_group tag')
					// ->join('tk_apprv_grp_dept tagd', 'tag.apprv_grp_id = tagd.apprv_grp_id', 'left' )
					// ->join('dept d', 'd.dept_no = tagd.dept_id', 'left' )
					->group_by('tag.group_code')
					->order_by('tag.group_code');

		if(!$show_inactive)
			$this->db->where('tag.enabled', 1);
		
		return $this->db->get()->result();
	}
	
	public function getAllApprovalGroupsFiltered($show_inactive = false, $select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
		$this->db->select($select, false)
					->from('tk_approval_group tag')
					->join('tk_apprv_grp_dept tagd', 'tag.apprv_grp_id = tagd.apprv_grp_id', 'left' )
					->join('dept d', 'd.dept_no = tagd.dept_id', 'left' )
					->group_by('tag.group_code');
		
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
	
	public function getShift($shift_id, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_shift_code tsc')
					->order_by('tsc.shift_code');

	  $this->db->where('tsc.shift_id', $shift_id);
		
	  return $this->db->get()->result();
	}
	
	public function getEmployeeSchedulesDept($show_inactive = false, $select = '*', $dept = "",  $start=0, $limit=0, $order_arr=array(), $where_arr = array()) {
	  $this->db->select($select, false)
					->from('tk_member_schedule tms')
					->join('g4_member gm', 'tms.mb_no = gm.mb_no', 'inner' )
					->join('dept d', 'gm.mb_deptno = d.dept_no' , 'inner')
					->join('tk_shift_code tsc', 'tsc.shift_id = tms.shift_id' , 'left')
					->join('dept_group_mem_assign gma', 'gma.mb_no = gm.mb_no', 'left')
				->join('dept_group dept', 'dept.id = gma.group_id and dept.dept_status = 1', 'left');
	  
	  if (!empty($where_arr)) {
		foreach ($where_arr as $field => $val) {
			$this->db->where($field, $val);
		}
	  }
	  
	  if ($dept && is_numeric($dept))
            $this->db->where('gm.mb_deptno', $dept);
	  
	  if(!empty($order_arr)) {
		foreach($order_arr as $field=>$dir)
		  $this->db->order_by($field,$dir);
	  }
	  if (!$show_inactive)
        $this->db->where('gm.mb_status', 1);
			
	  if($limit > 0)
		  $this->db->limit($limit, $start);
	  
	  return $this->db;
	}

	public function getGeneralSettings() {
	  $this->db->select("*", false)
					->from('tk_general_setting tgs');
	  
	  return $this->db->get()->result();
	}
	
        public function getphshift($select = '*',$where="",$start=0,$limit=0,$order_arr=array()){
           $this->db->select($select, false)
                                        ->from('tk_member_schedule tms')
					->join('g4_member gm','gm.mb_no = tms.mb_no','left')
					->join('hr_holiday_mem_res hhmr', 'hhmr.mb_no = tms.mb_no' , 'left')
					->join('tk_lv_application tla', 'tla.mb_no = tms.mb_no and tms.leave_id = tla.leave_id and tms.lv_app_id = tla.lv_app_id and tla.status = 3 and date_format(concat(tms.year,\'-\',tms.month,\'-\',tms.day),\'%Y-%m-%d\') between tla.date_from and tla.date_to' , 'left')
					->join('tk_lv_balance tlb', 'tla.leave_id = tlb.leave_id and tla.mb_no = tlb.mb_no ' , 'left')
					->join('tk_change_sched_req tcsr', 'tcsr.mb_no = tms.mb_no and date_format(concat(tms.year,\'-\',tms.month,\'-\',tms.day),\'%Y-%m-%d\') between tcsr.att_date_from and tcsr.att_date_to and tcsr.status = 3' , 'left')
            ;
           if(!empty($where)) {
		$this->db->where($where);
	  }
	  
	  if(!empty($order_arr)) {
		foreach($order_arr as $field=>$dir)
		  $this->db->order_by($field,$dir);
	  }
	  
	  if($limit > 0)
		  $this->db->limit($limit, $start);
	  
	  return $this->db->get()->result();
        }
        
         
       
	public function getEmployeeSchedules($select = '*', $where="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('tk_member_schedule tms')
					->join('tk_shift_code tsc', 'tsc.shift_id = tms.shift_id' , 'left');
					
	  if(!empty($where)) {
		$this->db->where($where);
	  }
	  
	  if(!empty($order_arr)) {
		foreach($order_arr as $field=>$dir)
		  $this->db->order_by($field,$dir);
	  }
	  
	  if($limit > 0)
		  $this->db->limit($limit, $start);
	  
	  return $this->db->get()->result();
	}
	
	public function insertMemberSchedule($data) {
	  return $this->db->insert("tk_member_schedule",$data);
	}
	
	public function updateMemberSchedule($data,$param) {
	  return $this->db->update("tk_member_schedule",$data,$param);
	}
        
        public function checkoldmemberschedule($select,$where){
            return $this->db->select($select)
                        ->from('tk_member_schedule tms')
                        ->join('tk_mem_sched_holiday_his tmshh','tmshh.tkms_id = tms.tkms_id','right')
                        ->join('tk_lv_balance tlb','tlb.mb_no = tms.mb_no and tms.leave_id = tlb.leave_id','left')
                        ->where($where)
                        ->get()
                        ->result();
        }
        public function noshiftlvhistory($where){
            return $this->db->query("insert into tk_mem_sched_holiday_his select * from tk_member_schedule where ".$where);
        }
        
        public function rollbackmemberschedule($where){
            $rollbck = $this->db->where($where,NULL,FALSE); 
            $rollbck = $rollbck->set("tms.shift_id","tmshh.shift_id",FALSE);
            $rollbck = $rollbck->update("tk_member_schedule tms right join tk_mem_sched_holiday_his tmshh on tmshh.tkms_id = tms.tkms_id");
                return $rollbck;
        }
        public function deleteoldhistorysched($where){
            return $this->db->query("delete from tk_mem_sched_holiday_his where ".$where);
        }
	public function insertForApproval($data) {
	  return $this->db->insert("tk_sched_approval",$data);
	}
	
	public function updateForApproval($data,$param) {
	  return $this->db->update("tk_sched_approval",$data,$param);
	}
	
	public function deleteForApproval($param) {
	  return $this->db->delete("tk_sched_approval",$param);
	}
	
	public function updateGeneralSettings($data,$param) {
	  return $this->db->update("tk_general_setting",$data,$param);
	}
	
	public function deleteApprovalGroup($param) {
	  return $this->db->delete("tk_approval_group",$param);
	}
	
	public function insertApprovalGroup($data) {
	  return $this->db->insert("tk_approval_group",$data);
	}
	
	public function updateApprovalGroup($data,$param) {
	  return $this->db->update("tk_approval_group",$data,$param);
	}
	
	public function deleteApprovalGroupDepts($param) {
	  return $this->db->delete("tk_apprv_grp_dept",$param);
	}
	
	public function insertApprovalGroupDepts($data) {
	  return $this->db->insert("tk_apprv_grp_dept",$data);
	}
	
	public function deleteApprovalGroupUploaders($param) {
	  return $this->db->delete("tk_apprv_grp_uploader",$param);
	}
	
	public function insertApprovalGroupUploaders($data) {
	  return $this->db->insert("tk_apprv_grp_uploader",$data);
	}
	
	public function deleteApprovalGroupApprovers($param) {
	  return $this->db->delete("tk_apprv_grp_approver",$param);
	}
	
	public function insertApprovalGroupApprovers($data) {
	  return $this->db->insert("tk_apprv_grp_approver",$data);
	}

	public function updateSchedUpload($data,$param) {
	  return $this->db->update("tk_sched_upload",$data,$param);
	}
	
	public function insertSchedUpload($data) {
	  return $this->db->insert("tk_sched_upload",$data);
	}

	public function deleteSchedUpload($param) {
	  return $this->db->delete("tk_sched_upload",$param);
	}

	public function updateChangeShift($data,$param) {
	  return $this->db->update("tk_change_sched_req",$data,$param);
	}
	
	public function insertChangeShift($data) {
	  return $this->db->insert("tk_change_sched_req",$data);
	}
	
	public function deleteChangeShift($param) {
	  return $this->db->delete("tk_change_sched_req",$param);
	}
	
	public function insertForApprovalChangeShift($data) {
	  return $this->db->insert("tk_change_sched_approval",$data);
	}
	
	public function updateForApprovalChangeShift($data,$param) {
	  return $this->db->update("tk_change_sched_approval",$data,$param);
	}
	
	public function deleteForApprovalChangeShift($param) {
	  return $this->db->delete("tk_change_sched_approval",$param);
	}

	public function getAllForApprovalChangeShiftFiltered($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('tk_change_sched_approval tcsa')
					->join('tk_change_sched_req tcsr', 'tcsa.cs_req_id = tcsr.cs_req_id',"left")
					->join('tk_cws_approval_group tag', 'tcsa.apprv_grp_id = tag.cws_apprv_grp_id',"left")
					->join('tk_cws_apprv_grp_approver taga', 'tcsa.apprv_grp_id = taga.cws_apprv_grp_id AND taga.mb_id = "'.$this->session->userdata("mb_no").'"',"left")
					->join('g4_member sub', 'tcsa.submitted_by = sub.mb_no' ,"left")
					->join('g4_member apprv', 'tcsa.approved_by = apprv.mb_no', 'left' ,"left")
					->group_by('tag.group_code, tcsa.cs_req_id DESC');
					
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

	public function insertForApprovalChangeShiftHist($data) {
	  return $this->db->insert("tk_change_sched_hist",$data);
	}
	
	public function getEmployeeChangeSchedulesRemarks($select = '*', $having="") {
	  $this->db->select($select, false)
					->from('tk_change_sched_hist tcsh')
					->join('g4_member gm', 'tcsh.created_by = gm.mb_no' ,"left")
					->order_by('tcsh.created_datetime',"DESC");
					
	  if(!empty($having)) {
		$this->db->having($having);
	  }
	  return $this->db->get()->result();
	}
	
	public function insertForApprovalSchedHist($data) {
	  return $this->db->insert("tk_sched_approval_hist",$data);
	}
	
	public function getUploadSchedRemarks($select = '*', $having="") {
	   $this->db->select($select, false)
					->from('tk_sched_approval_hist tsah')
					->join('g4_member gm', 'tsah.created_by = gm.mb_no', 'left' )
					->order_by('tsah.created_datetime',"DESC");
					
	  if(!empty($having)) {
		$this->db->having($having);
	  }
	  
	  return $this->db->get()->result();
	}

	/* Special Schedule */
	
	public function getAllSpecialUploads($select = '*') {
		
		$this->db->select($select, false)
					->from( 'tk_sched_sp_upload tsu' )
					->join( 'g4_member gm', 'tsu.updated_by = gm.mb_no' );
		
		return $this->db->get()->result();
	}

	public function getAllSpecialUploadsFiltered($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
		$this->db->select($select, false)
					->from('tk_sched_sp_upload tsu')
					->join( 'g4_member gm', 'tsu.updated_by = gm.mb_no' );
		
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
	
	public function insertSpecialSchedUpload($data) {
	  return $this->db->insert("tk_sched_sp_upload",$data);
	}

	public function updateSpecialSchedUpload($data,$param) {
	  return $this->db->update("tk_sched_sp_upload",$data,$param);
	}
	
	public function deleteSpecialSchedUpload($param) {
	  return $this->db->delete("tk_sched_sp_upload",$param);
	}

	/* End of Special Schedule */
	
	/* CWS Approval */
	public function getAllCWSApprovalGroups($show_inactive = false, $select = '*') {
		$this->db->select($select, false)
					->from('tk_cws_approval_group tag')
					->group_by('tag.group_code')
					->order_by('tag.group_code');

		if(!$show_inactive)
			$this->db->where('tag.enabled', 1);
		
		return $this->db->get()->result();
	}
	
	public function getAllCWSApprovalGroupsFiltered($show_inactive = false, $select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
		$this->db->select($select, false)
					->from('tk_cws_approval_group tag')
					->group_by('tag.group_code');
		
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
	
	public function getCWSApprovalGroup($app_grp_id, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_cws_approval_group tag')
					->group_by('tag.group_code')
					->order_by('tag.group_code');

	  $this->db->where('tag.cws_apprv_grp_id', $app_grp_id);
		
	  return $this->db->get()->result();
	}
	
	public function getCWSApprovalGroupApprover($app_grp_id, $select = '*', $where= array()) {
	  $this->db->select($select, false)
					->from('tk_cws_apprv_grp_approver taga')
					->join('g4_member gm', 'taga.mb_id = gm.mb_no' )
					;

	  $this->db->where('taga.cws_apprv_grp_id', $app_grp_id);
	
	  foreach($where as $field=>$val){
	    $this->db->where($field, $val);
	  }
	
	  return $this->db->get()->result();
	}
	
	public function insertCWSApprovalGroup($data) {
	  return $this->db->insert("tk_cws_approval_group",$data);
	}
	
	public function updateCWSApprovalGroup($data,$param) {
	  return $this->db->update("tk_cws_approval_group",$data,$param);
	}
	
	public function deleteCWSApprovalGroup($param) {
	  return $this->db->delete("tk_cws_approval_group",$param);
	}
	
	public function deleteCWSApprovalGroupApprovers($param) {
	  return $this->db->delete("tk_cws_apprv_grp_approver",$param);
	}
	
	public function insertCWSApprovalGroupApprovers($data) {
	  return $this->db->insert("tk_cws_apprv_grp_approver",$data);
	}

	public function getCWSApproverGroup($mb_no, $select = '*') {
	  $this->db->select($select, false)
					->from('tk_cws_apprv_grp_approver taga')
					;

	  $this->db->where('taga.mb_id', $mb_no);
	  return $this->db->get()->result();
	}
	
	public function getEmployeeChangeSchedules($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('g4_member gm')
					->join('dept d', 'gm.mb_deptno = d.dept_no' , 'left')
					->join('tk_change_sched_req tcsq', 'tcsq.mb_no = gm.mb_no' , 'left')
					->join('tk_shift_code tsc2', 'tsc2.shift_id = tcsq.proposed_shift_id' , 'left');
					
	  if(!empty($having)) {
		$this->db->having($having);
	  }
	  
	  if($limit > 0)
		  $this->db->limit($limit, $start);
	  
	  return $this->db->get()->result();
	}
	
	/* End of CWS Approval */
	
	public function lastID() {
	  return $this->db->insert_id();
	}
	
}