<?php

class drive extends component {
	
	private $files = array();
	private $tmp;
	private $user;
	
	public function __construct ($path = '', $user = 'test') {
		$this->path = $path;
		$this->user = $user;
	}
	
	public function get () {
		return $this->listAll();
	}
	
	public function run () {
		$this->tmp = "module Drive";
	}
	
	
	private function listAll () {
		$return = array();
		
		# print 'go back' if needed
		if (!is_null($this->path)) {
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
		
		# rel image lightbox[set1]
		
		$absolutePath = $this->conf['general']['dataPath'] . $this->user . '/'. $this->path;
		
		if (is_dir($absolutePath)) {
			$res = opendir($absolutePath);
			
			#
			#	LIST FOLDERS
			#
			
			$tabFolders = array ();
			while (false !== ($folder = readdir($res))) {
				if ((is_dir($absolutePath . $folder)) && ($folder != ".") && ($folder != "..") && (!in_array($folder, $this->conf['files']['hiddenItems']))) {
					$tabFolders[] = $folder;
		    	}
			}
			
			$this->folderCount = count($tabFolders);
			
			# sort result
			sort($tabFolders);
			
			foreach ($tabFolders as $file) {
				
				# folder name
		    	$shortFolderName = $file;
				if (strlen($file) > $this->conf['files']['nameMaxLenght']) {
					$shortFolderName = substr ($file, 0, $this->conf['files']['nameMaxLenght']) .'...';
				}
				
				# folder layout
				$link = '?path='. urlencode($this->path . $file);
				
				$return[] = array (
					'type' 		=> 'folder',
					'title'		=> $file,
					'path'		=> $link,
					'icon'		=> $this->conf['general']['appURL'] .'theme/'. $this->conf['theme']['name'] .'/icons/folder-enable.png',
					'alt'		=> '',
					'name'		=> $shortFolderName,
					'rel'		=> ''
				);
			}
			
			#
			#	LIST FILES
			#
			
			$tabFiles = array ();
			while (false !== ($file = readdir($res))) {
				if ((is_file($absolutePath . $file)) && ($file != ".") && ($file != "..") && (!in_array($file, $this->conf['files']['hiddenItems']))) {
					$tabFiles[] = $file;
		    	}
			}
			
			# sort result
			sort($tabFiles);
			
			print_r ($tabFiles);
			
			foreach ($tabFiles as $file) {
				
				# folder name
		    	$shortFileName = $file;
				if (strlen($file) > $this->conf['files']['nameMaxLenght']) {
					$shortFolderName = substr ($file, 0, $this->conf['files']['nameMaxLenght']) .'...';
				}
				
				# folder layout
				$link = '?path='. urlencode($this->path . $file);
				
				$return[] = array (
					'type' 		=> 'file',
					'title'		=> $file,
					'path'		=> $link,
					'icon'		=> $this->conf['general']['appURL'] .'theme/'. $this->conf['theme']['name'] .'/icons/folder-enable.png',
					'alt'		=> '',
					'name'		=> $shortFileName,
					'rel'		=> ''
				);
			}
			
			# close ressource
			closedir($res);
		}
		return $return;
	}
	
	/**
	 * MOVE UP.
	 * @param link of the top level folder if exists
	 */
	private function moveup () {
		$path = '';

		/*
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
		*/
		
		if (empty($this->path)) {
			return $this->conf['general']['appURL'];
		} else {
			return $this->conf['general']['appURL'] .'?path='. urlencode($this->path);
		}
	}
}