<?php

class user {
	
	/*
	 * SESSION vars
	 *   auth_login
	 *   auth_date
	 */
	
	private $connected = false;
	private $userName = '';
	private $login = '';
	protected $conf = array();
	
	
	/**
	 *
	 */
	
	public function __construct() {
		
		$this->conf = config::get();
		
		if (!empty($_SESSION['auth_login'])) {
			$this->connected = true;
			$this->login = $_SESSION['auth_login'];
			$this->userName = $_SESSION['auth_userName'];
		} else {
			$this->connected = false;
		}
	}
	
	/**
	 *
	 */
	
	private function login() {
		if (isset ($_POST['vd_auth_login']) && isset ($_POST['vd_auth_password'])) {
			$login = $_POST['vd_auth_login'];
			$password = $_POST['vd_auth_password'];
			
			$accountsFile = Spyc::YAMLLoad(dirname(__FILE__) .'/../config/accounts.yaml');
			
			if (isset($accountsFile[$login])) {
				if ($login == $accountsFile[$login]['login'] && sha1($password) == $accountsFile[$login]['password'] && $accountsFile[$login]['enable'] == 'true') {
					$_SESSION['auth_login'] = $login;
					$_SESSION['auth_userName'] = $accountsFile[$login]['name'];
					
					$this->connected = true;
					$this->userName = $accountsFile[$login]['name'];
					$this->login = $login;
				}
			}
		}
		
		# redirect to homepage
		header('Location: '. $this->conf['general']['appURL']);
	}
	
	/**
	 *
	 */
	
	private function logout() {
		unset ($_SESSION['auth_login']);
		$this->connected = false;
		
		# redirect to homepage
		header('Location: '. $this->conf['general']['appURL']);
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
	
	public function get() {
		return "module user";
	}
	
	/**
	 *
	 */
	
	public function run($action_method) {
		
		switch ($action_method) {
			case 'logout':
				$this->logout();
				break;
			case 'login':
				$this->login();
		}
	}
}