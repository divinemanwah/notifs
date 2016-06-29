<?php

class WSException extends Exception {

	const MODULE_NOT_FOUND =	1;
	const INVALID_ARGUMENT =	2;
	
	public function __construct($c = 0, $obj = null, Exception $e = null) {
	
		$m = '';
	
		switch($c) {
			case self::MODULE_NOT_FOUND:
			
				$m = 'Module [' . strtoupper($obj) . '] not found';
				
				break;
			case self::INVALID_ARGUMENT:
			
				$m = 'Invalid argument. Only types string and array are allowed, ' . gettype($obj) . ' was supplied';
				
				break;
			default:
			
				$m = 'Unknown exception';
		}
		
		parent::__construct($m, $c, $e);
	}
	
	public function __toString() {
		
		return __CLASS__ . " [{$this->code}]: {$this->message}";
	}
}