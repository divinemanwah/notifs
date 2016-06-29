<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Violations extends MY_Controller {

	function __construct() {
	
		parent::__construct();
		
		$this->load->model('violations_model', 'violations');
	}
	
	public function index() {
		
		$this->view_template('violations_list', 'Violations', array(
			'breadcrumbs' => array('Manage', 'Records'),
			'js' => array(
					'jquery.dataTables.min.js',
					'dataTables.bootstrap.min.js',
					'ajaxq.js',
					'violations.list.js'
				)
		));
		
	}
	
	public function settings() {
		
		$this->view_template('violations_settings', 'Violations', array(
			'breadcrumbs' => array('Manage', 'Settings'),
			'js' => array(
					'jquery.dataTables.min.js',
					'dataTables.bootstrap.min.js',
					'violations.settings.js'
				)
		));
		
	}
	
	public function get($id) {
	
		$res = array();
		
		if(is_numeric($id))
			$res = $this->violations->get($id);
		
		print json_encode($res);
	}
	
	public function getAll($type, $show_disabled = false, $user_id = 0, $year_month = null) {
		
		$data = array();
	
		$res = $this->violations->getAll($type, $show_disabled, $user_id, $year_month);
		
		foreach($res as $i => $r) {
		
			$data[$i] = array_values((array) $r);
			
			if($type == 1 && $data[$i][6])
				$data[$i][6] = json_decode($data[$i][6]);
		}

		print json_encode(array('data' => $data));
		
	}
	
	public function add() {
		
		print json_encode(array('success' => $this->violations->add(1, $this->input->post('desc', true), $this->input->post('details', true))));
		
	}
	
	public function update($type) {
		
		$post = $this->input->post(null, true);
		
		$success = false;
		
		if(is_array($post['data']) && count($post['data']))
			$success = $this->violations->update($type, $post['id'], $post['data']);
		else
			$success = false;
		
		print json_encode(array('success' => $success));
		
	}
	
	public function getCategory($id) {
	
		$res = array();
		
		if(is_numeric($id))
			$res = $this->violations->getCategory($id);
		
		print json_encode($res);
	}
	
	public function getCategories($show_disabled = false) {
	
		$data = array();
	
		$res = $this->violations->getCategories($show_disabled);
		
		foreach($res as $r)
			$data[] = array_values((array) $r);

		echo json_encode(array('data' => $data));
	}
	
	public function addCategory() {
	
		$parent_id = $this->input->post('parent_id', true);
		
		if($parent_id === false)
			$parent_id = 0;
		
		echo json_encode(array(
				'success' => 	($title = $this->input->post('title', true)) &&
								$this->violations->addCategory($title, $parent_id)
			));
	}
	
	public function updateCategory() {
	
		$id = $this->input->post('id', true);
		$title = $this->input->post('title', true);
		$parent_id = $this->input->post('parent_id', true);
		$active = $this->input->post('active', true);

		echo json_encode(array(
				'success' => 	$id &&
								$this->violations->updateCategory($id, $title, $parent_id, $active)
			));
	}
	
	private function array_remove_empty($haystack)
	{
		foreach ($haystack as $key => $value) {
			if (is_array($value)) {
				$haystack[$key] = $this->array_remove_empty($haystack[$key]);
			}
	
			if (empty($haystack[$key])) {
				unset($haystack[$key]);
			}
		}
	
		return $haystack;
	}
	
	private function validateDate($date)
	{
		$d = DateTime::createFromFormat('m-d-y', $date);
		return $d && $d->format('m-d-y') == $date;
	}
	
	public function importRecords() {
		
		$this->load->library('excel');
		
		$file = $_FILES['file'];
		
		$post = $this->input->post(null, true);
		
		$valid = false;
		$types = array('Excel2007', 'Excel5');
		
		$count = 0;
		
		foreach($types as $type) {
			
			$reader = PHPExcel_IOFactory::createReader($type);
			
			if($reader->canRead($file['tmp_name'])) {
				
				$valid = true;
				
				break;
			}
		}
		
		if($valid) {
			
			$objPHPExcel = PHPExcel_IOFactory::load($file['tmp_name']);

			$sheets = $objPHPExcel->getAllSheets();
			
			//$objPHPExcel->disconnectWorksheets();
			
// 			$rows = $this->array_remove_empty($sheets->toArray());

			$data = array();
			
			foreach($sheets as $i => $sheet) {
				
				$rows = $sheet->toArray();
					
				// array_shift($rows);
				
// 				$count += count($rows);
					
				// $this->load->model('employees_model', 'employees');
					
				$currdate = new DateTime(null, new DateTimeZone('Asia/Manila'));
					
				foreach($rows as $rownum => $row) {
				
					//  	$status = 3;
				
					//  	if(strpos(strtolower($row[9]), 'closed') !== false)
						//  		$status = 3;
						//  	elseif(strpos(strtolower($row[9]), 'cancelled') !== false)
						//  		$status = 4;
						//  	elseif(strpos(strtolower($row[9]), 'pending') !== false)
						//  		$status = 0;
						
						//  	$date = DateTime::createFromFormat('j-M-Y H:i:s', "{$row[5]}-2015 00:00:00");
						// 		$date = DateTime::createFromFormat('n/j/y H:i', $row[7]);
				
					$e = trim($row[0]);
				
					if(preg_match('/[EL][0-9]{2}-[0-9]{3}/', $e) == 1 && is_numeric($row[8])) {
// 						$sheet->getCommentByColumnAndRow(6, $rownum)->getText()
// 						$data[$i][$e] = array(
// 								intval($row[1], 10),
// 								$row[4]
// 							);

						$doc = DateTime::createFromFormat('j-M-y H:i', "{$row[5]}-15 00:00");
                        
                        $_data = array('doc' => $doc->format('m-d-Y H:i'), 'eid' => intval($row[1], 10), 'vid' => intval($row[2], 10), 'rem' => $row[4], 'initial' => intval($row[8], 10) * -1);
						
						if($this->violations->add(2, $_data))
							$count++;
                        else
                            $data[] = $_data;
					}
				
					//  	$data[] = array(
					//  			'id' => null,
					//  			'employee_id' => $row[4],
					//  			'violation_id' => $row[5],
					//  			'commission_date' => $date->format('Y-m-d H:i:s'),
					//  			'remarks' => (isset($row[10]) ? $row[10] . ' - ' : '') . $row[8],
					//  			'status' => 1,
					//  			'created_date' => $currdate->format('Y-m-d H:i:s'),
					//  			'created_by' => $this->session->userdata('mb_no'),
					//  			'temp_code' => trim($row[9])
					//  		);
				}
                break;
			}
			
//			sleep(10);
			
//  			if(count($data))
//  				$this->db->insert_batch('hr_member_violations', $data);
			
		}
		
		echo json_encode(array('success' => $valid, 'total' => $count, 'records' => $data));
	}
}