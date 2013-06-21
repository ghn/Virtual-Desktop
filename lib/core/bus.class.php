<?php

class bus {
	
	static $dataBus = array();
	
	/**
	 *
	 */
	
	public function setData($key, $data) {
		self::$dataBus[$key] = $data;
	}
	
	/**
	 *
	 */
	
	public function getData($key) {
		if (array_key_exists($key, self::$dataBus)) {
			return self::$dataBus[$key];
		} else {
			return null;
		}
	}
}