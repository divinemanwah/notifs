<?php

class MY_Input extends CI_Input {

    function __construct()
    {
        parent::__construct();
    }

    function ip_address() 
    {
        return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
    }
}

?>