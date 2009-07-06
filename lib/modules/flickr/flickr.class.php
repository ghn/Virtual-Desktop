<?php

require_once ('phpFlickr.php');

class flickr implements module {
	
	private $user;			// flickr user
	private $fli;			// instance of flickr
	
	private $conf;
	
	/**
	 *
	 */
	
	public function __construct() {
		
		$this->conf = config::get();
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
	
	public function run ($action = 'list') {
		return array (
				'flickrList'	=> $this->getPictures(),	// must be called first
				'menuItems'		=> $this->getMenuItems(),
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
	
	private function getMenuItems() {
		return array (
			0	=> array (
				'url'	=> 'Mon url',
				'name'	=> 'Flickr Pictures',
				'class'	=> 'current'
			)
		);
	}
	
	/**
	 *
	 */
	
	private function nbFiles() {
		return $this->nbFiles;
	}
}