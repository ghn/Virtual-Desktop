<?php
	require_once('lib/starter.php');
	
	# logout ?
	if (isset($_GET['action']) && $_GET['action'] == 'logout') {
		$drive->auth->logout();
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php print $drive->getAppTitle() .' v'. $drive->getVersion(); ?></title>
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		
		<!-- no cache -->
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta name="robots" content="noindex, nofollow" />
		
		<script type="text/javascript" src="js/mootools-1.2.1-core-yc.js"></script>
		<script type="text/javascript" src="js/mootools-1.2-more.js"></script>
		<script type="text/javascript" src="js/mediaBoxAdv/mediaboxAdv94.js"></script>
		
		<link rel="stylesheet" type="text/css" href="<?php print $drive->getThemeName(); ?>/style.css" />
	</head>
	
	<body>
		<div id="page">
			<div id="header">
				<h1><a href="<?php print $drive->getAppURL(); ?>"><?php print $drive->getAppTitle() .' v'. $drive->getVersion(); ?></a></h1>
			</div>
			
			<div id="login">
				<p>
					<span class="logged">Logged as <?php print $drive->auth->getLogin(); ?></span>
					<span class="logout"><a href="index.php?action=logout">Logout</a></span>
				</p>
			</div>
			
			<div id="content">
				<div id="navigation">
					<div id="tools">
						<div class="box">
							<div class="box-title">
								Options
							</div>
							<div class="box-content">
								<ul>
									<li><a href="upload.php" rel="lightbox[external 640 360]">Add files</a></li>
								</ul>
							</div>
						</div>
					</div>
					
					<div id="shortcuts">
						<div class="box">
							<div class="box-title">
								Folders
							</div>
							<div class="box-content">
								<?php $drive->getMenuItems(); ?>
							</div>
						</div>
					</div>
				</div>
				
				<div id="list">
					<ul>
						<?php $drive->listfolders(); ?>
						<?php $drive->listfiles(); ?>
					</ul>
					
					<div class="info">
						<div class="directory"><?php print $drive->getpath (); ?></div>
						<?php $drive->countfiles ('<p class="result">There is %s files in that folder.</p>'); ?>
					</div>
				</div>
			</div>
			
			<div id="footer">
				<p class="copyright">&copy; 2007-<?php print date('Y', time()); ?> germain.cn | <a href="thanks.html" rel="lightbox[external 640 360]" title="Credits">Credits</a></p>
			</div>
		</div>
	</body>
</html>