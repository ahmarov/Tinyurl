<?php defined('SYSPATH') or die('No direct script access.');

Route::set('t', 't(/<key>)', array('key' => '.+'))
	->defaults(array(
		'controller' => 'tinyurl',
		'action'     => 'index',
		'key'        => NULL
	));