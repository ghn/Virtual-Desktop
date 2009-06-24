<?php

abstract class file {
	
	protected $file		= '';		// file we'll work on (filesystem)
	protected $conf		= array();	// configuration parameters
	protected $format;				// file format
	
	protected $audio	= false;	// true | false
	protected $video	= false;	// true | false
	protected $picture	= false;	// true | false
	
	/**
	 *
	 */
	 
	public function __construct($file) {
		$this->conf = config::get();
		$this->file = $file;
		$this->getMimeType();
	}
	
	/**
	 *
	 */
	 
	public function getFile() {
		
		if (is_file($this->file)) {
			
			$filename = basename($this->file);
			
			if ($this->isVideo() || $this->isPicture() || $this->isAudio()) {
				header('Content-Type: '. $this->format);
				header('Content-Length: '. $this->getSize());
				header('filename="'. $filename .'"');
				header('Cache-Control: no-cache, must-revalidate');
				print file_get_contents($this->file);
			} else {
				header('Content-Type: '. $this->format);
				header('Content-Length: '. filesize($this->file));
				header('Content-Disposition: attachment; filename="'. $filename .'"');
				header('Cache-Control: no-cache, must-revalidate');
				print file_get_contents($this->file);
			}
		}
	}
	
	/**
	 *
	 */
	
	public function getSize() {
		return filesize($this->file);
	}
	
	/**
	 *
	 */
	
	public function isVideo() {
		return $this->video;
	}
	
	/**
	 *
	 */
	
	public function isPicture() {
		return $this->picture;
	}
	
	/**
	 *
	 */
	
	public function isAudio() {
		return $this->audio;
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
	 
	private function getMimeType() {
		
		if (is_file($this->file)) {
		
			#
			#	MIME TYPE
			#
			
			if (isset($this->conf['files']['mimeMagicPath'])) {
				$finfo = finfo_open(FILEINFO_MIME, $this->conf['files']['mimeMagicPath']);
			} else {
				$finfo = finfo_open(FILEINFO_MIME);
			}
			$mime = finfo_file($finfo, $this->file);

			// Get the only reference only (ex: text-plain; textencode, ... => text-plain)
			$mime2 = strstr($mime, ';');
			if (strlen($mime2) > 0) {
			$mime = str_replace($mime2, '', $mime);
			}
			
			$this->format = $mime;
			
			#
			#	MEDIA TYPE
			#
			
			if (in_array($this->format, $this->conf['files']['video']['type'])) {
				$this->video = true;
			}
			
			if (in_array($this->format, $this->conf['files']['pictures']['type'])) {
				$this->picture = true;
			}
			
			if (in_array($this->format, $this->conf['files']['audio']['type'])) {
				$this->audio = true;
			}
			
		} else {
			$this->format = 'unknown';
		}
	}
}