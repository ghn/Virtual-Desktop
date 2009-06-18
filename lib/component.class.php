<?php

abstract class component {
	
	protected $path;
	protected $conf;
	
	public function __construct() {
	}
		
	
	public function build () {
		$this->conf = config::get();
		return $this->get();
	}
	
	
	public function run () {
	}
}