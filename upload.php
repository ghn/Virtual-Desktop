<?php require_once('lib/starter.php'); ?>

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
		<script type="text/javascript" src="js/fancyUpload/Swiff.Uploader.js"></script>
		<script type="text/javascript" src="js/fancyUpload/Fx.ProgressBar.js"></script>
		<script type="text/javascript" src="js/fancyUpload/FancyUpload2.js"></script>
		<script type="text/javascript" src="js/fancyUpload/FancyScript.js"></script>
		
		<link rel="stylesheet" type="text/css" href="<?php print $drive->getThemeName(); ?>/style.css" />
		<link rel="stylesheet" type="text/css" href="themes/light-view/fancyUpload/css/fancyUpload.css" media="screen, projection" />
		
		<style type="text/css">
			body {background: #fff;}
		</style>
	</head>
	
	<body>
		<div id="demo">
			<form action="upload-script.php" method="post" enctype="multipart/form-data" id="form-demo">
				<fieldset id="demo-fallback">
					<legend>File Upload</legend>
					<p>Selected your photo to upload.</p>
					<label for="demo-photoupload">
						Upload Photos:
						<input type="file" name="photoupload" id="demo-photoupload" />
					</label>
				</fieldset>
			 
				<div id="demo-status" class="hide">
					<p>
						<a href="#" id="demo-browse">Browse Files</a> |
						<input type="checkbox" id="demo-select-images" /> Images Only |
						<a href="#" id="demo-clear">Clear List</a> |
						<a href="#" id="demo-upload">Upload</a>
					</p>
					<div>
						<strong class="overall-title">Overall progress</strong><br />
						<img src="themes/light-view/fancyUpload/images/progress-bar/progress.gif" class="progress overall-progress" alt="bar" />
					</div>
					<div>
						<strong class="current-title">File Progress</strong><br />
						<img src="themes/light-view/fancyUpload/images/progress-bar/progress.gif" class="progress current-progress" alt="bar" />
					</div>
					<div class="current-text"></div>
				</div>
			 
				<ul id="demo-list"></ul>
			 
			</form>
		</div>
	</body>
</html>