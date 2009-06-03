<?php

class CAuth extends CDrive {
	
	private $currentPage = 'index.php';
	public $user = "";		// instance of CAccounts class
	
	/**
	 *
	 */
	public function __construct($parameters = null) {
		@session_start();
				
		if (!is_null($parameters)) {
			$this->connect($parameters);
		}
		if (!$this->isconnected()) {
			$_SESSION['VD_AUTH_CURRENT_PAGE'] = $this->currentPage;
			header('Location: auth.php');
		} else {
			if (!is_null($parameters)) { 
				header('Location: '. $_SESSION['VD_AUTH_CURRENT_PAGE']);
			}
		}
	}
	
	/**
	 *
	 */
	public function isconnected() {
		if (isset($_SESSION['VD_AUTH_LOGIN'])) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 *
	 */
	public function getLogin() {
		return $_SESSION['VD_AUTH_LOGIN'];
	}
	
	/**
	 *
	 */
	public function logout() {
		
		$current_page = $_SESSION['VD_AUTH_CURRENT_PAGE'];
		$_SESSION = array();
		session_destroy();
		
		header('Location: '. $current_page);
	}
	
	/**
	 *
	 */
	private function connect($p = array()) {
		$login = $p['login'];
		$password = $p['password'];
		
		require_once('CAuthAccounts.php');
		
		$this->user = new CAuthAccounts($login, $password);
		
		if ($this->user->isAutorized) {
			$_SESSION['VD_AUTH_LOGIN'] = $p['login'];
		}
	}
}