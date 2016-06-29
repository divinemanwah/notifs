<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include 'lib/loader.php';

class WS {

	private static $tag;
	private $loadedModules = array();
	
	public function __construct($params) {

		if(!isset($params['tag']))
			throw new WSException(WSException::MISSING_TAG);

		self::$tag = $params['tag'];
	}
	
	public static function getTag() {
		
		return self::$tag;
	}
	
	public function load($arg) {
		
		if(is_string($arg)) {
			
			if(!in_array($arg, $this->loadedModules)) {
		
				$m = dirname(__FILE__) . "/modules/$arg.php";
	
				if(file_exists($m)) {
	
					include_once $m;
					
					$this->loadedModules[] = $arg;
				}
				else
					throw new WSException(WSException::MODULE_NOT_FOUND, $arg);
			}
		}
		elseif(is_array($arg)) {
			
			foreach($arg as $_m) {
				
				$m = dirname(__FILE__) . "/modules/$_m.php";
			
				if(file_exists($m) && !in_array($_m, $this->loadedModules)) {
				
					include_once $m;
						
					$this->loadedModules[] = $_m;
				}
				else
					throw new WSException(WSException::MODULE_NOT_FOUND, $arg);
				
			}
			
		}
		else
			throw new WSException(WSException::INVALID_ARGUMENT, $arg);
		
	}
	
	public function getLoadedModules() {
		
		return $this->loadedModules;
	}
}