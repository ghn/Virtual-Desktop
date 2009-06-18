<?php

/* starter */

class starter {

	public function init() {
		
		session_start();
		
		# include required files
		require_once ('PEAR.php');
		require_once ('sigma.php');
		require_once ('spyc.class.php');
		require_once ('config.class.php');
		require_once ('component.class.php');
		require_once ('controller.class.php');
		
		# Read config
		config::load();
		
		# Init Controller
		$controller = new controller();
	}
}