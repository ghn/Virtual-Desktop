<?php

# required plugins
require_once (LIB_MOD .'/logs/logs.class.php');
require_once (LIB_MOD .'/user/user.class.php');

class controller {
	
	private $conf			= array();	// configuration
	private $action			= 'drive';	// module
	private $actionMethod	= null;		// method within the module
	
	private $user;						// instance of class user
	private $tpl;						// instance of class template
	
	/**
	 *
	 */
	
	public function __construct () {
		
		$this->conf = config::get();
		
		$this->getParams();
		$this->initTemplate();
		
		# Create user object and log result if any
		$this->user = new user();
		if ($this->action == 'user') {
			$ret = $this->user->run($this->actionMethod);
			logs::write('user', $ret);
		}
		
		# execute action if connected!
		if ($this->user->isConnected()) {
		
			# hide items
			$this->tpl->hideBlock('log_in');
			
			# execute plugin, then render it
			$plugin = new $this->action ();
			$html = $plugin->run($this->actionMethod);
			$this->setPluginVars($html);
			
			# search JS & CSS files
			$js = $plugin->getJS();
			$css = $plugin->getCSS();
			$this->setDependencies($js, $css);
			
			# search all plugins
			$modules = $this->listModules();
			$this->setVars($modules);
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
		$this->tpl->setCurrentBlock('__global__');
		$this->tpl->setVariable(array(
			'appTitle' 		=> $this->conf['general']['appTitle'],
			'appVersion'	=> $this->conf['general']['version'],
			'themeName'		=> $this->conf['theme']['name'],
			'appURL'		=> $this->conf['general']['appURL'],
			'urlUpload'		=> $this->conf['general']['appURL'] .'?action=upload.show',
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
			@list($action, $this->actionMethod) = explode('.', $_GET['action']);
			
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
		
		# check action method
		if (empty($this->actionMethod)) {
			$this->actionMethod = 'show';
		}
	}
	
	/**
	 *	INIT TEMPLATE
	 */
	
	private function initTemplate() {
		$this->tpl = new HTML_Template_Sigma('./theme/'. $this->conf['theme']['name'], 'cache');
		$this->tpl->setErrorHandling(PEAR_ERROR_DIE);
		$this->tpl->loadTemplateFile('default.html');
	}
	
	/**
	 *	SET VARIABLES
	 */
	 
	private function setPluginVars($html) {
		
		# print all items and all attribut
		if (is_array($html)) {
			$this->tpl->setRoot(LIB_MOD . $this->action .'/');
			$this->tpl->addBlockfile('plugin__place', $this->action, 'templates/'. $this->action .'-'. $this->actionMethod .'-layout.html');
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
			$this->tpl->parseCurrentBlock();
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 *	SET VARIABLES
	 */
	 
	private function setVars($html) {
		
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
			$this->tpl->parseCurrentBlock();
			
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 *
	 */
	
	private function setDependencies($js = null, $css = null) {
		
		# set JS
		if ($js != null){
			$this->tpl->setCurrentBlock('plugin-js');
			foreach ($js as $item) {
				$this->tpl->setVariable('name', $this->conf['general']['appURL'] .'lib/modules/'. $item);
				$this->tpl->parse('plugin-js');
			}
		}
		
		# set CSS
		if ($css != null) {
			$this->tpl->setCurrentBlock('plugin-css');
			foreach ($css as $item) {
				$this->tpl->setVariable('name', $this->conf['general']['appURL'] .'lib/modules/'. $item);
				$this->tpl->parse('plugin-css');
			}
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
				if (is_file(LIB_MOD . $fModule .'/'. $fModule .'.class.php')) {
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