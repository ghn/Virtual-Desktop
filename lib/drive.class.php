<?php

require_once ('tools.class.php');
require_once ('document.class.php');
require_once ('picture.class.php');
require_once ('audio.class.php');
require_once ('video.class.php');

class drive implements module {
	
	private $nbFiles = 0;
	private $absolutePath;
	private $rootPath;
	private $userPath;
	private $oFile;
	
	protected $path = '';
	protected $user;
	protected $conf = array();
	
	/**
	 *
	 */
	
	public function __construct ($path, $user) {
		
		$this->conf = config::get();
		
		$this->path = $path;
		$this->user = $user;
		
		$this->rootPath = $this->conf['general']['dataPath'] . $this->user . '/';
		tools::mkdir_r($this->rootPath);
		
		if (empty($this->path)) {
			$this->absolutePath = $this->rootPath;
			$this->userPath = '';
		} else {
			$this->absolutePath = $this->rootPath . $this->path .'/';
			$this->userPath = $this->conf['general']['dataPath'] . $this->user . '/'. $this->path;
		}
	}
	
	/**
	 *
	 */
	
	public function run ($action = 'list') {
		
		# return the file it there is one.
		#  or list the current folder
		
		if (is_file($this->userPath)) {
			$file = new document($this->userPath);
			$file->getFile();
		} else {
			if (empty($this->path)) {
				$directory = 'root folder';
			} else {
				$directory = $this->path;
			}
			return array (
				'list'		=> $this->listCurrentFolder(),	// must be called first
				'menuItems'	=> $this->getMenuItems(),
				'nbFiles'	=> $this->nbFiles(),
				'directory'	=> $directory
			);
		}
	}
	
	/**
	 *
	 */
	
	private function nbFiles() {
		return $this->nbFiles;
	}
	
	/**
	 *
	 */
	
	private function listCurrentFolder () {
		$return = array();
		
		# print 'go back' if needed
		if (!empty($this->path)) {
			$return[] = array (
				'type' 		=> 'folder',
				'title'		=> 'Go backward',
				'path'		=> $this->moveup(),
				'icon'		=> $this->conf['general']['appURL'] .'theme/'. $this->conf['theme']['name'] .'/icons/folder-enable.png',
				'alt'		=> '',
				'name'		=> '...',
				'rel'		=> ''
				);
		}
		
		if (is_dir($this->absolutePath)) {
			$res = opendir($this->absolutePath);
			
			#
			#	LIST FOLDERS
			#
			
			$tabFolders = array ();
			while (false !== ($folder = readdir($res))) {
				if ((is_dir($this->absolutePath . $folder)) && ($folder != ".") && ($folder != "..") && (!in_array($folder, $this->conf['files']['hiddenItems']))) {
					$tabFolders[] = $folder;
		    	}
			}
			
			# sort result
			sort($tabFolders);
			
			foreach ($tabFolders as $folder) {
				
				# folder layout
				if (empty($this->path)) {
					$link = '?path='. self::doUrl($folder);
				} else {
					$link = '?path='. self::doUrl($this->path .'/'. $folder);
				}
				
				$return[] = array (
					'type' 		=> 'folder',
					'title'		=> $folder,
					'path'		=> $link,
					'icon'		=> $this->conf['general']['appURL'] .'theme/'. $this->conf['theme']['name'] .'/icons/folder-enable.png',
					'alt'		=> '',
					'name'		=>  $this->makeShort($folder),
					'rel'		=> ''
				);
			}
			
			# close ressource
			closedir($res);
			
			#
			#	LIST FILES
			#
			
			$res = opendir($this->absolutePath);
			$tabFiles = array ();
			while (false !== ($file = readdir($res))) {
				if ((is_file($this->absolutePath . $file)) && ($file != ".") && ($file != "..") && (!in_array($file, $this->conf['files']['hiddenItems']))) {
					$tabFiles[] = $file;
		    	}
			}
			
			# sort result
			sort($tabFiles);
			$this->nbFiles = count($tabFiles);
			
			foreach ($tabFiles as $file) {
				$type = tools::getType($this->absolutePath . $file);
				$this->oFile = new $type($this->absolutePath . $file);
				
				switch ($type) {
					case 'picture':
						# file layout
						$return[] = array (
							'type' 		=> $type,
							'title'		=> $file,
							'path'		=> $this->conf['general']['appURL'] .'?path='. $this->oFile->getThumbnail(1),
							'icon'		=> $this->conf['general']['appURL'] .'?path='. $this->oFile->getThumbnail(0),
							'alt'		=> $type,
							'name'		=> $this->makeShort($file),
							'rel'		=> 'lightbox[set1]'
						);
						break;
					
					case 'audio':
						# file layout
						$return[] = array (
							'type' 		=> $type,
							'title'		=> $file,
							'path'		=> $this->conf['general']['appURL'] .'?path='. $this->path .'/'. $file,
							'icon'		=> $this->conf['general']['appURL'] .'?path=',
							'alt'		=> $type,
							'name'		=> $this->makeShort($file),
							'rel'		=> 'lightbox[audio 50% 40]'
						);
						break;
					
					case 'video':
						# file layout
						$return[] = array (
							'type' 		=> $type,
							'title'		=> $file,
							'path'		=> $this->conf['general']['appURL'] .'?path='. $this->path .'/'. $file,
							'icon'		=> $this->conf['general']['appURL'] .'?path=',
							'alt'		=> $type,
							'name'		=> $this->makeShort($file),
							'rel'		=> 'lightbox[flash 640 360]'
						);
						break;
						
					default:
						# file layout
						$return[] = array (
							'type' 		=> $type,
							'title'		=> $file,
							'path'		=> $this->conf['general']['appURL'] .'?path='. $this->path .'/'. $file,
							'icon'		=> $this->conf['general']['appURL'] .'?path='. '',
							'alt'		=> $type,
							'name'		=> $this->makeShort($file),
							'rel'		=> ''
						);
				}
			}
			
			# close ressource
			closedir($res);
		}
		return $return;
	}
	
