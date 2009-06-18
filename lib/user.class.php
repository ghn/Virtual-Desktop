<?php

class user extends component {
	
	/*
	 * SESSION vars
	 *   auth_login
	 *   auth_date
	 */
	
	private $connected = false;
	
	
	public function __construct() {
		if (!empty($_SESSION['auth_login'])) {
			$this->connected = true;
		} else {
			$this->connected = false;
		}
	}
	
	
	private function login($login, $password) {
		$_SESSION['auth_login'] = $login;
		$this->connected = true;
		
		# redirect to homepage
		$conf = config::get();
		header('Location: '. $conf['general']['appURL']);
	}
	
	
	protected function logout() {
	}
	
	
	public function isConnected() {
		return $this->connected;
	}
	
	
	public function getUserName() {
		return $this->userName;
	}
	
	
	public function get() {
		return "module user";
	}
	
	
	public function run($action_method) {
		$this->login($_POST['vd_auth_login'],$_POST['vd_auth_password']);
	}
}