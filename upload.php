<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Demo</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="themes/light-view/fancyUpload/css/fancyUpload.css" media="screen, projection" />
		
		<script type="text/javascript" src="https://www.germain.cn:443/finder/js/mootools-1.2.1-core-yc.js"></script>
		<script type="text/javascript" src="https://www.germain.cn:443/finder/js/fancyUpload/Swiff.js"></script>
		<script type="text/javascript" src="https://www.germain.cn:443/finder/js/fancyUpload/Fx.js"></script>
		<script type="text/javascript" src="https://www.germain.cn:443/finder/js/fancyUpload/FancyUpload2.js"></script>
		<script type="text/javascript" src="https://www.germain.cn:443/finder/js/fancyUpload/FancyScript.js"></script>
	</head>
	
	<body>
		<div id="demo">
			<form action="https://www.germain.cn:443/finder/upload-script.php" method="post" enctype="multipart/form-data" id="form-demo">
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
						<img src="themes/light-view/fancyUpload/images/bar.gif" class="progress overall-progress" alt="bar" />
					</div>
					<div>
						<strong class="current-title">File Progress</strong><br />
						<img src="themes/light-view/fancyUpload/images/bar.gif" class="progress current-progress" alt="bar" />
					</div>
					<div class="current-text"></div>
				</div>
			 
				<ul id="demo-list"></ul>
			 
			</form>
		</div>
	</body>
</html>