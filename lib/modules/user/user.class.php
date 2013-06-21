<?php

require_once (LIB_CORE .'plugin.class.php');
require_once (LIB_CORE .'tools.class.php');

class user extends plugin {
	
	/*
	 * SESSION vars
	 *   auth_login
	 *   auth_date
	 */
	
	private $connected	= false;
	private $userName	= null;
	private $login		= null;
	private $password	= null;
	private $flickrName = null;
	
	/**
	 *	Try to log on automatically
	 */
	
	public function __construct() {
		parent::__construct();
		
		if (!empty($_SESSION['auth_login'])) {
			$this->connected = true;
			$this->login = $_SESSION['auth_login'];
			$this->userName = $_SESSION['auth_userName'];
			$this->flickrName = $_SESSION['auth_flickrName'];
			
			$this->updateBus();
			
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
	
	protected function login() {
		
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
					$message = $this->userName .' connected';
					
					$this->updateBus();
				} else {
					$this->setError('Login or password not valid.');
					$message = $this->login .' connexion failed';
				}
			} else {
				$this->setError('Login or password not valid.');
				$message = $this->login .' connexion failed';
			}
			
			# redirect to homepage
			header('Location: '. $this->conf['general']['appURL']);
			
			return $message;
		}
	}
	
	/**
	 *
	 */
	
	protected function logout() {
	
		$login = $this->login;
		
		unset ($_SESSION['auth_login']);
		$this->connected = false;
		
		$this->userName	= null;
		$this->login	= null;
		$this->password	= null;
		
		# redirect to homepage
		header('Location: '. $this->conf['general']['appURL']);
		
		return $login .' disconnected';
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
	
	protected function getLogin() {
		return $this->login;
	}
	
	/**
	 *
	 */
	
	public function getFlickrName() {
		return $this->flickrName;
	}
	
	/**
	 *
	 */
	
	protected function show () {
		return array (
			'name'			=> 'User',
			'description'	=> 'User manager'
		);
	}
	
	
	/**
	 *
	 */
	 
	private function updateBus() {
		# save user info
		bus::setData(
			'user', array (
				'login'			=> $this->getLogin(),
				'flickrName'	=> $this->getFlickrName()
			)
		);
	}
}