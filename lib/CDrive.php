<?php
/**
 *	CLASS DRIVE
 */

class CDrive {
	
	public $config = array ();			# configuration file
	
	# --------------------------------------------------------------------------
	# DO NOT MODIFIY
	
	protected 	$userDataPath;			# absolute path to the data folder
	protected	$absoluteDirectoryPath;	# absolute path to the current directory
	protected	$slash = '/';
	const		FILE_GET = 'get.php?p=';
	
	private $url_params = array();	# contains all folders leading to the current directory (work var)
	private $fileCount = 0;
	private $folderCount = 0;
	
	public $auth;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		
		$this->config = Spyc::YAMLLoad(dirname(__FILE__) .'/../config/config.yaml');
		$this->auth = new CAuth();
		
		# init user's folder path
		$this->userDataPath = $this->config['general']['dataPath'] . $this->auth->getLogin() . $this->slash;
		
		# get and manage url
		$this->initdirectory();
				
		# init theme path
		$this->config['theme']['path'] = $this->config['general']['appURL'] .'themes'. $this->slash . $this->config['theme']['name'];
	}
	
	/**
	 * INIT DIRECTORY.
	 * @param string directory
	 */
	private function initdirectory () {
		# no need to urldecode => $_GET already do this!
		if (isset($_GET['p']) && $_GET['p'] != '') {
			$url = $_GET['p'];
			$tabParams = spliti ($this->slash, $url);
			
			$this->url_params = $tabParams;
		} else {
			$this->url_params = NULL;
		}
		
		# get params from url
		$directory = $this->userDataPath;
		if (!empty ($this->url_params)) {
			foreach ($this->url_params as $id) {
				$directory .= $id .$this->slash;
			}
		}
		
		$this->absoluteDirectoryPath = $directory;
	}
	
	/**
	 * IS IMAGE ?
	 * @param string path of the file
	 */
	protected function isImage ($file) {
		
		$type = $this->getMimeType($file);
		
		if (in_array($type, $this->config['files']['pictures']['type'])) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * IS AUDIO ?
	 * @param string path of the file
	 */
	protected function isAudio ($file) {
		
		$type = $this->getMimeType($file);
		
		if (in_array($type, $this->config['files']['audio']['type'])) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * IS VIDEO ?
	 * @param string path of the file
	 */
	private function isVideo ($file) {
		
		$type = $this->getMimeType($file);
		
		if (in_array($type, $this->config['files']['video']['type'])) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * GET PATH
	 */ 
	public function getpath () {
		$path = "";
		
		if (!empty ($this->url_params)) {
			foreach ($this->url_params as $id) {
				$path .= $id . $this->slash;
			}
		}
		
		return $path;
	}
	
	/**
	 * MOVE UP.
	 * @param link of the top level folder if exists
	 */
	private function moveup ($level = 1) {
		$path = "";
		
		if (count ($this->url_params) == 1) {
			$path = '';
		} else {
			if (!empty ($this->url_params)) {
				for ($i=0; $i<count($this->url_params)-2; $i++) {
					$path .= $this->url_params[$i] .'/';
				}
				$path .= $this->url_params[$i];
			}
		}
		
		return urlencode($path);
	}
	
	/**
	 * LIST FOLDERS.
	 * @param context for function sprintf
	 */
	public function listfolders ($context='%s') {
		$return = '';
		
		# print 'go back' if needed
		if (!is_null($this->url_params)) {
			$return = '	<li class="folder">
							<a href="?p='. $this->moveup() .'" title="Go backward">
								<img src="'. $this->config['theme']['path'] .'/icons/folder-enable.png" alt="" title="Go backward" /><br />
								<span class="filename">...</span>
							</a>
						</li>';
		}
		
		# if folder exists
		if (is_dir($this->absoluteDirectoryPath)) {
			$res = opendir($this->absoluteDirectoryPath);
			$tab_1 = array ();
			
			# list current folder
			while (false !== ($file = readdir($res))) {
				if ((is_dir ($this->absoluteDirectoryPath . $file) == 1) && ($file != ".") && ($file != "..") && (!in_array($file, $this->config['files']['hiddenItems']))) {
					$this->folderCount += 1;
					$tab_1[] = $file;
		    	}
			}
			
			# sort result
			sort($tab_1);
			
			foreach ($tab_1 as $file) {
				$new_name = $file;
				
				# folder name
		    	$shortFolderName = $new_name;
				if (strlen($new_name) > $this->config['files']['nameMaxLenght']) {
					$shortFolderName = substr ($new_name, 0, $this->config['files']['nameMaxLenght']) .'...';
				}
				
				# folder layout
				$link = urlencode($this->getPath() . $new_name);
				
				$return .= '<li class="folder">
								<a href="?p='. $link .'" title="'. $new_name .'">
									<img src="'. $this->config['theme']['path'] . $this->slash .'icons/folder-enable.png" height="52" alt="" title="'. $new_name .'" /><br />
									<span class="filename">'. $shortFolderName .'</span>
								</a>
							</li>';
			}
			closedir($res);
			
			print sprintf ($context, $return);
		} else {

			# do something if folder selected does not exit
			print ('no such file or directory.');
		}
	}
	
	/**
	 * LIST FILES.
	 * @param context for function sprintf
	 */
	public function listfiles ($context='%s') {
		$return = '';
		$this->fileCount = 0;
		
		$file = array();
		
		# list folder and files if exists
		if (is_dir($this->absoluteDirectoryPath)) {
			$dimg = opendir($this->absoluteDirectoryPath);
			while (false !== ($imgfile = readdir($dimg))) {
	 			# filter items
				if (is_file($this->absoluteDirectoryPath . $imgfile) && !in_array($imgfile, $this->config['files']['hiddenItems'])) {
					$file[] = $imgfile;
		  	  		sort($file);
			  		reset ($file);
				  		
			  		$this->fileCount += 1;
				}
			}
			
	  	 	# print that list
	  	 	for($x=0; $x < count($file); $x++) {
	  	 		
	  	 		# URL to the file
				$picpath = self::FILE_GET. urlencode($this->getpath() . $file[$x]);
				
				# Absolute path to the file
				$filepath = $this->userDataPath . $this->getpath() . $file[$x];
				
				# filename
				$shortFilename = $longFilename = $file[$x];
				if (strlen($file[$x]) > $this->config['files']['nameMaxLenght']) {
					$shortFilename = substr ($file[$x], 0, $this->config['files']['nameMaxLenght']) .'...';
				}
				
				# define icon
				$icon = $this->config['theme']['path'] . $this->slash .'icons'. $this->slash . $this->getMimeType($filepath) .'.png';
				
				# IF IMAGE
				if ($this->isImage($filepath)) {
					
					# Get thumbnail
					print $filepath; exit;
					$cPic = new CPicture($filepath);
					$originalFile = "<a href='". $picpath ."'>Download original file</a>";
					$thumbnail = self::FILE_GET . urlencode($cPic->getThumbnail($this->config['files']['pictures']['thumbFormats'][0]));
					$thumbnail_slideshow = self::FILE_GET . urlencode($cPic->getThumbnail($this->config['files']['pictures']['thumbFormats'][1]));
					
					$return .= '<li class="image">
									<a href="'. $thumbnail_slideshow .'" title="'. $longFilename .'::'. $originalFile .'" rel="lightbox[set1]">
										<img src="'. $thumbnail .'" alt="'. $longFilename .'" /><br />
										<span class="filename">'. $shortFilename .'</span><br />
									</a>
									<div class="actions">
										<ul>
											<li><a href="#">do something</a></li>
											<li><a href="#">do something</a></li>
											<li><a href="#">do something</a></li>
										</ul>
									</div>
								</li>';

				# IF VIDEO
				} else if ($this->isVideo($filepath)) {
					$return .= '<li class="video">
								<a href="'. $picpath .'" title="'. $longFilename .'" rel="lightbox[flash 640 360]">
									<img src="'. $icon .'" alt="'. $longFilename .'" /><br />
									<span class="filename">'. $shortFilename .'</span>
								</a>
							</li>';
				
				# IF AUDIO
				} else if ($this->isAudio($filepath)) {
					$return .= '<li class="audio">
								<a href="'. $picpath .'" title="MP3 audio::'. $longFilename .'" rel="lightbox[audio 300 50]">
									<img src="'. $icon .'" alt="'. $longFilename .'" /><br />
									<span class="filename">'. $shortFilename .'</span>
								</a>
							</li>';
				
				# OTHER FORMAT
				} else {
					$return .= '<li class="document">
								<a href="'. $picpath .'" title="'. $longFilename .'">
									<img src="'. $icon .'" alt="'. $longFilename .'" /><br />
									<span class="filename">'. $shortFilename .'</span>
								</a>
							</li>';
				}
			}
			
			print sprintf ($context, $return);
			
		} else {
			
			# do something if the file does not exist
		}
	}

	/**
	 * COUNT FILES
	 * @param context for function sprintf
	 */	
	public function countfiles ($context='%s') {
		print sprintf ($context, $this->fileCount); 
	}
	
	/**
	 * COUNT FOLDERS
	 * @param context for function sprintf
	 */
	public function countfolders ($context='%s') {
		print sprintf ($context, $this->folderCount);
	}
	
	/**
	 * GET FILE.
	 * @param $file
	 */
	public function getFile ($file) {
		$fn = $this->userDataPath . $file;
		if (is_file($fn)) {
			$type = $this->getMimeType($fn);
			
			$filename = basename($fn);
			
			header('Content-Type: '. $type);
			header('Content-Length: '. filesize($fn));
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			print file_get_contents($fn);
		}
	}
	
	/**
	 * GET MENU ITEMS.
	 */
	public function getMenuItems () {
		
		# list folder at top level if exists
		$res = opendir($this->userDataPath);
		$tab_1 = array ();
		while (false !== ($file = readdir($res))) {
			if ((is_dir ($this->userDataPath . $file) == 1) && ($file != ".") && ($file != "..") && (!in_array($file, $this->config['files']['hiddenItems']))) {
				$this->folderCount += 1;
				$tab_1[] = $file;
			}
		}
		
		# sort folders
		sort($tab_1);
		
		$menu = "";
		foreach ($tab_1 as $folder) {
			if ($folder == $this->url_params[0]) {
				$menu .= '<li class="current"><a href="'. $this->config['general']['appURL'] .'?p='. urlencode($folder) .'">'. $folder .'</a></li>' ."\n";
			} else {
				$menu .= '<li><a href="'. $this->config['general']['appURL'] .'?p='. urlencode($folder) .'">'. $folder .'</a></li>' ."\n";
			}
		}		
		
		if (empty($this->url_params[0])) {
			$menu = '<li class="home current"><a href="'. $this->config['general']['appURL'] .'">'. $this->auth->getLogin() .'</a></li>'. "\n". $menu;
		} else {
			$menu = '<li class="home"><a href="'. $this->config['general']['appURL'] .'">'. $this->auth->getLogin() .'</a></li>'. "\n". $menu;
		}
		
		print '<ul>'. $menu .'</ul>';
	}
	
	/**
	 * GET MIME TYPE.
	 * @param string path of the file
	 */
	protected function getMimeType($file) {
		if (is_file($file)) {
			
			/*
			// !! CHECK YOUR SERVER PARAMETERS !!
			$mimeFile = 'C:/wamp/bin/php/php5.2.8/extras/magic';
			$handle = finfo_open(FILEINFO_MIME, $mimeFile);
			//$handle = finfo_open(FILEINFO_MIME);
			
			$mime = str_replace("/", "-", finfo_file($handle, $file));
			
			// Get the only reference only (ex: text-plain; textencode, ... => text-plain)
			$mime2 = strstr($mime, ';');
			if (strlen($mime2) > 0) {
				$mime = str_replace($mime2, '', $mime);
			}
			*/
			$mime = substr(strrchr($file, '.'), 1);
			
			return $mime;
		} else {
			return 'unknown';
		}
	}
	
	/**
	 * GET THEME NAME
	 * @param -emtpy-
	 */
	public function getThemeName() {
		return $this->config['theme']['path'];
	}
	
	public function getVersion() {
		return $this->config['general']['version'];
	}
	
	public function getAppTitle() {
		return $this->config['general']['appTitle'];
	}
	
	public function getAppURL() {
		return $this->config['general']['appURL'];
	}
}