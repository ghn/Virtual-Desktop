<?php

require_once (LIB_CORE .'plugin.class.php');
require_once ('phpFlickr.php');

class flickr extends plugin {
	
	private $user;			// flickr user
	private $fli;			// instance of flickr
	
	/**
	 *
	 */
	
	public function __construct() {
		parent::__construct();
		
		$datas = bus::getData('user');
		
		$this->user = $datas['flickrName'];
		$this->fli = new phpFlickr($this->conf['flickr']['APIKey']);
	}
	
	/**
	 *
	 */
	
	public function __destruct() {
		unset ($this->fli);
	}
	
	/**
	 *
	 */
	
	protected function show () {
		return array (
			'flickrList'	=> $this->getPictures(),	// must be called first
			'nbFiles'		=> $this->nbFiles(),
			'directory'		=> 'my flickr account.'
		);
	}
	
	/**
	 *
	 */
	
	private function getPictures() {
		
		$photos_url = $this->fli->urls_getUserPhotos($this->user);
		$photos = $this->fli->people_getPublicPhotos($this->user, NULL, NULL, 36);
		
		// Loop through the photos and output the html
		$return = array();
		foreach ((array)$photos['photos']['photo'] as $photo) {
			$return[] = array (
				'icon'	=> $this->fli->buildPhotoURL($photo, 'Square'),
				'alt'	=> $photo['title'],
				'path'	=> $this->fli->buildPhotoURL($photo), //$photos_url . $photo['id'],
				'rel'	=> 'lightbox[set1]'
			);
		}
		
		$this->nbFiles = count($return);
		return $return;
	}
	
	/**
	 *
	 */
	
	private function nbFiles() {
		return $this->nbFiles;
	}
}