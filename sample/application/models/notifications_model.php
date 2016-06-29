<?php

class Notifications_model extends CI_Model {

	private $messages;

	function __construct() {
        parent::__construct();
		
		$this->load->library('ws', array('tag' => getenv('HTTP_HOST') == '10.120.10.139' ? 'hris' : 'hristest'));
		
		$this->load->model('employees_model', 'employees');

		$this->config->load('notifs', true);
		
		$this->messages = $this->config->item('messages', 'notifs');
    }
	
	// public function getAll($user_id = 0, $page = 1, $per_page = 8, $unread_only = false) {
	public function getAll($user_id = 0) {
	
		$user_id = intval($user_id ? $user_id : $this->session->userdata('mb_no'));
		
		$this->db->start_cache();
		
		$this->db->_protect_identifiers = false;
		
		$this->db
				->join('hr_notifications_read r', 'r.employee_id = ' . $user_id . ' and n.id = r.notifications_id', 'left');
		
		$this->db->_protect_identifiers = true;
		
		$this->db
				->from('hr_notifications n')
				->where_in('n.recipient', array(0, $user_id))
				->where_in('n.recipient_group', array(0, intval($this->session->userdata('mb_deptno'))))
				->group_by('n.id')
				->order_by('n.created_date', 'desc')
				->limit(100);
		
		$raw = $this->db->get()->result();
		
		$unread = 0;
		
		array_map(function ($a) use (&$unread, $user_id) {
				
			if(!$a->read_date && $a->employee_id != $user_id)
				$unread++;
			
			return $a;
			
		}, $raw);

		$this->db->stop_cache();
		
		$count = count($raw);
		
		// $this->db->limit($per_page, $page ? ($page - 1) * $per_page : 0);
		
		$filtered = $this->db->get()->result();
		
		$this->db->flush_cache();

		return array(
				'count' => $count,
				'data' => array_map(function ($a) {
				
						$keys = array_keys($this->messages);

						$a->extra_data = json_decode($a->extra_data);
						$a->message = call_user_func_array('sprintf', array_merge(array($this->messages[$keys[$a->type]][$a->message_id]), $a->extra_data));
						$a->type = $keys[$a->type];
						$a->triggered_by = $this->employees->get3($a->triggered_by);
						$a->args = json_decode($a->args);
						
						return $a;
						
					}, $filtered),
				'unread' => $unread
			);
	}
	
	public function create($type, $message_id, $extra_data = null, $recipient = 0, $recipient_group = 0, $page = null, $action = null, $args = null) {
		
		if(isset($this->messages[$type][$message_id])) {
		
			$extra_data = is_null($extra_data) ? null : (is_array($extra_data) ? $extra_data : array($extra_data));
		
			$triggered_by = in_array($type, array('system')) ? 0 : intval($this->session->userdata('mb_no'));
			
			$args = is_null($args) ? null : json_encode($args);

			$data = array(
					'type' => array_search($type, array_keys($this->messages)),
					'message_id' => $message_id,
					'extra_data' => json_encode($extra_data),
					'recipient' => $recipient,
					'recipient_group' => $recipient_group,
					'triggered_by' => $triggered_by,
					'created_date' => time(),
					'page' => $page,
					'action' => $action,
					'args' => $args
				);
			
			if($this->db->insert('hr_notifications', $data)) {
			
				$this->ws->load('notifs');
			
				NOTIFS::publish("{$recipient_group}_$recipient", array(
					'id' => $this->db->insert_id(),
					'type' => $type,
					'message' => call_user_func_array('sprintf', array_merge(array($this->messages[$type][$message_id]), $extra_data)),
					'triggered_by' => $this->employees->get3($triggered_by),
					'created_date' => $data['created_date'],
					'page' => $page,
					'action' => $action,
					'args' => $args
				));
				
				return true;
			}
			else
				return false;
		}
		else
			throw new InvalidArgumentException("No such type '$type' with message_id '{$message_id}'");
	}
	
	public function read($id) {
		
		if(is_array($id)) {
			
			$ins = array();
			
			$mb_no = intval($this->session->userdata('mb_no'));
			
			$time = time();
			
			foreach($id as $_id)
				$ins[] = array(
						'notifications_id' => intval($_id),
						'employee_id' => $mb_no,
						'read_date' => $time
					);
			
			$this->db->insert_batch('hr_notifications_read', $ins);
		}
		else
			$this->db
					->insert('hr_notifications_read', array(
						'notifications_id' => intval($id),
						'employee_id' => intval($this->session->userdata('mb_no')),
						'read_date' => time()
					));
		
	}
	
	public function getUnreadIDs() {
		
		return array_map(
				function ($a) {
						
						return $a->id;
						
					},
				$this->db
						->select('n.id')
						->join('hr_notifications_read r', 'r.notifications_id = n.id', 'left')
						->from('hr_notifications n')
						->where('r.read_date is null', null, false)
						->where_in('n.recipient', array(0, intval($this->session->userdata('mb_no'))))
						->where_in('n.recipient_group', array(0, intval($this->session->userdata('mb_deptno'))))
						->get()
						->result()
			);
	}
}