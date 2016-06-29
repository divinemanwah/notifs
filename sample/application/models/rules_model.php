<?php

class Rules_model extends CI_Model {

	function __construct() {
        parent::__construct();
    }
	
	public function get($id) {
		
		return $this->db->select('r.*, m.mb_nick as created_by_nick, m2.mb_nick as updated_by_nick')
						->join('g4_member m', 'r.created_by = m.mb_no', 'inner')
						->join('g4_member m2', 'r.updated_by = m2.mb_no', 'left')
						->get_where('hr_violation_rules r', array('r.violation_id' => $id, 'r.status <' => 2))
						->result();

	}
	
	public function getAll($type, $show_disabled = false) {
	
		$t = false;
		
		switch($type) {
			case 1:
			
				$t = 'hr_violations';
			
				break;
			case 2:
			
				$t = 'hr_member_violations';
			
				break;
		}
		
		if($t) {
		
			if($type == 2)
				$this->db->select('v.*, v2.description, m.mb_id, m.mb_fname, m.mb_lname, d.dept_name, m2.mb_nick as created_by_nick, m3.mb_nick as updated_by_nick');
		
			$this->db->from("$t v");
			
			if($type == 2)
				$this->db
						->join('hr_violations v2', 'v.violation_id = v2.id')
						->join('g4_member m', 'v.employee_id = m.mb_no', 'inner')
						->join('g4_member m2', 'v.created_by = m2.mb_no', 'inner')
						->join('g4_member m3', 'v.updated_by = m3.mb_no', 'left')
						->join('dept d', 'm.mb_deptno = d.dept_no');
			
			$this->db->where('v.status <', 2)->order_by('v.id', 'desc');

			if(!$show_disabled)
				$this->db->where('v.status', 1);
		}
		
		return $t ? $this->db->get()->result() : array();
	}
	
	public function add($data) {
	
		$currdate = new DateTime(null, new DateTimeZone('Asia/Manila'));
		
		return is_array($data) && array_key_exists('v', $data) && $this->db->insert('hr_violation_rules', array(
				'violation_id' => $data['v'],
				'repetition' => $data['r'],
				'type' => $data['t'],
				'offense_id' => $data['o'],
				'minus' => $data['m1'],
				'subsequent_minus' => $data['m2'],
				'status' => 1,
				'created_date' => $currdate->format('Y-m-d H:i:s'),
				'created_by' => $this->session->userdata('mb_no')
			));;
		
	}
	
	public function update($post) {
		
		$currdate = new DateTime(null, new DateTimeZone('Asia/Manila'));
		
		return $this->db->update('hr_violation_rules', array(
				'repetition' => intval($post['r']),
				'type' => intval($post['t']),
				'offense_id' => intval($post['o']),
				'minus' => floatval($post['m1']),
				'subsequent_minus' => floatval($post['m2']),
				'updated_date' => $currdate->format('Y-m-d H:i:s'),
				'updated_by' => $this->session->userdata('mb_no')
			), array(
				'id' => intval($post['id']),
				'violation_id' => intval($post['v'])
			));
		
	}
	
	public function remove($id) {
		
		$currdate = new DateTime(null, new DateTimeZone('Asia/Manila'));
		
		return $this->db->update('hr_violation_rules', array(
				'status' => 2,
				'updated_date' => $currdate->format('Y-m-d H:i:s'),
				'updated_by' => $this->session->userdata('mb_no')
			), array(
				'id' => $id
			));
		
	}
}