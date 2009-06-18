<?php

class user extends component {
	
	/*
	 * SESSION vars
	 *   auth_login
	 *   auth_date
	 */
	
	private $connected = false;
	private $userName = '';
	private $login = '';
	
	public function __construct() {
		if (!empty($_SESSION['auth_login'])) {
			$this->connected = true;
			$this->login = $_SESSION['auth_login'];
			$this->userName = $_SESSION['auth_userName'];
		} else {
			$this->connected = false;
		}
		
		# try to connect
		$this->login();
	}
	
	
	private function login() {
		if (isset ($_POST['vd_auth_login']) && isset ($_POST['vd_auth_password'])) {
			$login = $_POST['vd_auth_login'];
			$password = $_POST['vd_auth_password'];
			
			if ($login == 'test' && $password == 'test') {
				$_SESSION['auth_login'] = $login;
				$_SESSION['auth_userName'] = 'test demo';
				$this->connected = true;
				$this->userName = 'test demo';
				$this->login = $login;
			}
		}
		
		# redirect to homepage
		//header('Location: '. $this->conf['general']['appURL']);
	}
	
	
	private function logout() {
		unset ($_SESSION['auth_login']);
		$this->connected = false;
		
		header('Location: '. $this->conf['general']['appURL']);
	}
	
	
	public function isConnected() {
		return $this->connected;
	}
	
	
	public function getUserName() {
		return $this->userName;
	}
	
	
	public function getLogin() {
		return $this->login;
	}
	
	
	public function get() {
		return "module user";
	}
	
	
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