<?php

require_once ('file.class.php');

class picture extends file {
	
	private $maxWidth	= 0;
	private $maxHeight	= 0;
	
	private $realWidth	= 0;
	private $realHeight	= 0;
	
	private $thumbWidth	= 0;
    private $thumbHeight= 0;
	
	/**
	 *
	 */
	
	public function __construct ($file) {
		parent::__construct($file);
	}
	
	/**
	 *
	 */
	
	public function getThumbnail($formatID, $forceCreate = false) {
		
		if ($this->isPicture()) {
			$this->maxWidth = $this->conf['files']['pictures']['thumbFormats'][$formatID][0];
			$this->maxHeight = $this->conf['files']['pictures']['thumbFormats'][$formatID][1];
			
			$filename = basename($this->file);
			
			$source = str_replace($this->conf['general']['dataPath'], '', $this->file);
			$source = str_replace($filename, '', $source);
			
			# get username
			list($user) = explode('/', $source);
			$source = str_replace($user, '', $source);
			
			# thumbnail exists?
			$thumbPath = $this->conf['general']['dataPath'] . $user .'/'. $this->conf['general']['thumbnailFolder'] . '/' . $source .'/';
			$this->thumbnail = $thumbPath . $this->maxWidth .'x'. $this->maxHeight .'-'. $filename;
			
			if ($forceCreate) {
			  $this->removeThumbnail($format);
			}
			
			if (!file_exists($this->thumbnail) || $forceCreate) {
				$this->mkdir_r($thumbPath);
				$this->createThumbnail();
			}
			
			if (empty($source)) {
				$ret = '';
			} else {
				$ret = $this->conf['general']['thumbnailFolder'] . $source . $this->maxWidth .'x'. $this->maxHeight .'-'. $filename;
			}
			
			return $ret;
			
		} else {
			return false;
		}
	}
	
	/**
	 *
	 */
	
	private function createThumbnail() {
		
		# Create thumbnail for supported format only
		switch($this->format) {
			case 'image/jpg':
			case 'image/jpeg':
				$src_img = imagecreatefromjpeg($this->file);
				
				$this->realWidth = imageSX($src_img);
				$this->realHeight = imageSY($src_img);
				
				$this->calculFormat();
				
				$dst_img = ImageCreateTrueColor($this->thumbWidth, $this->thumbHeight);
				imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $this->thumbWidth, $this->thumbHeight, $this->realWidth, $this->realHeight);        
				
				imagejpeg($dst_img, $this->thumbnail);
				
				# right 777 so I can manually remove the files if I want to
				@chmod($this->thumbnail, 0777);
				
				imagedestroy($dst_img);
				imagedestroy($src_img);
				break;

			case 'png':
				break;
			case 'gif':
				break;
		}
	}
	
	/*
	 *  CALCUL THUMB FORMAT
	 *    IN: real_w / real_h AND max_w / max_h
	 *    OUT: thumb_w / thumb_h
	 */

	private function calculFormat() {
		if ($this->realWidth > $this->realHeight) {
			$this->thumbWidth = $this->maxWidth;
			$this->thumbHeight = ceil($this->realHeight * ($this->maxHeight / $this->realWidth));
		}
		
		if ($this->realWidth < $this->realHeight) {
			$this->thumbWidth = ceil($this->realWidth * ($this->maxWidth / $this->realHeight));
			$this->thumbHeight = $this->maxHeight;
		}
		
		if ($this->realWidth == $this->realHeight) {
			$this->thumbWidth = $this->maxWidth;
			$this->thumbHeight = $this->maxHeight;
		}
	}
}