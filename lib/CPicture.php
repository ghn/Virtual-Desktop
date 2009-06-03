<?php

class CPicture extends CDrive {

	/*
	 *	ATTRIBUTS
	 */
	protected $picture;		// we will work on this picture
	protected $config = array();
	
	/*
	 *	WORKING VARS
	 */
	private $thumbnail;
	private $maxWidth;
	private $maxHeight;
	private $thumbWidth;
	private $thumbHeight;
	
	/*** PUBLIC FUNCTIONS **************************************************************************/
	
	/*
	 *	CONSTRUCTOR
	 */
	public function __construct($config, $image = "") {
		$this->config = $config;
		$this->picture = $image;
	}
	
	/*
	 *	GET THUMBNAIL
	 */
	public function getThumbnail($formatID, $forceCreate = false) {
		$this->maxWidth = $this->config['files']['pictures']['thumbFormats'][$formatID][0];
		$this->maxHeight = $this->config['files']['pictures']['thumbFormats'][$formatID][1];
		
		$file = basename($this->picture);
		
		$source = str_replace($this->config['tmp']['userDataPath'], '', $this->picture);
		$source = str_replace($file, '', $source);
		
		//echo $source .'<br>';
		
		# thumbnail exists?
		$thumbPath = $this->config['tmp']['userDataPath'] . $this->config['general']['thumbnailFolder'] . $this->slash . $source;
		$this->thumbnail = $thumbPath . $this->maxWidth .'x'. $this->maxHeight .'-'. $file;
		
		if ($forceCreate) {
			$this->removeThumbnail($format);
		}
		
		if (!file_exists($this->thumbnail) || $forceCreate) {
			$this->mkdir_r($thumbPath);
			$this->createThumbnail();
		}
		
		$ret = $this->config['general']['thumbnailFolder'] . $this->slash . $source . $this->maxWidth .'x'. $this->maxHeight .'-'. $file;
		return $ret;
	}
	
	/*
	 *	ROTATE (could be 90, 180, 270 ONLY)
	 */
	public function rotate($angle) {
		/*if (!in_array($angle, array(90, 180, 270)) {return false;}*/
	}
	
	/*
	 *	REMOVE ORIGINAL PICTURE
	 */
	public function remove() {
		
		# remove file
		
		# remove thumbnails
		$this->removeThumbnail();
	}
	
	/*
	 *	REMOVE THUMBNAIL PICTURE
	 */
	private function removeThumbnail($format) {
		
		# remove thumbnails
		@unlink($this->thumbnail);
	}
	
	/*** PRIVATE FUNCTIONS **************************************************************************/
	
	/*
	 *	CREATE THUMBNAIL
	 */
	private function createThumbnail() {
		
		# thumb created only if format supported
		if (!$this->isImage($this->picture)) {return false;}
		
		$thumb_w = '';
		$thumb_h = '';
		
		# Create thumbnail for supported format only
		$format = $this->getMimeType($this->picture);
		
		switch($format) {
			case 'jpg':
			case 'jpeg':
				$src_img = imagecreatefromjpeg($this->picture);
				
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
	 *	CALCUL THUMB FORMAT
	 *		IN: real_w / real_h AND max_w / max_h
	 *		OUT: thumb_w / thumb_h
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
	
	/*
	 *	CREATE FOLDERS
	 */
	private function mkdir_r($dirName, $rights=0777) {
		$dirs = explode('/', $dirName);
		$dir = '';
		
		foreach ($dirs as $part) {
			$dir .= $part .'/';
			
			if (strlen($dir)>0 && $dir != $this->slash) {
				@mkdir($dir, $rights);
			}
		}
	}
}