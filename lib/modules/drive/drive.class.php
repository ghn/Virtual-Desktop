<?php

require_once (LIB_CORE .'plugin.class.php');
require_once (LIB_CORE .'tools.class.php');

require_once ('folder.class.php');

require_once ('document.class.php');
require_once ('picture.class.php');
require_once ('audio.class.php');
require_once ('video.class.php');

class drive extends plugin {
	
	private $nbFiles		= 0;
	private $absolutePath	= null;
	private $rootPath		= null;
	private $file			= array();	// array of files
	private $path			= '';
	
	/**
	 *
	 */
	
	public function __construct () {
		parent::__construct();
		
		# get current path
		if (isset($_GET['path']) && !empty($_GET['path'])) {
			$this->path = $_GET['path'];
		} else {
			$this->path = '';
		}
		
		# get user login
		$user = bus::getData('user');
		$login = $user['login'];
		
		# init user path and create his folder if needed
		$this->rootPath = $this->conf['general']['dataPath'] . $login . '/';
		$dir = new folder($this->rootPath);
		$dir->create();
		
		if (is_file($this->rootPath . $this->path)) {
			$this->absolutePath = $this->rootPath . $this->path;
		} else {
			if (!empty($this->path)) {
				$this->absolutePath = $this->rootPath . $this->path  .'/';
				$this->path = $this->path  .'/';
			} else {
				$this->absolutePath = $this->rootPath . $this->path;
			}
		}
		
		bus::setData('path', $this->path);
	}
	
	/**
	 *
	 */
	
	public function run ($actionMethod = 'show') {
	
		$this->actionMethod = $actionMethod;
		
		# return the file it there is one.
		#  or list the current folder
		if (is_file($this->absolutePath)) {
			$file = new document($this->absolutePath);
			$file->getFile();
			exit;
		}
		
		if (empty($this->path)) {
			$directory = 'root folder';
		} else {
			$directory = $this->path;
		}
		
		switch ($actionMethod) {
			case 'show':
				return array (
					'driveList'		=> $this->listCurrentFolder(),	// must be called first
					'menuItems'		=> $this->getMenuItems(),
					'actionItems'	=> $this->getActionItems(),
					'nbFiles'		=> $this->nbFiles(),
					'directory'		=> $directory
				);
				break;
				
			case 'newfolder':
				if (isset($_POST['foldername'])) {
					$foldername = $_POST['foldername'];
					$dir = new folder($this->absolutePath . $foldername);
					$dir->create();
					
				} else {
					return array (
						'path'			=> $this->path,
						'action'		=> '?action=drive.newfolder&path='. $this->path,
						'menuItems'		=> $this->getMenuItems(),
						'actionItems'	=> $this->getActionItems(),
						'nbFiles'		=> $this->nbFiles(),
						'directory'		=> $directory
					);
				}
			case 'upload':
				return array (
						'path'			=> $this->path,
						'action'		=> '?action=drive.newfolder&path='. $this->path,
						'menuItems'		=> $this->getMenuItems(),
						'actionItems'	=> $this->getActionItems(),
						'nbFiles'		=> $this->nbFiles(),
						'directory'		=> $directory
					);
				break;
				
			default:
				return array (
					'menuItems'		=> $this->getMenuItems(),
					'actionItems'	=> $this->getActionItems(),
					'nbFiles'		=> $this->nbFiles(),
					'directory'		=> $directory
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
		
		# Display 'go back' if needed
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
		
		$dir = new folder($this->absolutePath);
		foreach($dir->listAll() as $folder) {
			if (!in_array($folder, $this->conf['files']['hiddenItems'])) {
				# folder layout
				if (empty($this->path)) {
					$link = '?path='. self::doUrl($folder);
				} else {
					$link = '?path='. self::doUrl($this->path . $folder);
				}
				
				$return[] = array (
					'type' 		=> 'folder',
					'title'		=> $folder,
					'path'		=> $link,
					'icon'		=> $this->conf['general']['appURL'] .'theme/'. $this->conf['theme']['name'] .'/icons/folder-enable.png',
					'alt'		=> '',
					'name'		=> self::makeShort($folder),
					'rel'		=> ''
				);
			}
		}
		
		
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
		
		foreach ($tabFiles as $key => $file) {
		
			$type = tools::getType($this->absolutePath . $file);
			$this->file[$key] = new $type($this->absolutePath . $file);
			
			$return[] = array (
				'type' 		=> $this->file[$key]->getFormat(),
				'title'		=> $file,
				'path'		=> $this->file[$key]->getURL(),
				'icon'		=> $this->file[$key]->getIcon(),
				'alt'		=> $this->file[$key]->getFormat(),
				'name'		=> self::makeShort($file),
				'rel'		=> $this->file[$key]->getRelAttribut()
			);
		}
		
		# close ressource
		closedir($res);

		return $return;
	}
	
	/**
	 * GET MENU ITEMS.
	 */
	
	protected function getMenuItems () {

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
		
		$up = explode ('/', $this->path, -2);
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
	
	/**
	 *
	 */
	
	protected function getActionItems() {
		return array(
			0	=> array(
				'url'	=> '?action='. $this->pluginName .'.newfolder&amp;path='. $this->path,
				'name'	=> 'Create a new folder'),
			1	=> array(
				'url'	=> '?action='. $this->pluginName .'.removefolder&amp;path='. $this->path,
				'name'	=> 'Delete the current folder'),
			2	=> array(
				'url'	=> '?action='. $this->pluginName .'.upload&amp;path='. $this->path,
				'name'	=> 'Upload files'),
			3	=> array(
				'url'	=> '?action='. $this->pluginName .'.about',
				'name'	=> 'About')
			);
	}
}