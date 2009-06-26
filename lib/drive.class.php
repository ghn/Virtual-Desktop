<?php

require_once ('tools.class.php');
require_once ('document.class.php');
require_once ('picture.class.php');

class drive {
	
	private $nbFiles = 0;
	private $absolutePath;
	private $rootPath;
	private $imgPath;
	
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
			$this->imgPath = '';
		} else {
			$this->absolutePath = $this->rootPath . $this->path .'/';
			$this->imgPath = $this->conf['general']['dataPath'] . $this->user . '/'. $this->path;
		}
	}
	
	/**
	 *
	 */
	
	public function run ($action = 'list') {
		
		# return the file it there is one.
		#  or list the current folder
		if (is_file($this->imgPath)) {
			$file = new document($this->imgPath);
			$file->getFile();
		} else {
			return $this->listCurrentFolder();
		}
	}
	
	
	/**
	 *
	 */
	
	public function nbFiles() {
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
				
				# folder name
		    	$shortFolderName = $folder;
				if (strlen($folder) > $this->conf['files']['nameMaxLenght']) {
					$shortFolderName = substr ($folder, 0, $this->conf['files']['nameMaxLenght']) .'...';
				}
				
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
					'name'		=> $shortFolderName,
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
				
				//$this->oFile = new document($this->absolutePath . $file);
				$this->oFile = new picture($this->absolutePath . $file);
				
				# file name
		    	$shortFileName = $file;
				if (strlen($file) > $this->conf['files']['nameMaxLenght']) {
					$shortFileName = substr ($file, 0, $this->conf['files']['nameMaxLenght']) .'...';
				}
				
				# file layout
				$link = '?path='. self::doUrl($this->path .'/'. $file);
				//$icon = $this->conf['general']['appURL'] .'theme/'. $this->conf['theme']['name'] .'/icons/unknown.png';
				
				$return[] = array (
					'type' 		=> 'document',
					'title'		=> $file,
					'path'		=> $this->conf['general']['appURL'] .'?path='. $this->oFile->getThumbnail(1),
					'icon'		=> $this->conf['general']['appURL'] .'?path='. $this->oFile->getThumbnail(0),
					'alt'		=> '',
					'name'		=> $shortFileName,
					'rel'		=> 'lightbox[set1]'
				);
			}
			
			# close ressource
			closedir($res);
		}
		return $return;
	}
	
	/**
	 * GET MENU ITEMS.
	 */
	
	public function getMenuItems () {

		# list folder at top level if exists
		$res = opendir($this->rootPath);
		
		$tab = array ();
		while (false !== ($file = readdir($res))) {
			if ((is_dir ($this->rootPath . $file) == 1) && ($file != ".") && ($file != "..") && (!in_array($file, $this->conf['files']['hiddenItems']))) {
				$tab[] = $file;
			}
		}
		sort($tab);
		
		# get root folder
		list($root) = explode ('/', $this->path);
		
		$ret[0]['class'] = '';
		$ret[0]['url'] = '';
		$ret[0]['name'] = 'Home';
		
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
}