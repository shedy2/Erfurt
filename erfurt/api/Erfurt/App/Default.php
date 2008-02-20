<?php
/**
 * Erfurt application class
 *
 * @package app
 */
class Erfurt_App_Default {
	
	/**
	 * @var object auth instance
	 */
	protected $auth = null;
	
	/**
	 * @var object instance auth adapter
	 */
	protected $authAdapter = null;
	
	/**
	 * @var object instance of ac-object
	 */
	protected $ac = null;
	
	/**
	 * @var object instance of model-object
	 */
	protected $acModel = null;
	
	/**
	 * @var object instance of model-object
	 */
	protected $sysOnt = null;
	
	/**
	 * @var array array of store instances
	 */
	protected $store = array();
	
	/**
	  * @var Denotes whether Erfurt should throw exceptions
	  *      or render errors itself instead.
	  */
	protected $throwExceptions;
	
	/**
	 *  
	 * @var string default store name
	 */
	protected $defaultStore = 'default';
	
	/**
	 *  
	 * @var object event handler
	 */
	protected $ed = null;
	
	/**
	 *  
	 * @var object for plugin manager
	 */
	protected $pluginManager = null;
	
	/**
	 * constructor
	 *
	 * @param object configuration parameters
	 */
	public function __construct($config, $username = '', $password = '', $throwExceptions = false) {
		$this->throwExceptions = $throwExceptions;
		
		if (Zend_Registry::isRegistered('erfurtLog')) {
			Zend_Registry::get('erfurtLog')->debug('Erfurt_App_Default::__construct()');
		}
		
		# general object for event handling
		$this->ed = new Erfurt_EventDispatcher($this);
		
		# init plugin manager 
		$this->pluginManager = new Erfurt_PluginManager($this);
		if ($config->plugins->erfurt && strlen($config->plugins->erfurt)>0) {
    		$this->pluginManager->init(REAL_BASE . $this->config->plugins->erfurt); # Is absolute path correct for erfurt?
		}
		
		$storeClass = 'Erfurt_Store_Adapter_'.ucfirst(($config->database->backend ? $config->database->backend : 'rap'));
		
		$defaultStore = $this->store[$this->defaultStore] = new $storeClass($config->database->params->type, 
																			$config->database->params->host, 
																			$config->database->params->dbname, 
																			$config->database->params->username, 
																			$config->database->params->password, 
																			$config->SysOntModelURI,
																			$config->database->tableprefix);
		
		try {
			# check for tables and possible sys-ont
			# TODO: remove sysont
			$defaultStore->init();
		} catch (Erfurt_Exception $e) {
			if ($e->getCode() == 1) {
				
				$msg = $e->getMessage() . '<br/>' . 
				       'Database Setup: OntoWiki tables created<br />';
				$defaultStore->createTables($config->database->type);
				
				if ($throwExceptions) {
					header('Refresh: 3; url=' . $_SERVER['REQUEST_URI']);
					throw new Erfurt_Exception($msg . '<br />Refreshing in 3 seconds.');
				}
				
				echo $msg . '<br /><b>Please reload now (the next step will last a few seconds)</b>';
				exit();
			} else {
				die ($e->display(true));
			}
		}
	
																								
		# register object in registry
		Zend_Registry::set('erfurt', $this);
																								
		# check for new installation
		$this->_checkNewInstallation($config);
		
		/**
 		* load OntoWiki action access control configuration
 		*/
		#$owActionClass = $defaultStore->getModel($config->ac->action->class);
		#Zend_Registry::set('owActionClass', $owActionClass);
		
		#$owAcUserModel = $defaultStore->getModel($config->ac->user->model);
		
		// store config in session so that asynchronous requests
		// have access to modified config data (by e.g. OntoWiki)
		$session = new Zend_Session_Namespace('ERFURT');
		$session->config = clone $config;
		
		$this->ac = null;
		
		# set auth instance
		$this->auth = Zend_Auth::getInstance();
		
		# auth a special user
		if ($username != '') {
			$result = $this->authenticate($username, $password);
			if (!$result->isValid()) {
				throw new Erfurt_Exception('no valid user data given', 1205);
			}
		}
		
		# system configuration informations
		$this->sysontModel = $defaultStore->getModel($config->sysont->model);
		$defaultStore->SysOnt = $this->sysontModel;
		Zend_Registry::set('sysontModel', $this->sysontModel);
		
		# access control informations
		if ($config->sysont->model === $config->ac->model) {
			// avoid calling expensive getModel method if sysont = ac model
			$this->acModel = $this->sysontModel;
			
		} else {
			$this->acModel = $defaultStore->getModel($config->ac->model);
		}
		Zend_Registry::set('owAc', $this->acModel);
		
		
		# no current auth data? => set default user
		if (!$this->auth->hasIdentity()) {
			# authenticate anonymous
			$this->authenticate('Anonymous');
		}
		$identity = $this->auth->getIdentity();
		
		# action conf
		$defautlActionConf = include(ERFURT_BASE.'actions.ini.php');
		
		$this->ac = new Erfurt_Ac_Default($defautlActionConf);
		Zend_Registry::set('ac', $this->ac);
		
		# set ac to store
		$defaultStore->setAc($this->ac);
		
		# register object in registry
		Zend_Registry::set('erfurt', $this);
		
		# call init
		$this->init();
	}
	
	
	/**
	 * init-function 
	 * 
	 * init is called at the end of the initialisation process
	 */
	public function init() {
		// connect to store
		#ob_start();
		
		
		#$_ET['store']->dbConn->LogSQL(); // incompatible with $_POWL['db']['tablePrefix'] !!!
		#$_ET['store']->dbConn->debug=true;
		
		// initialise active model
		if(!empty($_REQUEST['model']))
			$_SESSION['_ETS']['model'] = $_REQUEST['model'];
		if(!empty($_SESSION['_ETS']['model']))
			$GLOBALS['_ET']['rdfsmodel'] = $this->getStore()->getModel($_SESSION['_ETS']['model']);
		#ob_end_clean();
		
		// load model specific sysont
		
		if(!empty($this->getStore()->SysOnt) && $modelClass = $this->getStore()->SysOnt->getClass('Model'))
			if($inst = $modelClass->findInstance(array('modelURI' => $GLOBALS['_ET']['rdfsmodel']['rdfsmodel']->modelURI)))
				if($sysont = $inst->getPropertyValuePlain('modelSysOntURI'))
					$GLOBALS['_POWL']['SysOnt'] = $this->getStore()->SysOnt;
					
		
	}
	
