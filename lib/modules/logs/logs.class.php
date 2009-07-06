<?php

class logs {
	
	private $logFile = null;
	
	/**
	 *
	 */
	
	public function __construct() {
	}
	
	/**
	 *
	 */
	
	public function write() {
	}
	
	/**
	 *
	 */
	
	public function run($action_method = 'show') {
		return array (
			'name'			=> 'Log file',
			'description'	=> '',
			'logsList'		=> $this->show(),
			'menuItems'		=> $this->getMenuItems()
		);
	}
	
	/**
	 *
	 */
	
	private function show() {
		return array (
			0	=> array (
				'date'		=> '2009-10-20',
				'ip'		=> '10.1.22',
				'module'	=> 'mpdule',
				'message'	=> 'asdlfgjk asgdkjfh '
			)
		);
	}
	
	/**
	 *
	 */
	
	private function getMenuItems() {
		return array (
			0	=> array (
				'url'	=> '?action=logs.show',
				'name'	=> 'Log file',
				'class'	=> 'current'
			),
			1	=> array (
				'url'	=> '?action=logs.about',
				'name'	=> 'About',
				'class'	=> ''
			)
		);
	}
}