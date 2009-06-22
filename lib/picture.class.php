<?php

require_once ('file.class.php');

class picture extends file {
	
	private $maxWidth;
	private $maxHeight;
	
	private $thumbWidth;
    private $thumbHeight;
	
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
		$icon = $this->conf['general']['appURL'] .'theme/'. $this->conf['theme']['name'] .'/icons/unknown.png';
		
		////////////
		$this->maxWidth = $this->conf['files']['pictures']['thumbFormats'][$formatID][0];
		$this->maxHeight = $this->conf['files']['pictures']['thumbFormats'][$formatID][1];
		
		$filename = basename($this->file);
		
		$source = str_replace($this->conf['general']['dataPath'], '', $this->file);
		$source = str_replace($filename, '', $source);
		
		# get username
		list($user, $source) = explode('/', $source);
		
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
		
		$ret = $this->conf['general']['appURL'] .'?path='. $this->conf['general']['thumbnailFolder'] .'/'. $source .'/'. $this->maxWidth .'x'. $this->maxHeight .'-'. $filename;
		
		return $ret;
	}
	
	/**
	 *
	 */
	
	private function createThumbnail() {
	
		# thumb created only if format supported
		if (!$this->isImage()) {return false;}
		
		$thumb_w = 0;
		$thumb_h = 0;
		
		# Create thumbnail for supported format only
		$format = $this->getMimeType();
		
		switch($format) {
			case 'image/jpg':
			case 'image/jpeg':
				$src_img = imagecreatefromjpeg($this->file);
				
				$realWidth = imageSX($src_img);
				$realHeight = imageSY($src_img);
				
				$this->calculFormat($realWidth, $realHeight);
				
				$dst_img = ImageCreateTrueColor($this->thumbWidth, $this->thumbHeight);
				imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $this->thumbWidth, $this->thumbHeight, $realWidth, $realHeight);        
				
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
  private function calculFormat($realWidth, $realHeight) {
    if ($realWidth > $realHeight) {
      $this->thumbWidth = $this->maxWidth;
      $this->thumbHeight = ceil($realHeight * ($this->maxHeight / $realWidth));
    }
    
    if ($realWidth < $realHeight) {
      $this->thumbWidth = ceil($realWidth * ($this->maxWidth / $realHeight));
      $this->thumbHeight = $this->maxHeight;
    }
    
    if ($realWidth == $realHeight) {
      $this->thumbWidth = $this->maxWidth;
      $this->thumbHeight = $this->maxHeight;
    }
  }
}