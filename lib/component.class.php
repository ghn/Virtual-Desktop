<?php

abstract class component {
	
	protected $path;
	protected $template;
	
	public function __construct() {
	}
		
	
	public function build () {
		$conf = config::get();
		
		$tpl =& new HTML_Template_Sigma('./theme/'. $conf['theme']['name'], './cache');
		$tpl->setErrorHandling(PEAR_ERROR_DIE);
		$tpl->loadTemplateFile('default.html');

		$tpl->setVariable(array(
			'appTitle' 		=> $conf['general']['appTitle'],
			'appVersion'	=> $conf['general']['version'],
			'themeName'		=> $conf['theme']['name'],
			'appURL'		=> $conf['general']['appURL'],
			'urlUpload'		=> $conf['general']['appURL'] .'?action=upload&amp;path=',
			'urlCreateFolder'=>$conf['general']['appURL'] .'?action=create.folder&amp;path=',
			'urlDisconnect'	=> $conf['general']['appURL'] .'?action=user.logout',
			'urlConnect'	=> $conf['general']['appURL'] .'?action=user.login',
			
			'username'		=> '#username#',
			'menuItems'		=> '<ul><li><a href="#">la liste</a></li></ul>',
			'directory'		=> $this->path,
			'nbFiles'		=> 8,
			'content'		=> $this->get()
			));
		$tpl->setCurrentBlock('log_in');
		$tpl->setVariable(array (
			'texte' => 'Please login',
			'urlDisconnect'	=> $conf['general']['appURL'] .'?action=log.in'));
		
		$tpl->hideBlock('log_out'); //$tpl->setCurrentBlock('log_out');
		$tpl->show();
	}
	
	
	public function get() {
	}
	
	
	public function run () {
	}
}