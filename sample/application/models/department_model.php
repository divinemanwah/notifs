<?php

class department_model extends CI_Model {
    
    protected $set_message;
    
    
    function __construct() {
        parent::__construct();
    }
    
    
    public function getdepartment($sel = ''){
        $dept = $this->db;
        
        if(!$sel)
            $dept = $dept->select('dept.id , dept.group_name,dept.dept_status ',false);
        else
            $dept = $dept->select($sel,false);
        
        $dept = $dept->from('dept_group dept');
        
        return $dept;
    }

    public function getmembers($sel = ''){
        $mem = $this->db;
        if(!$sel)
            $mem = $mem->select('gm.mb_no,gm.mb_nick,gm.mb_id,gm.mb_name,gm.mb_fname,gm.mb_lname,gm.mb_deptno,deptid.dept_name,gma.group_id ',false);
        else
            $mem = $mem->select($sel,false);
        $mem = $mem->from('g4_member gm')
                ->join('dept deptid','deptid.dept_no = gm.mb_deptno','left')
                ->join('dept_group_mem_assign gma','gma.mb_no = gm.mb_no','left')
                ->join('dept_group dept','dept.id = gma.group_id and dept.dept_status = 1','left');
        return $mem;
        
    }
    public function setAllowedGroup($mb_no){
        
        return $dept = $this->db->from('dept_allowed_group')->where(Array('mb_no'=>$mb_no))->get()->result();
        
    }
    public function update_data($tblname ,$where ,$data){
        $update = $this->db;
        foreach($where as $addwhere)$update->where($addwhere);
        return $update->update($tblname,$data);
    }
    
    public function set_record($tbl,$where){
        return $this->db->from($tbl)
                ->where($where);
    }
    
    
    public function create_data($tblname , $group_name = array()){
                
        	// bail if the group name was not passed
		if(empty($group_name))
		{
			$this->set_message = 'Data Required';
			return FALSE;
		}
		// bail if the group name already exists
                

		$data = $group_name;


		// insert the new group
		$this->db->insert($tblname, $data);
		$group_id = $this->db->insert_id();


		// return the brand new group id
		return $group_id;
        
    }
    
    public function data_exists($table,$where)
    {
        $get_info = $this->db
                         ->from($table);
        foreach($where as $additional_where) $get_info->where($additional_where);
        return ($get_info->row() > 0) ? 1 : 0;
                
    }
    
}