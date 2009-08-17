<?php

class folder {
	
	private $createMode = '0777';
	private $folder;
	
	/**
	 *
	 */
	
	public function __construct($folder = null) {
		$this->folder = $folder;
	}
	
	/**
	 *
	 */
	
	public function setFolder($folder) {
		$this->folder = $folder;
	}
	
	/**
	 *
	 */
	
	public function getFolder() {
		return $this->folder;
	}
	
	/**
	 *
	 */
	
	public function create() {
		if (!is_null($this->folder)) {
			
			# create the folder if it doesn't exist
			if (!is_dir($this->folder)) {
				$this->mkdir_r($this->folder);
				$ret = 'RET_CREATED';
			} else {
				$ret = 'RET_EXISTS';
			}
		} else {
			$ret = 'RET_NO_FOLDER_SPECIFIED';
		}
		
		return $ret;
	}
	
	/**
	 *	LIST THE CURRENT FOLDER
	 */
	
	public function listAll() {
		if (is_dir($this->folder)) {
			$res = opendir($this->folder);
			
			$tabFolders = array ();
			while (false !== ($item = readdir($res))) {
				if ((is_dir($this->folder . $item)) && ($item != ".") && ($item != "..")) {
					$tabFolders[] = $item;
		    	}
			}
			closedir($res);
			
			# sort result
			sort($tabFolders);
			$ret = $tabFolders;
		} else {
			$ret = 'RET_FOLDER_DOES_NOT_EXIST';
		}
		
		return $ret;
	}
	
	/*
	 *  CREATE FOLDERS
	 */
	
	private function mkdir_r($folder) {
		$dirs = explode('/', $folder);
		$dir = '';
		
		foreach ($dirs as $part) {
			$dir .= $part .'/';

			if (strlen($dir)>0 && $dir != '/') {
				@mkdir($dir, $this->createMode);
			}
		}
	}
}