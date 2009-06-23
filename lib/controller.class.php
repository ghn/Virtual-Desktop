<?php

require_once ('user.class.php');
require_once ('drive.class.php');

class controller {
	
	private $conf;				// configuration
	private $action;			// module
	private $action_method;		// method within the module
	
	private $path;				// current path
	private $user;				// user
	
	private $tpl;				// template
	
	/**
	 *
	 */
	
	public function __construct () {
		
		$this->conf = config::get();
		
		$this->getParams();
		$this->initTemplate();
		
		# execute action if connected!
		$this->user = new user();
		$this->user->run($this->action_method);
		
		if ($this->user->isConnected()) {
			
			$drive = new drive ($this->path, $this->user->getLogin());
			
			# hide items
			$this->tpl->hideBlock('log_in');
			$this->tpl->setCurrentBlock('drive');
			$this->tpl->setVariable(array(
				'nbFiles'		=> $drive->nbFiles(),
				'directory'		=> $this->path
			));
			
			# execute drive, then render it
			$html = $drive->run($this->action);
			
			# print all items and all attribut
			if (is_array($html)) {
				$this->tpl->setCurrentBlock('file');
				
				foreach ($html as $item) {
					foreach ($item as $k => $v) {
						$this->tpl->setVariable(array ($k => $v));
					}
					$this->tpl->parse('file');
				}
			}
			
			# print menu items
			$menu = $drive->getMenuItems();
			
			# print all items and all attribut
			if (is_array($menu)) {
				$this->tpl->setCurrentBlock('menuItems');
				
				foreach ($menu as $item) {
					foreach ($item as $k => $v) {
						$this->tpl->setVariable(array ($k => $v));
					}
					$this->tpl->parse('menuItems');
				}
			}
			
		} else {
			$this->tpl->setCurrentBlock('drive');
			$this->tpl->setVariable(array (
				'date' => date('l jS \of F Y h:i:s A')
			));
			
			# hide items
			$this->tpl->hideBlock('log_out');
			$this->tpl->hideBlock('tools');
		}
		
		# print general information
		$this->tpl->setVariable(array(
			'appTitle' 		=> $this->conf['general']['appTitle'],
			'appVersion'	=> $this->conf['general']['version'],
			'themeName'		=> $this->conf['theme']['name'],
			'appURL'		=> $this->conf['general']['appURL'],
			'urlUpload'		=> $this->conf['general']['appURL'] .'?action=upload&amp;path=',
			'urlCreateFolder'=>$this->conf['general']['appURL'] .'?action=create.folder&amp;path=',
			'urlDisconnect'	=> $this->conf['general']['appURL'] .'?action=user.logout',
			'urlConnect'	=> $this->conf['general']['appURL'] .'?action=user.login',
			
			'username'		=> $this->user->getUserName()
		));
		
		# print the page
		$this->tpl->show();
	}
	
	
	/**
	 *	GET URL PARAMETERS
	 */
	
	private function getParams() {
		# get action
		if (isset($_GET['action']) && !empty($_GET['action'])) {
			@list($this->action, $this->action_method) = explode('.', $_GET['action']);
		} else {
			$this->action = 'drive';		// default action
		}
		
		# get path
		if (isset($_GET['path']) && !empty($_GET['path'])) {
			$this->path = $_GET['path'];
		} else {
			$this->path = '';
		}
	}
	
	/**
	 *	INIT TEMPLATE
	 */
	
	private function initTemplate() {
		$this->tpl =& new HTML_Template_Sigma('./theme/'. $this->conf['theme']['name'], './cache');
		$this->tpl->setErrorHandling(PEAR_ERROR_DIE);
		$this->tpl->loadTemplateFile('default.html');
	}
}