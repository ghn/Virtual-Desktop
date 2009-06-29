<?php

require_once (LIB_MOD .'/user/user.class.php');

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
			$this->setComponentVars($html);
			
			# search all plugins
			$modules = $this->listModules();
			$this->setComponentVars($modules);
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
			
			# check if module file exists
			$classFile = LIB_MOD . $action .'/'. $action .'.class.php';
			
			if (file_exists($classFile)) {
				require_once ($classFile);
				
				# class exists?
				if (class_exists($action)) {
					$this->action = $action;
				}
			} else {
				require_once (LIB_MOD . $this->action .'/'. $this->action .'.class.php');
			}
		} else {
			require_once (LIB_MOD . $this->action .'/'. $this->action .'.class.php');
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
	 
	private function setComponentVars($html) {
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
	
	/**
	 *	TODO: to be completed !!
	 */
	
	private function listModules() {
		
		$tabModules = array ();
		
		# search for modules in {appPath}/lib/modules/{module_name}/{module_name}.class.php
		$res = opendir(LIB_MOD);
		$i = 0;
		while (false !== ($fModule = readdir($res))) {
			if (is_dir(LIB_MOD . $fModule) && $fModule != '.' && $fModule != '..') {
				if (is_file(LIB_MOD . $fModule .'/'. $fModule .'.class.php') && $fModule != 'user') {
					$tabModules[$i]['name'] = $fModule;
					$tabModules[$i]['link'] = $this->conf['general']['appURL'] .'?action='. $fModule .'.show';
					++$i;
				}
			}
		}
		closedir($res);
		$ret = array (
			'modules'	=> $tabModules
		);
		
		return $ret;
	}
}