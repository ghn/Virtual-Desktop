<?php

class CAuthAccounts extends CAuth {
	
	/**************************************************************************/
	
	private $accounts = array (
		"test"		=> array (
			"password"	=> "098f6bcd4621d373cade4e832627b4f6",
			"fullname"	=> "test account"),
		/* add more accounts here */);
	
	/**************************************************************************/
	
	protected $isAutorized	= false;
	protected $fullname		= "";
	
	/**
	 *	CHECK LOGIN [login, password]
	 *    write result in protected var [account]
	 */
	public function __construct($login, $password) {
		
		$this->isAutorized = false;
		
		// check login
		if (array_key_exists($login, $this->accounts)) {
			
			//check password
			if ($this->accounts[$login]["password"] == md5($password)) {
				$this->isAutorized = true;
				$this->fullname = $this->accounts[$login]["fullname"];
			}
		}
	}
}