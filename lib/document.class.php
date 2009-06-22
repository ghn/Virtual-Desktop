<?php

require_once ('file.class.php');

class document extends file {
	
	/**
	 *
	 */
	
	public function __construct($path) {
		parent::__construct($path);
	}
	
	/**
	 *
	 */
	
	public function run($action_method) {
		switch ($action_method) {
			case 'get':
				$this->getFile();
				break;
		}
	}
	
	/**
	 *
	 */
	
	public function build() {
	}
}