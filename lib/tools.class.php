<?php

class tools {

	/**
	 *
	 */
	 
	public function getMimeType($file) {
	
		$conf = config::get();
		
		if (is_file($file)) {
			$finfo = finfo_open(FILEINFO_MIME, $conf['files']['mimeMagicPath']);
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
	
	/*
	 *  CREATE FOLDERS
	 */
	
	public function mkdir_r($dirName, $rights = 0777) {
		$dirs = explode('/', $dirName);
		$dir = '';
		
		foreach ($dirs as $part) {
			$dir .= $part .'/';

			if (strlen($dir)>0 && $dir != '/') {
				@mkdir($dir, $rights);
			}
		}
	}
}