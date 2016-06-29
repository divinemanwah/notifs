<?php

require dirname(dirname(__FILE__)) . '/lib/Predis/Autoloader.php';

Predis\Autoloader::register();

class Notifs {
	
	public function __construct() {}
	
	public static function publish($channel, $data) {
	
		$message = json_encode($data);
		
		if($message) {
	
			$client = new Predis\Client(array(
					// 'host' => getenv('HTTP_HOST') == '10.120.10.139' ? '10.120.10.138' : '10.120.0.195'
					'host' => '10.120.10.138'
				));
			
			return $client->publish(WS::getTag() . '_' . $channel, $message);
		}
		else
			throw new Exception('Invalid data.');
		
	}
	
	public static function spublish($channel, $data) {
	
		$message = json_encode($data);
		
		if($message) {
	
			$client = new Predis\Client();
		
			$date = new DateTime(null, new DateTimeZone('Asia/Manila'));
			
			$c = WS::getTag() . "_$channel_" . $date->format('m-d');
			
			$client->rpush($c, $message);
			
			$date->add(new DateInterval('P7D'));
			
			$client->expireat($c, $date->format('U'));
			
			$client->publish(WS::getTag() . '_' . $channel, $message);
		}
		else
			throw new Exception('Invalid data.');
		
	}
	
	public static function _list($channel, $offset = 0, $days = 1) {
	
		$date = new DateTime(null, new DateTimeZone('Asia/Manila'));
		
		$c = WS::getTag() . "_$channel";
		
		$client = new Predis\Client();
		
		$keys = $client->keys($c . '_[0-9][0-9]-[0-9][0-9]');
		
		if(is_array($keys) && count($keys)) {
			
			natsort($keys);
			
			$keys = array_values($keys);

			$i = count($keys) - $offset;
			$ctr = 0;
			
			$res = array();

			while($i-- && isset($keys[$i]) && $ctr < $days) {

				$res[] = array(
						'list' => array_map('json_decode', $client->lrange($keys[$i], 0, -1)),
						'next' => isset($keys[$i - 1]) ? str_replace($c . '_', '', $keys[$i - 1]) : null,
						'total' => count($keys)
					);
			
				$ctr++;
			}
			
			return $res;
			
		}
	}
}