<?php

require_once(LIB_CORE .'plugin.class.php');

class logs extends plugin {
	
	private $logFile = null;
	
	/**
	 *
	 */
	
	public function __construct() {
	}
	
	/**
	 *
	 */
	
	public function run($actionMethod = 'show') {
		$this->actionMethod = $actionMethod;
		
		switch ($actionMethod) {
			case 'about':
				$about = $this->about();
				
				return array (
					'name'			=> $about['title'],
					'description'	=> $about['description'],
					'menuItems'		=> $this->getMenuItems()
				);
				break;
				
			case 'show':
			default:
				return array (
					'name'			=> 'Log file',
					'description'	=> '',
					'logsList'		=> $this->show(),
					'menuItems'		=> $this->getMenuItems()
				);
				break;
		}
	}
	
	
	/**
	 *
	 */
	
	protected function write() {
	}
	
	/**
	 *
	 */
	
	protected function show() {
		return array (
			0	=> array (
				'date'		=> '2009-10-20',
				'ip'		=> '10.1.22',
				'module'	=> 'mpdule',
				'message'	=> 'asdlfgjk asgdkjfh '
			),
			1	=> array (
				'date'		=> '2009-10-20',
				'ip'		=> '10.1.22',
				'module'	=> 'mpdule',
				'message'	=> 'asdlfgjk asgdkjfh '
			),
			2	=> array (
				'date'		=> '2009-10-20',
				'ip'		=> '10.1.22',
				'module'	=> 'mpdule',
				'message'	=> 'asdlfgjk asgdkjfh '
			),
			3	=> array (
				'date'		=> '2009-10-20',
				'ip'		=> '10.1.22',
				'module'	=> 'mpdule',
				'message'	=> 'asdlfgjk asgdkjfh '
			)
		);
	}
	
	
}