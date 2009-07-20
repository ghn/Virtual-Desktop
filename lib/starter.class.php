<?php

/* starter */

class starter {

	public function init() {
		
		# enable compression
		ob_start("ob_gzhandler");
		
		define('LIB_CORE', dirname(__FILE__) .'/core/');
		define('LIB_MOD', dirname(__FILE__) .'/modules/');
		
		session_start();
		
		# include required files
		require_once ('PEAR.php');
		require_once (LIB_CORE .'/Sigma.php');
		require_once (LIB_CORE .'/tools.class.php');
		require_once (LIB_CORE .'/bus.class.php');		
		require_once (LIB_CORE .'/config.class.php');
		require_once (LIB_CORE .'/controller.class.php');
		
		# Read config
		config::load();
		
		# Init Controller
		$controller = new controller();
	}
}