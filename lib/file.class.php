<?php

require_once('tools.class.php');

abstract class file {
	
	protected $file		= '';		// file we'll work on (filesystem)
	protected $conf		= array();	// configuration parameters
	protected $format;				// file format
	protected $mime;
	
	/**
	 *
	 */
	 
	public function __construct($file) {
		$this->conf = config::get();
		$this->file = $file;
		$this->format = tools::getType($file);
		$this->mime = tools::getMimeType($file);
	}
	
	/**
	 *
	 */
	 
	public function getFile() {
		
		if (is_file($this->file)) {
			$filename = basename($this->file);
			
			switch ($this->format) {
				case 'audio':
				case 'video':
				case 'picture':
					header('Content-Type: '. $this->mime);
					header('Content-Length: '. $this->getSize());
					header('filename="'. $filename .'"');
					header('Cache-Control: no-cache, must-revalidate');
					print file_get_contents($this->file);
					break;
					
				default:
					header('Content-Type: '. $this->mime);
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
}