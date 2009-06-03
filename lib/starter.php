<?php
/**
 *	LOAD ALL CLASSES NEEDED
 *	  and create a new drive instance
 */

	# load classes
	function __autoload($class_name) {
		require_once dirname(__FILE__) ."/{$class_name}.php";
	}
	
	# create a new drive
	$drive = new CDrive();