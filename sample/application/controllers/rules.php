<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rules extends MY_Controller {

	function __construct() {
	
		parent::__construct();
		
		$this->load->model('rules_model', 'rules');
	}
	
	public function get($id) {
	
		$res = array();
		
		if(is_numeric($id))
			$res = $this->rules->get($id);
		
		print json_encode($res);
	}
	
	public function getAll($type, $show_disabled = false) {
		
		$data = array();
	
		$res = $this->violations->getAll($type, $show_disabled);
		
		foreach($res as $r)
			$data[] = array_values((array) $r);

		print json_encode(array('data' => $data));
		
	}
	
	public function add() {
		
		print json_encode(array('success' => $this->rules->add($this->input->post(null, true))));
		
	}
	
	public function update() {
		
		print json_encode(array('success' => $this->rules->update($this->input->post(null, true))));
		
	}
	
	public function remove() {
	
		$id = $this->input->post('id', true);
		
		print json_encode(array('success' => is_numeric($id) && $this->rules->remove($id)));
		
	}
}