<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cite extends MY_Controller {

	function __construct() {
	
		parent::__construct();
		
		$this->load->model('employees_model', 'employees');
	}
	
	public function index($id = null) {
		
		if(!$id && intval($this->session->userdata('mb_deptno'), 10) != 24)
			redirect('/cite/' . $this->session->userdata('mb_no'), 'refresh');
		
		$this->view_template('cite_list', 'Cite Forms', array(
			'breadcrumbs' => is_numeric($id) && $id ? array('My Records') : array('Manage', 'Records'),
			'js' => array(
					'jquery.dataTables.min.js',
					'dataTables.bootstrap.min.js',
					'bootstrap-tag.min.js',
					'jquery.inputlimiter.1.3.1.min.js',
					'cite.list.js'
				),
			'_id' => is_numeric($id) && $id ? $id : null
		));
		
	}
	
	public function settings() {
		
		$this->view_template('cite_settings', 'Cite Forms', array(
			'breadcrumbs' => array('Manage', 'Settings'),
			'js' => array(
					'jquery.dataTables.min.js',
					'dataTables.bootstrap.min.js',
					'cite.settings.js'
				)
		));
		
	}
	
	public function get($type, $id) {
	
		$res = array();
		
		if(is_numeric($id) && in_array($type, array('offense', 'penalty')))
			$res = $this->cite->get($type, $id);
		
		print json_encode($res);
	}
	
	public function getAll($type = '', $show_disabled = false, $cite_filter = null) {
		
		$data = array();
	
		$res = array();
		
		if(in_array($type, array('offense', 'penalty', 'cite')))
			$res = $this->cite->getAll($type, $show_disabled, $cite_filter);
		
		foreach($res as $r)
			$data[] = array_values((array) $r);

		print json_encode(array('data' => $data));
		
	}
	
	public function getAllByID($id, $type = '', $show_disabled = false, $cite_filter = null) {
	
		$data = array();
	
		$res = array();
	
		if(is_numeric($id) && $id && in_array($type, array('offense', 'penalty', 'cite')))
			$res = $this->cite->getAllByID($id, $type, $show_disabled, $cite_filter);
	
		foreach($res as $r)
			$data[] = array_values((array) $r);
	
		print json_encode(array('data' => $data));
	
	}
	
	public function getCiteDetails($id) {
		
		print json_encode($this->cite->getCiteDetails($id));
	}
	
	public function add() {
		
		$post = $this->input->post(null, true);
		
		$success = false;
		
		if(in_array($post['type'], array('offense', 'penalty')))
			$success = $this->cite->add($post['type'], $post['desc']);
		
		print json_encode(array('success' => $success));
		
	}
	
	public function update() {
		
		$post = $this->input->post(null, true);
		
		$count = 0;
		
		$success = false;
		
		if(is_array($post['data']) && count($post['data']) && in_array($post['type'], array('offense', 'penalty', 'cite'))) {
		
			$success = $this->cite->update($post['type'], $post['id'], $post['data']);
			
			$count = $this->cite->getPendingCount();
			
			if($success && $post['type'] == 'cite' && $post['data']['status'] == '1') {
				
				$this->load->model('notifications_model', 'notifs');
				
				$_uid = $this->cite->getUserID($post['id']);
				
				$this->notifs->create('cite', 1, 1, $_uid, 0, "cite/$_uid", 'showNewCites', $_uid);
			}
		}
		else
			$success = false;
		
		print json_encode(array('success' => $success, 'count' => $count));
		
	}
	
	public function asd() {
		// $this->load->library('email');
		
		// $config['protocol'] = 'sendmail';
		// $config['mailpath'] = 'C:\\xampp\\sendmailsendmail.exe';

		// $this->email->initialize($config);

		// $this->email->from('rsantor@pacificseainvests.com', 'Ron');
		// $this->email->to('rsantor@pacificseainvests.com');

		// $this->email->subject('Email Test');
		// $this->email->message('Testing the email class.');

		// $this->email->send();

		// echo $this->email->print_debugger();
		
		if (function_exists('mb_internal_encoding') && ((int) ini_get('mbstring.func_overload')) & 2) {
		
			$mbEncoding = mb_internal_encoding();
			mb_internal_encoding('ASCII');
		}
		
		require_once APPPATH . '/third_party/lib/swift_required.php';
		
		// Create the Transport
		$transport = Swift_SmtpTransport::newInstance('mail.pacificseainvests.com', 25)
		  ->setUsername('rsantor@pacificseainvests.com')
		  ->setPassword('xxxx')
		  ;

		/*
		You could alternatively use a different transport such as Sendmail or Mail:

		// Sendmail
		$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');

		// Mail
		$transport = Swift_MailTransport::newInstance();
		*/

		// Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);

		// Create a message
		$message = Swift_Message::newInstance('Wonderful Subject')
		  ->setFrom(array('rsantor@pacificseainvests.com' => 'Ron'))
		  ->setTo(array('rsantor@pacificseainvests.com'))
		  ->setBody('<html><body>test</body></html>', 'text/html')
		  ;

		// Send the message
		$result = $mailer->send($message);
		
		if (isset($mbEncoding)) {
		
			mb_internal_encoding($mbEncoding);
		}
		
		var_dump($result);
	}
	
	public function aaa() {
		
		$this->load->library('tree');
		
		$c1 = $this->tree->node('child1');
		
		$tree = $this->tree->node('foo')
									->setValue('value')
									->addChild($c1)
									->addChild($this->tree->node('child2'))
									->getChildren();
		
		var_dump($tree[1]->getNeighbors());
	}
}