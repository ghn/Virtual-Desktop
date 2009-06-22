<?php

abstract class file {
	
	protected $file = '';
	protected $conf = array();
	
	/**
	 *
	 */
	 
	public function __construct($file) {
		$this->conf = config::get();
		$this->file = $file;
	}
	
	/**
	 *
	 */
	 
	public function getFile() {
		
		if (is_file($this->file)) {
			$type = $this->getMimeType();
			
			$filename = basename($this->file);
			
			if ($this->isVideo($this->file) || $this->isImage($this->file) || $this->isAudio($this->file)) {
				header('Content-Type: '. $type);
				header('Content-Length: '. filesize($this->file));
				header('filename="'.$filename.'"');
				header('Cache-Control: no-cache, must-revalidate');
				print file_get_contents($this->file);
			} else {
				header('Content-Type: '. $type);
				header('Content-Length: '. filesize($this->file));
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				header('Cache-Control: no-cache, must-revalidate');
				print file_get_contents($this->file);
			}
		}
	}
	
	/*
	 *  CREATE FOLDERS
	 */
	
	protected function mkdir_r($dirName, $rights = 0777) {
		$dirs = explode('/', $dirName);
		$dir = '';
		
		foreach ($dirs as $part) {
			$dir .= $part .'/';

			if (strlen($dir)>0 && $dir != '/') {
				@mkdir($dir, $rights);
			}
		}
	}
	
	/**
	 *
	 */
	 
	public function getMimeType() {
		
		if (is_file($this->file)) {
			$finfo = finfo_open(FILEINFO_MIME, $this->conf['files']['mimeMagicPath']);
			$mime = finfo_file($finfo, $this->file);

			// Get the only reference only (ex: text-plain; textencode, ... => text-plain)
			$mime2 = strstr($mime, ';');
			if (strlen($mime2) > 0) {
			$mime = str_replace($mime2, '', $mime);
			}
			
			return $mime;
		} else {
			return 'unknown';
		}
	}
	
	/**
	 *
	 */
	
	public function run () {
		$this->tmp = "module Drive";
	}
	
	/**
	 *
	 */
	
	public function isVideo() {
		if (in_array($this->getMimeType(), $this->conf['files']['video']['type'])) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 *
	 */
	
	public function isImage() {
		
		if (in_array($this->getMimeType(), $this->conf['files']['pictures']['type'])) {
			return true;
			
		} else {
			return false;
		}
	}
	
	/**
	 *
	 */
	
	public function isAudio() {
		
		if (in_array($this->getMimeType(), $this->conf['files']['audio']['type'])) {
			return true;
		} else {
			return false;
		}
	}
}