<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * System messages
 *
 * id => message
*/
$config['messages'] = array(
		'system' => array(
					1 => 'There are %d employee(s) waiting for confirmation',
                                        2 => 'There are %d expiring document(s)'
				),
		'application' => array(
					1 => 'Your %s application has been %s',
                                        2 => '%s Schedule has been %s'
				),
		'cite' => array(
					1 => 'You have %d new cite form(s)'
				)
	);

$config['icons'] = array(
		'system' => 'server',
		'application' => 'user',
		'cite' => 'file'
	);