	/**
		 * check for new erfurt installation and sets the system ontologies
		 */
	private function _checkNewInstallation(Zend_Config $config) {
		/**
		 * check for ontowiki system ontologies
		 */
		if (!$this->store[$this->defaultStore]->modelExists($config->sysont->schema, false)) {
			$msg = 'Database Setup: Checking for Ontowiki SysOnt Schema ... no schema found.<br />';
			
			$m = $this->store[$this->defaultStore]->getNewModel($config->sysont->schema, '', 'RDFS', false);
			$m->dontCheckForDuplicatesOnAdd = true;
			$this->store[$this->defaultStore]->dbConn->StartTrans();
			$m->load(ERFURT_BASE . 'SysOnt.rdf', NULL, true);
			$this->store[$this->defaultStore]->dbConn->CompleteTrans();
			
			$msg .= 'Database Setup: Ontowiki SysOnt schema created.<br />';
			
			if ($this->throwExceptions) {
				header('Refresh: 3; url=' . $_SERVER['REQUEST_URI']);
				throw new Erfurt_Exception($msg . '<br />Refreshing in 3 seconds.');
			} else {
				echo $msg . '<br /><b>Please reload now.</b>';
			}
			
			exit();
		}
		if (!$this->store[$this->defaultStore]->modelExists($config->sysont->model, false)) {
			$msg = 'Database Setup: Checking for Ontowiki SysOnt model ... no model found.<br />';
			
			$m = $this->store[$this->defaultStore]->getNewModel($config->sysont->model, '', 'RDFS', false);
			$m->dontCheckForDuplicatesOnAdd = true;
			$this->store[$this->defaultStore]->dbConn->StartTrans();
			$m->load(ERFURT_BASE . 'SysOntLocal.rdf', NULL, true);
			$this->store[$this->defaultStore]->dbConn->CompleteTrans();
			
			$msg .= 'Database Setup: Ontowiki SysOnt model created<br />';
			
			if ($this->throwExceptions) {
				header('Refresh: 3; url=' . $_SERVER['REQUEST_URI']);
				throw new Erfurt_Exception($msg . '<br />Refreshing in 3 seconds.');
			} else {
				echo $msg . '<br /><b>Please reload now.</b>';
			}
			
			exit();
		}
	}
	
	
	/**
	 * authtenticate user
	 * 
	 * authenticate a user to the store
	 * 
	 * @trigger ExampleEvent ttt
	 */
	public function authenticate($username = '', $password = '') {
		
		// Set up the authentication adapter
		$this->authAdapter = new Erfurt_Auth_Adapter_RDF($this->acModel, $username, $password);
		
		// Attempt authentication, saving the result
		$result = $this->auth->authenticate($this->authAdapter);
		
		# set ac for new user
		if ($result->isValid() and $this->ac) {
			$this->ac->init();
			if (!$this->ac->isActionAllowed('Login')) {
				$this->auth->clearIdentity();
				$identity = array(
	               'username' => '',
	               'uri'	=>	'',
	               'dbuser'	=>	false,
	               'anonymous' => false
	               );
				$result = new Zend_Auth_Result(false, $identity, array('login for user not permitted'));
			}
		}
		
		return $result;
	}
	
	/**
	 * returns the event dispatcher
	 */
	public function getEventDispatcher() {
        return $this->ed;
	}
	
	/**
	 * returns the plugin manager
	 */
	public function getPluginManager() {
        return $this->pluginManager;
	}
	
	/**
	 * returns a direct or default store instance
	 */
	public function getStore($store = '') {
		if ($store == '' or !isset($this->store[$store]))
			return $this->store[$this->defaultStore];
		else
			return $this->store[$store];
	}
	
	/**
	 * returns ac instance
	 */
	public function getAc() {
		return $this->ac;
	}
	
	/**
	 * returns ac instance
	 */
	public function getAcModel() {
		return $this->acModel;
	}
	
/**
	 * returns the auth adapter instance
	 */
	public function getAuthAdapter() {
		if (null === $this->authAdapter)
			return $this->authAdapter = new Erfurt_Auth_Adapter_RDF($this->acModel, '', '');
		else
			return $this->authAdapter;
	}
	
}
?>
