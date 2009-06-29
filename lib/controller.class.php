<?php

require_once ('user.class.php');

class controller {
	
	private $conf			= array();	// configuration
	private $action			= 'drive';	// module
	private $action_method	= null;		// method within the module
	private $path			= '';		// current path
	
	private $user;						// instance of class user
	private $tpl;						// instance of class template
	
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
			
			# hide items
			$this->tpl->hideBlock('log_in');
			
			# execute component, then render it
			$component = new $this->action ($this->path, $this->user->getLogin());
			$html = $component->run($this->action_method);
			$this->setModuleVar($html);
			
		} else {
			
			# hide items
			$this->tpl->hideBlock('log_out');
			$this->tpl->hideBlock('tools');
			
			# render homepage
			$this->tpl->setCurrentBlock('homepage');
			$this->tpl->setVariable(array (
				'error'	=> $this->user->getError(),
				'date'	=> date('l jS \of F Y h:i:s A')
			));
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
			@list($action, $this->action_method) = explode('.', $_GET['action']);
			
			# check if class file exists
			$classFile = $action .'.class.php';
			
			if (file_exists(dirname(__FILE__) .'/'. $classFile)) {
				require_once ($classFile);
				
				# class exists?
				if (class_exists($action)) {
					$this->action = $action;
				}
			} else {
				require_once ($this->action .'.class.php');
			}
		} else {
			require_once ($this->action .'.class.php');
		}
		
		# get path
		if (isset($_GET['path']) && !empty($_GET['path'])) {
			$this->path = $_GET['path'];
		}
	}
	
	/**
	 *	INIT TEMPLATE
	 */
	
	private function initTemplate() {
		$this->tpl =& new HTML_Template_Sigma('./theme/'. $this->conf['theme']['name'], 'cache');
		$this->tpl->setErrorHandling(PEAR_ERROR_DIE);
		$this->tpl->loadTemplateFile('default.html');
	}
	
	/**
	 *	SET VARIABLES
	 */
	 
	private function setModuleVar($html) {
		# print all items and all attribut
		if (is_array($html)) {
			$this->tpl->setCurrentBlock($this->action);
			
			foreach ($html as $key => $var) {
				if (!is_array($var)) {
					$this->tpl->setVariable($key, $var);
				} else {
					foreach ($var as $item) {
						foreach ($item as $k => $v) {
							$this->tpl->setVariable(array ($k => $v));
						}
						$this->tpl->parse($key);
					}
				}
			}
			
			return true;
		} else {
			return false;
		}
	}
}