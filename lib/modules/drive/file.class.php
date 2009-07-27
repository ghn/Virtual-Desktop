<?php

require_once(LIB_CORE .'tools.class.php');

abstract class file {
	
	protected $file		= null;		// file we'll work on (filesystem)
	protected $path		= null;
	protected $conf		= array();	// configuration parameters
	protected $format	= null;		// file format
	protected $mime		= null;
	protected $user		= null;
	
	/**
	 *
	 */
	 
	public function __construct($file) {
		$this->conf = config::get();
		$this->file = $file;
		$this->format = tools::getType($file);
		$this->mime = tools::getMimeType($file);
		
		$datas = bus::getData('user');
		$this->user = $datas['login'];
		
		$this->path = bus::getData('path');
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
					readfile($this->file);
					break;
					
				default:
					header('Content-Type: '. $this->mime);
					header('Content-Length: '. filesize($this->file));
					header('Content-Disposition: attachment; filename="'. $filename .'"');
					header('Cache-Control: no-cache, must-revalidate');
					readfile($this->file);
			}
		}
	}
	
	/**
	 *
	 */
	
	public function getFormat() {
		return $this->format;
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
	
	public function getIcon() {
		
		switch ($this->format) {
			case 'picture':
				return $this->conf['general']['appURL'] .'?path='. $this->getThumbnail(0);
				break;
				
			default:
				return $this->conf['general']['appURL'] .'theme/'. $this->conf['theme']['name'] .'/icons/unknown.png';
		}
	}
	
	/**
	 *
	 */
	
	public function getURL() {
		return $this->conf['general']['appURL'] .'?path='. $this->path . basename($this->file);
	}
	
	/**
	 *
	 */
	
	public function getRelAttribut() {
		
		switch ($this->format) {
			case 'picture':
				return 'lightbox[set1]';
				break;
			
			case 'audio':
				return 'lightbox[audio 50% 40]';
				break;
			
			case 'video':
				return 'lightbox[flash 640 360]';
				break;
			default:
				return '';
		}
	}
}