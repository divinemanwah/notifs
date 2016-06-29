<?php

class Condo_model extends CI_Model {

	function __construct() {
        parent::__construct();
    }
	
	
    function Condo_List() {
        
        $query = "  Select
                        c.condo_id,
                        c.condo_name,
                        c.condo_address,
                        c.status,
                        c.is_active,
                        c.created_by,
                        c.updated_by,
                        c.deleted_by,
                        c.dt_created,
                        c.dt_updated,
                        c.dt_deleted
                    From condo_list c
                    Where c.is_active = 1
                    ";
        
        $list = $this->db->query($query)->result();
        //echo $this->db->last_query();
        return $list;
    }
    
    
    function Condo_Info($condo_id) {
        
        $query_info = " Select
                            c.condo_id,
                            c.condo_name,
                            c.condo_address,
                            c.status,
                            c.is_active,
                            c.created_by,
                            c.updated_by,
                            c.deleted_by,
                            c.dt_created,
                            c.dt_updated,
                            c.dt_deleted
                        From condo_list c
                        
                        Where   c.is_active = 1
                            And c.condo_id = ".$condo_id;
        
        $query_expat = "Select
                            mb.mb_no,
                            mb.mb_name,
                            mb.mb_fname,
                            mb.mb_mname,
                            mb.mb_lname,
                            mb.mb_nick,
                            mb.condo_id,
                            mb.mb_2 'dept'
                            
                        From g4_member mb
                        
                        Where   mb.mb_status = 1
                            And mb.condo_id = ". $condo_id ."
                        Order by mb.mb_nick, mb.mb_name";
        
        $result_info    = $this->db->query($query_info)->result();
        $result_expat   = $this->db->query($query_expat)->result();
        
        return array($result_info, $result_expat);
        
    }
    
    function getAllExpats()
    {
        $query = "  Select
                        mb.mb_no,
                        mb.mb_name,
                        mb.mb_fname,
                        mb.mb_mname,
                        mb.mb_lname,
                        mb.mb_nick,
                        mb.condo_id,
                        mb.mb_2 'dept'

                    From g4_member mb

                    Where   mb.mb_status = 1
                        And UPPER(mb.mb_3) = 'EXPAT'
                    Order by mb.mb_nick, mb.mb_name";
        
        $list = $this->db->query($query)->result();
        //echo $this->db->last_query();
        return $list;
        
        
    }
    
    
    function Condo_Exist($condo_id, $condo_name) {
        $query = "  Select 
                        Count(*) 'cnt'
                    From condo_list c
                    Where c.is_active = 1
                        And c.condo_id <> " . $condo_id . "
                        And c.condo_name = '" . addslashes($condo_name) . "'
                ";

        $result = $this->db->query($query)->result();

        If (Count($result) > 0) {
            ForEach ($result as $row) {
                $iExist = $row->cnt;
            }
            
            return $iExist;
        }
    }
    
    
    
    function Save_Condo($save_data) {
        $time       = date("Y-m-d H:i:s");
        $user_ID    = $this->session->userdata('mb_no');
        
        $condo_id       = $save_data[0];
        $condo_name     = addslashes($save_data[1]);
        $condo_address  = addslashes($save_data[2]);
        $status         = $save_data[3];
        $arr_expats     = $save_data[4];
        
        if ($condo_id == 0) {
            $saveData = array(
                'condo_name'    => $condo_name,
                'condo_address' => $condo_address,
                'status'        => $status,
                'is_active'     => 1,
                'created_by'    => $user_ID,
                'dt_created'    => $time
            );
            
            $this->db->insert('condo_list', $saveData);
            $condo_id = $this->db->insert_id();
            
            $action = "Add";
        } else {
            $updateData = array(
                'condo_name'    => $condo_name,
                'condo_address' => $condo_address,
                'status'        => $status,
                'updated_by'    => $user_ID,
                'dt_updated'    => $time
            );
            
            $this->db->where('condo_id', $condo_id);
            $this->db->update('condo_list', $updateData);
            
            $action = "Update";
        }
        
        $iRecordSave = $this->db->affected_rows();
        
        
        //save condo_id in g4_member
        $updateList = array(
                'condo_id' => 0
            );
        $this->db->where('condo_id', $condo_id);
        $this->db->update('g4_member', $updateList);
        
        For ($i = 0; Count($arr_expats) > $i; ++$i)
        {
           $mb_no = $arr_expats[$i];

            $updateExpats = array(
                    'condo_id' => $condo_id
                );
            
            $this->db->where('mb_no', $mb_no);
            $this->db->update('g4_member', $updateExpats);
        }
        
        return $iRecordSave;
    }
    
    
    function Delete_Condo($condo_id) {
        
        $time       = date("Y-m-d H:i:s");
        $user_ID    = $this->session->userdata('mb_no');
        
        $deleteData = array(
            'is_active' => 0,
            'deleted_by' => $user_ID,
            'dt_deleted' => $time
        );
        
        $this->db->where('condo_id in ('.$condo_id.')');
        $this->db->update('condo_list', $deleteData);
        
        $iRecordDelete = $this->db->affected_rows();
        
        
        //delete condo_id in g4_member
        $updateList = array(
                'condo_id' => 0
            );
        $this->db->where('condo_id', $condo_id);
        $this->db->update('g4_member', $updateList);
        
        return $iRecordDelete;
    }
}