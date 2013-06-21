<?php

require_once (LIB_CORE .'plugin.class.php');
require_once (LIB_CORE .'tools.class.php');
require_once (LIB_CORE .'folder.class.php');

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
	private $pathUrl		= '';
	
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
		$this->path = str_replace('../', '', $this->path);
		
		$this->pathUrl = $this->path;
		
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
						'path'			=> $this->pathUrl,
						'action'		=> '?action=drive.newfolder&path='. $this->pathUrl,
						'menuItems'		=> $this->getMenuItems(),
						'actionItems'	=> $this->getActionItems(),
						'nbFiles'		=> $this->nbFiles(),
						'directory'		=> $directory
					);
				}
			case 'upload':
				# if the file has been uploaded
				if (count($_POST) > 0) {
					
					$error = false;

					# file is uploaded?
					if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
						$error = 'Invalid Upload';
					}
					
					# check file format
					//if (!$error && !in_array($size[2], array(1, 2, 3, 7, 8) ) ) {
					//	$error = 'Please upload only images of type JPEG, GIF or PNG.';
					//}
					
					# return values
					if ($error) {
						$return = array(
							'status' => '0',
							'error' => $error
						);
					} else {
						$return = array(
							'status' => '1',
							'name' => $_FILES['Filedata']['name']
						);
						
						move_uploaded_file($_FILES['Filedata']['tmp_name'], $this->absolutePath . $_FILES['Filedata']['name']);
						
						// ... and if available, we get image data
						$info = @getimagesize($_FILES['Filedata']['tmp_name']);
					 
						if ($info) {
							$return['width'] = $info[0];
							$return['height'] = $info[1];
							$return['mime'] = $info['mime'];
						}
					}

					header('Content-type: application/json');
					echo json_encode($return);
					
					return array (
						'actionItems'	=> $this->getActionItems()
					);
					
				} else {
					return array (
						'path'			=> $this->pathUrl,
						'action'		=> $this->conf['general']['appURL'] .'?action=drive.upload&standalone=true&path='. $this->pathUrl,
						'menuItems'		=> $this->getMenuItems(),
						'actionItems'	=> $this->getActionItems(),
						'nbFiles'		=> $this->nbFiles(),
						'directory'		=> $directory
					);
				}
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
		$lstFolders = $lstFiles = array();
		$count = 0;
		
		# Display 'go back' if needed
		if (!empty($this->path)) {
			$lstFolders[] = array (
				'type' 		=> 'folder',
				'title'		=> 'Go backward',
				'path'		=> $this->moveup(),
				'icon'		=> $this->conf['general']['appURL'] .'theme/'. $this->conf['theme']['name'] .'/icons/folder-enable.png',
				'alt'		=> '',
				'name'		=> '..',
				'rel'		=> ''
				);
		}
		
		$dir = new folder($this->absolutePath);
		foreach($dir->listAll() as $key => $item) {
			if (!in_array($item['name'], $this->conf['files']['hiddenItems'])) {
				# folder layout
				if (empty($this->path)) {
					$link = '?path='. self::doUrl($item['name']);
				} else {
					$link = '?path='. self::doUrl($this->path . $item['name']);
				}
				
				switch ($item['type']) {
					case 'dir':
						$lstFolders[] = array (
							'type' 		=> 'folder',
							'title'		=> $item['name'],
							'path'		=> $link,
							'icon'		=> $this->conf['general']['appURL'] .'theme/'. $this->conf['theme']['name'] .'/icons/folder-enable.png',
							'alt'		=> '',
							'name'		=> self::makeShort($item['name']),
							'rel'		=> ''
						);
						break;
						
					case 'file':
						$type = tools::getType($this->absolutePath . $item['name']);
						$this->file[$key] = new $type($this->absolutePath . $item['name']);
						
						$lstFiles[] = array (
							'type' 		=> $this->file[$key]->getFormat(),
							'title'		=> $item['name'],
							'path'		=> $this->file[$key]->getURL(),
							'icon'		=> $this->file[$key]->getIcon(),
							'alt'		=> $this->file[$key]->getFormat(),
							'name'		=> $this->makeShort($item['name']),
							'rel'		=> $this->file[$key]->getRelAttribut()
						);
						$count++;
						break;
				}
			}
		}
		
		$this->nbFiles = $count;
		
		# merge both array and return
		return array_merge($lstFolders, $lstFiles);
	}
	
	/**
	 * GET MENU ITEMS.
	 */
	
	protected function getMenuItems () {
		
		# Link to "Home"
		if (empty($this->path)) {
			$ret[0]['name']	= 'Home';
			$ret[0]['url']	= $this->conf['general']['appURL'];
			$ret[0]['class']= 'current';
		} else {
			$ret[0]['name']	= 'Home';
			$ret[0]['url']	= $this->conf['general']['appURL'];
			$ret[0]['class']= '';
		}
		
		# get root folders
		list($root) = explode ('/', $this->path);
		
		# list folder at top level if exists
		$dir = new folder ($this->rootPath);
		
		foreach($dir->listAll() as $key => $item) {
			if (($item['type'] == 'dir') && (!in_array($item['name'], $this->conf['files']['hiddenItems']))) {
				
				if ($item['name'] == $root) {
					$ret[$key+1]['class'] = 'current';
				} else {
					$ret[$key+1]['class'] = '';
				}
				
				$ret[$key+1]['url'] = '?path='. $item['name'];
				$ret[$key+1]['name'] = $item['name'];
			}
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
			return $this->conf['general']['appURL'] .'?path='. $this->doUrl($up);
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
				'url'	=> '?action='. $this->pluginName .'.newfolder&amp;path='. $this->pathUrl,
				'name'	=> 'Create a new folder'),
			1	=> array(
				'url'	=> '?action='. $this->pluginName .'.removefolder&amp;path='. $this->pathUrl,
				'name'	=> 'Delete the current folder'),
			2	=> array(
				'url'	=> '?action='. $this->pluginName .'.upload&amp;path='. $this->pathUrl,
				'name'	=> 'Upload files'),
			3	=> array(
				'url'	=> '?action='. $this->pluginName .'.about',
				'name'	=> 'About')
			);
	}
}