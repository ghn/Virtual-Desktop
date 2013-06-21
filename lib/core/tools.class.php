<?php

class tools {

	/**
	 *
	 */
	 
	public function getMimeType($file) {
	
		# check if it is a file
		if (is_file($file)) {
			$conf = config::get();
			
			if (!isset($conf['files']['mimeMagicPath']) || is_null($conf['files']['mimeMagicPath'])) {
				$finfo = finfo_open(FILEINFO_MIME);
			} else {
				$finfo = finfo_open(FILEINFO_MIME, $conf['files']['mimeMagicPath']);
			}
			$mime = finfo_file($finfo, $file);
			
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
	 *	return: type !! must be a class name too !!
	 */
	 
	public function getType($file) {
		
		$mime = self::getMimeType($file);
		$conf = config::get();
		$type = '';
		
		#
		#	MEDIA TYPE [audio / video / picture / document]
		#
		
		if (in_array($mime, $conf['files']['video']['type'])) {
			$type = 'video';
		} else if (in_array($mime, $conf['files']['pictures']['type'])) {
			$type = 'picture';
		} else if (in_array($mime, $conf['files']['audio']['type'])) {
			$type = 'audio';
		} else {
			$type = 'document';
		}
		
		return $type;
	}
	
	/**
	 *	GET SYMBOL QUANTITY
	 */
	
	public function getSymbolByQuantity($bytes) {
		$symbols = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
		$exp = floor(log($bytes)/log(1024));

		return sprintf('%.2f '.$symbols[$exp], ($bytes/pow(1024, floor($exp))));
	}
}