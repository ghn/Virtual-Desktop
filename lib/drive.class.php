<?php

class drive extends component {
	
	private $files = array();
	private $tmp;
	
	public function __construct ($path = '') {
		$this->path = $path;
	}
	
	public function get () {
		return $this->tmp;
	}
	
	public function run () {
		$this->tmp = "trop cool";
	}
	
	
	public function listfolders () {
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
			
			return $return;
		} else {

			# do something if folder selected does not exit
			return 'no such file or directory.';
		}
	}
}