	/**
	 * GET MENU ITEMS.
	 */
	
	private function getMenuItems () {

		# list folder at top level if exists
		$res = opendir($this->rootPath);
		
		$tab = array ();
		while (false !== ($file = readdir($res))) {
			if ((is_dir ($this->rootPath . $file) == 1) && ($file != ".") && ($file != "..") && (!in_array($file, $this->conf['files']['hiddenItems']))) {
				$tab[] = $file;
			}
		}
		sort($tab);
		
		if (empty($this->path)) {
			$ret[0]['class'] = 'current';
			$ret[0]['url'] = $this->conf['general']['appURL'];
			$ret[0]['name'] = 'Home';
		} else {
			$ret[0]['class'] = '';
			$ret[0]['url'] = $this->conf['general']['appURL'];
			$ret[0]['name'] = 'Home';
		}
		
		# get root folder
		list($root) = explode ('/', $this->path);
		foreach ($tab as $key => $val) {
			if ($val == $root) {
				$ret[$key+1]['class'] = 'current';
			} else {
				$ret[$key+1]['class'] = '';
			}
			
			$ret[$key+1]['url'] = '?path='. $val;
			$ret[$key+1]['name'] = $val;
		}
		
		return $ret;
	}
	
	/**
	 * MOVE UP.
	 * @param link of the top level folder if exists
	 */
	
	private function moveup () {
		
		$up = explode ('/', $this->path, -1);
		$up = implode ('/', $up);
		
		if (empty($up)) {
			return $this->conf['general']['appURL'];
		} else {
			return $this->conf['general']['appURL'] .'?path='. self::doUrl($up);
		}
	}
	
	/**
	 *
	 */
	
	private function doUrl($url) {
		$url = urlencode($url);
		$url = str_replace('%2F', '/', $url);
		return $url;
	}
	
	/**
	 *
	 */

	private function makeShort($string) {
		
		if (strlen($string) > $this->conf['files']['nameMaxLenght']) {
			return substr ($string, 0, $this->conf['files']['nameMaxLenght']) .'...';
		} else {
			return $string;
		}
	}
}