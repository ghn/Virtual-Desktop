<?php

class CAuthAccounts extends CAuth {

	private $accounts = array();
	protected $isAutorized	= false;
	protected $fullname		= "";
	
	/**
	 *	CHECK LOGIN [login, password]
	 *    write result in protected var [account]
	 */
	public function __construct($login, $password) {
		
		$this->accounts = Spyc::YAMLLoad(dirname(__FILE__) .'/../config/accounts.yaml');
		
		$this->isAutorized = false;
		
		# check login
		if (array_key_exists($login, $this->accounts)) {
			
			# account must be enabled
			if ($this->accounts[$login]["enable"]) {
			
				# check password
				if ($this->accounts[$login]["password"] == sha1($password)) {
					$this->isAutorized = true;
					$this->fullname = $this->accounts[$login]["name"];
				}
			}
		}
	}
}