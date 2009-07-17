<?php

abstract class plugin {
	
	private $listMethod	= array ('show', 'about');
	private $pluginName = 'stats';
	private $actionMethod = null;
	
	/**
	 *
	 */
	
	public function __construct($pluginName, $actionMethod) {
	}
	
	/**
	 *
	 */
	
	protected function about() {
		$file = LIB_MOD . $this->pluginName .'/about.yaml';
		$about = Spyc::YAMLLoad($file);
		return $about;
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