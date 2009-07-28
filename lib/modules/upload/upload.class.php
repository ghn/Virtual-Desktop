<?php

require_once (LIB_CORE .'plugin.class.php');

class upload extends plugin {
	
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
			'path'	=> 'test'
		);
	}
	
	/**
	 *
	 */
	
	private function upload() {
	
	}
}