<?php

class Sms_model extends CI_Model {

	function __construct() {
        parent::__construct();
    }
	
	public function insertSMS($data) {
	  return $this->db->insert("hr_sms_incoming",$data);
	}
	
	public function getAllMessagesFiltered($select = '*', $having="", $start=0, $limit=0, $order_arr=array()) {
	  $this->db->select($select, false)
					->from('hr_sms_incoming hsi')
					->join('g4_member gm', 'gm.mb_id = hsi.mb_id', 'left')
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
	  return $this->db->get()->result();
	}
	
}