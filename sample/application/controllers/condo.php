<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Condo extends MY_Controller {

	function __construct() {
	
		parent::__construct();
		
		$this->load->model('condo_model', 'condo');
		$this->load->model('employees_model', 'emp');
	}
	
	public function index() {
                
            $record_List = array("record_List" => $this->condo->Condo_List());

            $this->view_template('condo/condo_main', 'Others', array(
                    'breadcrumbs' => array('Condominium'),
                    'js' => array(
                                'jquery.dataTables.min.js',
                                'dataTables.bootstrap.min.js',
                                'jquery.gritter.min.js',
                                'condo.list.js',
                                'bootbox.min.js'
                            ),
                            'recordList' => $this->load->view('condo/condo_list', $record_List, true),
                            "modalContent"=> $this->load->view('condo/condo_modal', '', true)
            ));
		
	}
        
        function getAllExpats()
	{
		$Obj = $this->condo;
		
		$Expat_result	= array('getAllExpats'	=> $Obj->getAllExpats());
		
		echo json_encode(array(	'expatData' => $Expat_result));
	}
        
        
        function Build_Condo_List() {
            
            $record_List = array("record_List" => $this->condo->Condo_List());

            $condo_list_HTML = $this->load->view('condo/condo_list', $record_List, true);

            echo json_encode(array('condo_list_HTML' => $condo_list_HTML));
        }
        
        
        function Condo_Info() {
            
            $obj = $this->condo;
            
            $condo_id   = (int) $this->input->post('condo_id');
            $action     = (int) $this->input->post('action');
            
            $record_data    = array("Condo_result" => $obj->Condo_Info($condo_id), "Expat_result" => $obj->getAllExpats());
            
            If ($action == 0) {
                $condo_info_HTML = $this->load->view('condo/condo_new', $record_data, true);
            } Else {
                
                If ($action == 1) {
                    $condo_info_HTML = $this->load->view('condo/condo_view', $record_data, true);
                } Else {
                    $condo_info_HTML = $this->load->view('condo/condo_edit', $record_data, true);
                }
            }

            echo json_encode(array('condo_info_HTML' => $condo_info_HTML));
        }
        
        
        function Condo_Exist()
        {
            $obj = $this->condo;
            
            $condo_id   = $this->input->post('condo_id');
            $condo_name = $this->input->post('condo_name');
            
            $iExist = $obj->Condo_Exist($condo_id, $condo_name);
            
            echo json_encode(array('iExist' => $iExist));
        }
        
        
	function Save_Condo() {
            
            $obj = $this->condo;
            
            $save_data = $this->input->post('save_data');
            
            $iRecordSave = $obj->Save_Condo($save_data);
            
            echo json_encode(array('iRecordSave' => $iRecordSave));
        }
	
        
        function Delete_Condo() {
        
            $obj = $this->condo;

            $condo_id = $this->input->post('condo_id');

            $iRecordDelete = $obj->Delete_Condo($condo_id);

            echo json_encode(array('iRecordDelete' => $iRecordDelete));
        }
        
}