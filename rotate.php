<?php
	require_once('lib/starter.php');
	
	# then..
	if (isset($_GET['p'])) {
		$file = $_GET['p'];
		
		$picture = new CPicture($file);
		$picture->rotate(90);
	}
?>