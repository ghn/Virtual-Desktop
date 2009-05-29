<?php
	# Session valid?
	# load classes
	function __autoload($class_name) {
		require_once "lib/{$class_name}.php";
	}
	
	$drive = new CDrive();
	
	# then..
	if (isset($_GET['p'])) {
		$file = $_GET['p'];
		echo $drive->getFile($file);
	}
?>