<?php

require_once (LIB_CORE .'plugin.class.php');

class test extends plugin {
	
	/**
	 *
	 */
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 *
	 */
	
	protected function show() {
		return array (
			'toto'	=> 12,
			'lala'	=> 'salut'
		);
	}
}