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
	
	public function run($action_method = 'show') {
		
		$this->action_method = $action_method;
		
		switch ($action_method) {
			case 'about':
				$about = $this->about();
				
				return array (
					'name'			=> $about['title'],
					'description'	=> $about['description'],
					'menuItems'		=> $this->getMenuItems()
				);
				break;
				
			case 'show':
				return array (
					'toto'	=> 12,
					'lala'	=> 'salut',
					'menuItems'		=> $this->getMenuItems()
				);
				break;
		}
	}
}