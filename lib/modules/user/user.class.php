<?php

require_once (LIB_CORE .'tools.class.php');

class user {
	
	/*
	 * SESSION vars
	 *   auth_login
	 *   auth_date
	 */
	
	private $conf		= array();
	
	private $connected	= false;
	private $userName	= null;
	private $login		= null;
	private $password	= null;
	private $flickrName = null;
	
	
	/**
	 *	Try to log on automatically
	 */
	
	public function __construct() {
		
		$this->conf = config::get();
		
		if (!empty($_SESSION['auth_login'])) {
			$this->connected = true;
			$this->login = $_SESSION['auth_login'];
			$this->userName = $_SESSION['auth_userName'];
			$this->flickrName = $_SESSION['auth_flickrName'];
			
		} else if (isset($_POST['vd_auth_login']) && isset ($_POST['vd_auth_password'] ) ){
			$this->login = $_POST['vd_auth_login'];
			$this->password = sha1($_POST['vd_auth_password']);
		} else {
			$this->connected = false;
		}
	}
	
	/**
	 *
	 */
	
	private function login() {
		
		if (!$this->connected && !is_null($this->login) && !is_null($this->password)) {
		
			$accountsFile = Spyc::YAMLLoad(dirname(__FILE__) .'/../../../config/accounts.yaml');
			
			if (isset($accountsFile[$this->login])) {
				if ($this->password == $accountsFile[$this->login]['password'] && $accountsFile[$this->login]['enable'] == 'true') {
					$_SESSION['auth_login'] = $this->login;
					$_SESSION['auth_userName'] = $accountsFile[$this->login]['name'];
					$_SESSION['auth_flickrName'] = $accountsFile[$this->login]['flickrName'];
					
					$this->connected = true;
					$this->userName = $accountsFile[$this->login]['name'];
					$this->flickrName = $accountsFile[$this->login]['flickrName'];
					
					// reset error
					$this->setError();
				} else {
					$this->setError('Login or password not valid.');
				}
			} else {
				$this->setError('Login or password not valid.');
			}
			
			# redirect to homepage
			header('Location: '. $this->conf['general']['appURL']);
		}
	}
	
	/**
	 *
	 */
	
	private function logout() {
		unset ($_SESSION['auth_login']);
		$this->connected = false;
		
		$this->userName	= null;
		$this->login	= null;
		$this->password	= null;
		
		# redirect to homepage
		header('Location: '. $this->conf['general']['appURL']);
	}
	
	/**
	 *
	 */
	
	private function setError($msg = null) {
		if (is_null($msg)) {
			unset ($_SESSION['auth_error']);
		} else {
			$_SESSION['auth_error'] = $msg;
		}
	}
	
	/**
	 *
	 */
	
	public function getError() {
		if (isset($_SESSION['auth_error'])) {
			return $_SESSION['auth_error'];
		} else {
			return null;
		}
	}
	
	/**
	 *
	 */
	
	public function isConnected() {
		return $this->connected;
	}
	
	/**
	 *
	 */
	
	public function getUserName() {
		return $this->userName;
	}
	
	/**
	 *
	 */
	
	public function getLogin() {
		return $this->login;
	}
	
	/**
	 *
	 */
	
	public function getFlickrName() {
		return $this->flickrName;
	}
	
	/**
	 *	Method callable from URL
	 */
	
	public function run($action_method) {
		
		switch ($action_method) {
			case 'logout':
				$this->logout();
				break;
			case 'login':
				$this->login();
				break;
		}
	}
}