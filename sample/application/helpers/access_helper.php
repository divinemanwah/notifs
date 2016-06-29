<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * CodeIgniter Download Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/download_helper.html
 */
// ------------------------------------------------------------------------ 

if (!function_exists('in_array_r')) {

    function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
	
		return false;
	}

}  


if (!function_exists('allowed_access_report')) {

    function allowed_access_report($report="") {  
        $obj = & get_instance();  //get instance, access the CI superobject
        
		if(!array_key_exists($report, $obj->session->userdata("reports")) || $report="" )
		 {
			return false;  
		 }
		else
		 {
			return true;  
		 }
		
		
	}

}  


if (!function_exists('management_access')) {

    function management_access() {  
        $obj = & get_instance();  //get instance, access the CI superobject
        $list_id = array(114,229,126); 
		
		return in_array($obj->session->userdata("mb_no"),$list_id)?true:false;   
		
	}

}  

if (!function_exists('group_access')) {

    function group_access() {  
        $obj = & get_instance();  //get instance, access the CI superobject
        $list_id = array(114,229); 
		$obj->load->model('ion_auth_model', 'ion_m');
		
		$groups = $obj->ion_m->get_users_groups()->result();
		foreach($groups as $group) {
		  if(in_array($group->id,array(7,8,9,5,10))) {
		    return true;	
	  }
		}
		return false;
		
	}

}  


if (!function_exists('allowed_group_access')){
    function allowed_group_access(){
        $obj = & get_instance();  //get instance, access the CI superobject
        
        $return = false;
        $mb_deptno = $obj->session->userdata("mb_deptno");
        $mb_no = $obj->session->userdata("mb_no");
        $tblname = Array('dept_allowed_group','g4_member');
        
        
            $obj->load->model('department_model', 'dept_m');
            $grp_access = $obj->dept_m->setAllowedGroup($mb_no);
            if(!empty($grp_access[0]->allowed_status) && $grp_access[0]->allowed_status = 1)$return = true;
            /*
            if(!$return)
            $return =  ($obj->session->userdata("mb_deptno") == 24)?true:false;   
             * 
             */
        
        return $return ;
    }
}
/* End of file admin_access.php */
/* Location: ./application/helpers/download_helper.php */


