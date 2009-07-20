<?php

require_once (LIB_CORE .'plugin.class.php');

class stats extends plugin {
	
	private $user = null;
	
	/**
	 *
	 */
	
	public function __construct() {
		parent::__construct();
		
		$datas = bus::getData('user');
		$this->user = $datas['login'];
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
				return array(
					'name'			=> 'Statistics',
					'description'	=> 'stats of your folders',
					'stats'			=> $this->show(),
					'menuItems'		=> $this->getMenuItems()
				);
				break;
		}
	}
	
	/**
	 *
	 */
	
	private function show() {
		$directory = $this->conf['general']['dataPath'] . $this->user .'/';
		$space = $this->GetFolderSize($directory);
		
		return tools::getSymbolByQuantity($space);
	}
	
	/**
	 *
	 */
	
	private function GetFolderSize($d ="." ) {
		// © kasskooye and patricia benedetto
		$h = @opendir($d);
		$sf = 0;
		if ($h == 0) return 0;

		while ($f = readdir($h)){
			if ($f != "..") {
				$sf += filesize($nd = $d ."/". $f);
				if ($f != "." && is_dir($nd)){
					$sf += $this->GetFolderSize($nd);
				}
			}
		}
		closedir($h);
		return $sf ;
	}
}