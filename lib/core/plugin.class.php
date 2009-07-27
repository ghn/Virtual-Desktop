<?php

abstract class plugin {
	
	private $listMethod			= array ('show', 'about');
	protected $pluginName		= null;
	protected $action_method	= null;
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
	
	protected function about() {
		$file_about = LIB_MOD . $this->pluginName .'/about.yaml';
		if (file_exists($file_about)) {
			return Spyc::YAMLLoad($file_about);
		} else {
			return array (
				'title'			=> $this->pluginName,
				'description'	=> 'cannot find a description for this plugin',
				'menuItems'		=> $this->getMenuItems()
			);
		}
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
	
	protected function getMenuItems() {
		
		foreach ($this->listMethod as $item) {
			
			if ($item == $this->action_method) {
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