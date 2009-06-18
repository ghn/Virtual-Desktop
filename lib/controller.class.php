<?php

require_once ('user.class.php');

class controller {
	
	protected $action;
	protected $action_method;
	
	protected $path;
	protected $user;
	
	/**
	 *
	 */
	public function __construct () {
		
		$this->getParams();
		
		# user is logged?
		$user = new user();
		if (!$user->isConnected()) {
			
			//$this->action = 'user';
			//$this->action_method = '';
		}
			
			# execute action
			if ($this->action == 'default') {
				$include = 'drive';
			} else {
				$include = $this->action;
			}
		
		require_once ($include .'.class.php');
		$component = new $include ($this->path);
		
		# execute component, then render it
		$component->run($this->action_method);
		$component->build();
	}
	
	
	/**
	 *	GET URL PARAMETERS
	 */
	
	private function getParams() {
		# get action
		if (isset($_GET['action']) && !empty($_GET['action'])) {
			list($this->action, $this->action_method) = explode('.', $_GET['action']);
		} else {
			$this->action = 'default';
		}
		
		# get path
		if (isset($_GET['path']) && !empty($_GET['path'])) {
			$this->path = $_GET['path'];
		} else {
			$this->path = '';
		}
		
		/*
		# Merge action and class file
		switch ($this->action) {
			case 'log':
				$this->action = 'user';
		}*/
	}
}