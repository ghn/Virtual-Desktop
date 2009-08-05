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
	
	public function getJS() {
		$rep = LIB_MOD . $this->pluginName .'/js/';
		if (is_dir($rep)) {
			$res = opendir($rep);
			while (false !== ($file = readdir($res))) {
				if (is_file($rep . $file)) {
					$tab[] = $this->pluginName .'/js/'. $file;
				}
			}
			if (empty($tab)) {
				return null;
			} else {
				return $tab;
			}
		}
	}
	
	/**
	 *
	 */
	
	public function getCSS() {
		$rep = LIB_MOD . $this->pluginName .'/css/';
		if (is_dir($rep)) {
			$res = opendir($rep);
			while (false !== ($file = readdir($res))) {
				$ext = substr($file, strrpos($file, '.') + 1);
				if (is_file($rep . $file) && ($ext == 'css')) {
					$tab[] = $this->pluginName .'/css/'. $file;
				}
			}
			if (empty($tab)) {
				return null;
			} else {
				return $tab;
			}
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