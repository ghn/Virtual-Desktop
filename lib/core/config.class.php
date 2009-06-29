<?php

require_once (LIB_CORE .'spyc.class.php');

class config {
	
	private static $config = array();
	
	/**
	 *
	 */
	public function load() {
		self::$config = Spyc::YAMLLoad(dirname(__FILE__) .'/../../config/config.yaml');
	}
	
	/**
	 *	
	 */
	public function get() {
		return self::$config;
	}
}