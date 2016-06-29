<?php

class Cite_model extends CI_Model {

	function __construct() {
        parent::__construct();
    }
	
	public function get($type, $id) {
	
		switch($type) {
			case 'offense':
			
				$type = 'offenses';
			
				break;
			case 'penalty':
			
				$type = 'penalties';
			
				break;
		}
		
		return $this->db->select('t.*, m.mb_nick as created_by_nick, m2.mb_nick as updated_by_nick')
						->join('g4_member m', 't.created_by = m.mb_no', 'inner')
						->join('g4_member m2', 't.updated_by = m2.mb_no', 'left')
						->get_where("hr_$type t", array('t.id' => $id))
						->row();
		
	}
	
	public function getAll($type, $show_disabled = false, $cite_filter = null, $this_month = false) {
	
		switch($type) {
			case 'offense':
			
				$type = 'offenses';
			
				break;
			case 'penalty':
			
				$type = 'penalties';
			
				break;
			case 'cite':
			
				$type = 'cites';
				
				break;
		}
		
		$this->db->from("hr_$type t")->order_by('t.id', 'desc');

		if(!$show_disabled)
			$this->db->where('t.status', 1);
		
		if($type == 'cites') {
		
			$this->db
				// ->select("t.*, m.mb_lname, m.mb_fname, m.mb_nick, d.dept_name, m.mb_3, m.mb_sex, COALESCE(null) as supervisor, o.description as offense, p.description as penalty, t2.similar_week, t3.similar_month, m1.mb_nick as created_by_nick, m2.mb_nick as updated_by_nick, o.type as offense_type")
				->select("t.*, m.mb_lname, m.mb_fname, m.mb_nick, d.dept_name, m.mb_3, m.mb_sex, i.nick_name as supervisor, o.description as offense, p.description as penalty, m1.mb_nick as created_by_nick, m2.mb_nick as updated_by_nick")
				// ->join('(select t.employee_id, t.offense_id, t.commission_date, count(t.id) as similar_week from hr_cites t group by t.employee_id, yearweek(t.commission_date, 1)) t2', 't.employee_id = t2.employee_id and t.offense_id = t2.offense_id and yearweek(t.commission_date, 1) = yearweek(t2.commission_date, 1)', 'left')
				// ->join('(select t.employee_id, t.offense_id, t.commission_date, count(t.id) as similar_month, month(t.commission_date) as imonth, year(t.commission_date) as iyear from hr_cites t group by t.employee_id, month(t.commission_date), year(t.commission_date)) t3', 't.employee_id = t3.employee_id and t.offense_id = t3.offense_id and month(t.commission_date) = t3.imonth and year(t.commission_date) = t3.iyear', 'left')
				->join('g4_member m', 't.employee_id = m.mb_no', 'inner')
				->join('dept d', 'm.mb_deptno = d.dept_no', 'inner')
				->join('g4_member m1', 't.created_by = m1.mb_no', 'inner')
				->join('g4_member m2', 't.updated_by = m2.mb_no', 'left')
				->join('hr_offenses o', 't.offense_id = o.id', 'inner')
				->join('hr_penalties p', 't.penalty_id = p.id', 'left')
				->join('hr_dept_heads h', 'h.dept_id = m.mb_deptno', 'left')
				->join('users_info i', 'i.hr_users_id = h.employee_id', 'left');
			
			if($cite_filter != null)
				$this->db->where('t.status', $cite_filter);
			
			if($this_month) {
				
				$curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));
				
				$this->db->where(array(
					'month(t.nte_date)' => $curr_date->format('n'),
					'year(t.nte_date)' => $curr_date->format('Y')
				));
			}
		}
		else
			$this->db->where('t.status <', 2);

		return $this->db->get()->result();
	}
	
	public function getAllByID($id, $type, $show_disabled = false, $cite_filter = null) {
	
		switch($type) {
			case 'offense':
					
				$type = 'offenses';
					
				break;
			case 'penalty':
					
				$type = 'penalties';
					
				break;
			case 'cite':
					
				$type = 'cites';
	
				break;
		}
	
		$this->db->from("hr_$type t")->order_by('t.id', 'desc');
	
		if(!$show_disabled)
			$this->db->where('t.status', 1);
	
		if($type == 'cites') {
	
			$this->db
				// ->select("t.*, m.mb_lname, m.mb_fname, m.mb_nick, d.dept_name, m.mb_3, m.mb_sex, COALESCE(null) as supervisor, o.description as offense, p.description as penalty, t2.similar_week, t3.similar_month, m1.mb_nick as created_by_nick, m2.mb_nick as updated_by_nick, o.type as offense_type")
				->select("t.*, m.mb_lname, m.mb_fname, m.mb_nick, d.dept_name, m.mb_3, m.mb_sex, i.nick_name as supervisor, o.description as offense, p.description as penalty, m1.mb_nick as created_by_nick, m2.mb_nick as updated_by_nick")
				// ->join('(select t.employee_id, t.offense_id, t.commission_date, count(t.id) as similar_week from hr_cites t group by t.employee_id, yearweek(t.commission_date, 1)) t2', 't.employee_id = t2.employee_id and t.offense_id = t2.offense_id and yearweek(t.commission_date, 1) = yearweek(t2.commission_date, 1)', 'left')
				// ->join('(select t.employee_id, t.offense_id, t.commission_date, count(t.id) as similar_month, month(t.commission_date) as imonth, year(t.commission_date) as iyear from hr_cites t group by t.employee_id, month(t.commission_date), year(t.commission_date)) t3', 't.employee_id = t3.employee_id and t.offense_id = t3.offense_id and month(t.commission_date) = t3.imonth and year(t.commission_date) = t3.iyear', 'left')
				->join('g4_member m', 't.employee_id = m.mb_no', 'inner')
				->join('dept d', 'm.mb_deptno = d.dept_no', 'inner')
				->join('g4_member m1', 't.created_by = m1.mb_no', 'inner')
				->join('g4_member m2', 't.updated_by = m2.mb_no', 'left')
				->join('hr_offenses o', 't.offense_id = o.id', 'inner')
				->join('hr_penalties p', 't.penalty_id = p.id', 'left')
				->join('hr_dept_heads h', 'h.dept_id = m.mb_deptno', 'left')
				->join('users_info i', 'i.hr_users_id = h.employee_id', 'left')
				->where(array(
						't.employee_id' => $id,
						't.status !=' => 0 
				));
				
			if($cite_filter != null)
				$this->db->where('t.status', $cite_filter);
		}
		else
			$this->db->where('t.status <', 2);
	
		return $this->db->get()->result();
	}
	
	public function getCiteDetails($id) {
	
		$this->db->from('hr_cite t')->order_by('t.id', 'desc');
	
		if(!$show_disabled)
			$this->db->where('t.status', 1);
	
		if($type == 'cites') {
	
			$this->db
			// ->select("t.*, m.mb_lname, m.mb_fname, m.mb_nick, d.dept_name, m.mb_3, m.mb_sex, COALESCE(null) as supervisor, o.description as offense, p.description as penalty, t2.similar_week, t3.similar_month, m1.mb_nick as created_by_nick, m2.mb_nick as updated_by_nick, o.type as offense_type")
			->select("t.*, m.mb_lname, m.mb_fname, m.mb_nick, d.dept_name, m.mb_3, m.mb_sex, i.nick_name as supervisor, o.description as offense, p.description as penalty, m1.mb_nick as created_by_nick, m2.mb_nick as updated_by_nick")
			// ->join('(select t.employee_id, t.offense_id, t.commission_date, count(t.id) as similar_week from hr_cites t group by t.employee_id, yearweek(t.commission_date, 1)) t2', 't.employee_id = t2.employee_id and t.offense_id = t2.offense_id and yearweek(t.commission_date, 1) = yearweek(t2.commission_date, 1)', 'left')
			// ->join('(select t.employee_id, t.offense_id, t.commission_date, count(t.id) as similar_month, month(t.commission_date) as imonth, year(t.commission_date) as iyear from hr_cites t group by t.employee_id, month(t.commission_date), year(t.commission_date)) t3', 't.employee_id = t3.employee_id and t.offense_id = t3.offense_id and month(t.commission_date) = t3.imonth and year(t.commission_date) = t3.iyear', 'left')
			->join('g4_member m', 't.employee_id = m.mb_no', 'inner')
			->join('dept d', 'm.mb_deptno = d.dept_no', 'inner')
			->join('g4_member m1', 't.created_by = m1.mb_no', 'inner')
			->join('g4_member m2', 't.updated_by = m2.mb_no', 'left')
			->join('hr_offenses o', 't.offense_id = o.id', 'inner')
			->join('hr_penalties p', 't.penalty_id = p.id', 'left')
			->join('hr_dept_heads h', 'h.dept_id = m.mb_deptno', 'left')
			->join('users_info i', 'i.hr_users_id = h.employee_id', 'left');
				
			if($cite_filter != null)
				$this->db->where('t.status', $cite_filter);
		}
		else
			$this->db->where('t.status <', 2);
	
		return $this->db->get()->result();
	}
	
	public function add($type, $data) {
	
		$currdate = new DateTime(null, new DateTimeZone('Asia/Manila'));
		
		switch($type) {
			case 'offense':
			
				$type = 'offenses';
			
				break;
			case 'penalty':
			
				$type = 'penalties';
			
				break;
		}
		
		return $this->db->insert("hr_$type", array(
				'description' => trim($data),
				'status' => 1,
				'created_date' => $currdate->format('Y-m-d H:i:s'),
				'created_by' => $this->session->userdata('mb_no')
			));
		
	}
	
	public function update($type, $id, $data) {
		
		$currdate = new DateTime(null, new DateTimeZone('Asia/Manila'));
		
		switch($type) {
			case 'offense':
			
				$type = 'offenses';
			
				break;
			case 'penalty':
			
				$type = 'penalties';
			
				break;
			case 'cite':
			
				$type = 'cites';
			
				break;
		}
		
		array_walk($data, function (&$v, $k) { if(is_string($v) && strlen($v)) $v = trim($v); else $v = null; });
		
		return $this->db->update("hr_$type", array_merge($data, array(
				'updated_date' => $currdate->format('Y-m-d H:i:s'),
				'updated_by' => $this->session->userdata('mb_no')
			)), array('id' => $id));
		
	}
	
	public function getPendingCount() {
		
		return $this->db->where('status', 0)->count_all_results('hr_cites');
	}
	public function getCurrentPreviousYearInfo() {
	
		$curr_date = new DateTime(null, new DateTimeZone('Asia/Manila'));
		
		$current = $this->db->from('hr_cites')->where(array('month(nte_date)' => $curr_date->format('n'), 'year(nte_date)' => $curr_date->format('Y'), 'status >' => 0))->count_all_results();

		$previous = intval($curr_date->format('n')) ? $this->db->from('hr_cites')->where(array('month(nte_date)' => intval($curr_date->format('n')) - 1, 'year(nte_date)' => $curr_date->format('Y'), 'status >' => 0))->count_all_results() : 0;

		$year = $this->db->from('hr_cites')->where(array('year(nte_date)' => $curr_date->format('Y'), 'status >' => 0))->count_all_results();
		
		return array($current, $previous, $year);
	}
	
	public function getMonthlyAverage() {

		return $this->db->query('select round(avg(c.a)) a from (select month(h.nte_date) as rmonth, avg(h.offense_id) a from hr_cites h where year(nte_date) = year(now()) group by rmonth) c')->row()->a;
	}
	
	public function getUserID($cite_id) {
		
		return @intval($this->db->get_where('hr_cites', array('id' => $cite_id))->row()->employee_id, 10);
	}
}