<?php
	# Session valid?
	# load classes
	require_once('lib/starter.php');
	
	# then..
	if (isset($_GET['p'])) {
		$file = $_GET['p'];
		echo $drive->getFile($file);
	}