<?php

/* starter */

class starter {

	public function init() {
		
		# enable compression
		ob_start("ob_gzhandler");
		
		session_start();
		
		# include required files
		require_once ('PEAR.php');
		require_once ('Sigma.php');
		require_once ('tools.class.php');
		require_once ('spyc.class.php');
		require_once ('config.class.php');
		require_once ('controller.class.php');
		
		# Read config
		config::load();
		
		# Init Controller
		$controller = new controller();
	}
}