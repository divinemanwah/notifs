<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Guide extends MY_Controller {

	function __construct() {
		parent::__construct();
	}
	
	/* Views */
	
	public function index() {
		$this->load->view('guide/user_guide');
	}
}
?>