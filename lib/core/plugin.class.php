<?php

abstract class plugin {
	
	private $listMethod			= array ('show', 'about');
	protected $pluginName		= null;
	protected $actionMethod		= null;
	protected $conf				= null;
	
	/**
	 *
	 */
	
	public function __construct() {
		$this->pluginName = get_class($this);
		$this->conf = config::get();
	}
	
	/**
	 *
	 */
	
	public function run($actionMethod = 'show') {
		
		$this->actionMethod = $actionMethod;
		
		# check if the method exists, then execute it
		if (!method_exists($this, $this->actionMethod)) {
			$this->actionMethod = 'show';
		}
		
		$ret = call_user_func(array($this, $this->actionMethod));
		
		$ret['menuItems'] = $this->getMenuItems();
		return $ret;
	}
	
	/**
	 *
	 */
	
	protected function getListMethod() {
		return $this->listMethod;
	}
	
	/**
	 *
	 */
	
	private function about() {
		$file_about = LIB_MOD . $this->pluginName .'/about.yaml';
		if (file_exists($file_about)) {
			return Spyc::YAMLLoad($file_about);
		} else {
			return array (
				'title'			=> $this->pluginName,
				'description'	=> 'cannot find a description for this plugin'
			);
		}
	}
	
	/**
	 *
	 */
	
	private function getMenuItems() {
		
		foreach ($this->listMethod as $item) {
			if ($item == $this->actionMethod) {
				$class = 'current';
			} else {
				$class = '';
			}
			
			$ret[] = array (
				'url'	=> '?action='. $this->pluginName .'.'. $item,
				'name'	=> $item,
				'class'	=> $class
			);
		}
		return $ret;
	}
}