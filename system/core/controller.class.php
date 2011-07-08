<?php
namespace system\core;
use modules\base\sessions\system\SessionHandler;
use modules\base\languages\system\Languages;
use modules\base\ajax\system\Post;
/**
 * 
 * Base class for controller
 * @author Juri Em
 *
 */ 
abstract class Controller {
	
	
	const TEMPLATE_TYPE_DEFAULT = 'default'; 
	const TEMPLATE_TYPE_LOCAL = 'local'; 
	const TEMPLATE_TYPE_EXTERNAL = 'external'; 
	const TEMPLATE_TYPE_GLOBAL = 'global';
	
	/**
	 * 
	 * Name of controller 
	 * @var String
	 */
	protected $_controllerName;
	
	/**
	 * 
	 * Current controller task
	 * @var String
	 */
	protected $_controllerTask;

	/**
	 * 
	 * Default model name 
	 * @var string
	 */
	protected $_defaultModelName; 
	
	/**
	 * 
	 * Flag for using local templates
	 * If flag is set then use templates from module folder
	 * @var Boolean
	 */
	protected $_useLocalTemplates = false;
	
	/**
	 * 
	 * List of taks which tells controller to put controller name in global url 
	 * By default its empty. Must specify in final controller 
	 * @var array
	 */
	protected $_urlTasks = array(); 
	
	/**
	 * 
	 * Post data 
	 * @var Object
	 */
	protected $_postData; 
	
	/**
	 * 
	 * Session Handler
	 * @var object
	 */
	protected $_sessionHandler; 
	
	/**
	 * 
	 * Access level for controller 
	 * By default uses ACCESS_LEVEL_ALL
	 * @var String
	 */
	protected $_accessLevel = self::ACCESS_LEVEL_ALL;

	
	const ACCESS_LEVEL_ALL = 'all'; 
	const ACCESS_LEVEL_ADMIN = 'administrator'; 
	const ACCESS_LEVEL_DEVELOPER = 'developer'; 
	const ACCESS_LEVEL_USER = 'user'; 
	
	/**
	 * 
	 * Enter description here ...
	 * @var string
	 */
	protected $_authCheckCondition = ''; 
	
	/**
	 * 
	 * Link for redirect  
	 * @var string 
	 */
	protected $_authRedirectLink = ''; 
	

	//Module and router where controller resides
	/**
	 * 
	 * Instance of module which called controller 
	 * @var Object
	 */
	protected $_module;
	/**
	 * 
	 * Instance of router  
	 * @var Object
	 */
	protected $_router;

	
	/**
	 * 
	 * Array of templates used in controller 
	 * @var Array
	 */
	private $_templates;
	
	/**
	 * 
	 * Array of variables for templates 
	 * @var Array
	 */
	private $_variables;
	
	/**
	 * 
	 * Constructor 
	 * Controller can be created without module 
	 * @param String $module 
	 */
	public function __construct(&$module) {
		
		// Get session handler
		$this->_sessionHandler =& SessionHandler::__(); 
		
		//Get controller name. Controller name is a last part of array 
		// 
		$parts = explode('\\',get_class($this)); 
		$this->_controllerName = strtolower(str_replace('Controller', '', array_pop($parts)));  
		
		
		//Processing module  
		if ($module != null) {
			$this->_module =& $module;
			$this->_router =& $module->getRouter(); 
		}
		//Assign current language 
		$this->_languageId = Languages::__()->getCurrentId(); //Languages::getCurrentLanguageId();
		// Call initialize function 
		$this->_init();
		/*
		 * Convert string format for url tasks to array 
		 */
		if (!is_array($this->_urlTasks)){
			$this->_urlTasks = explode(',', $this->_urlTasks);
			$buffer = array(); 
			//Remove spaces
			foreach($this->_urlTasks as $tmp) {
				$buffer[] = trim($tmp); 
			}
			$this->_urlTasks = $buffer; 
		}
	}
	
	/**
	 * 
	 * Class destructor 
	 */
	public function __destruct() {
		//if (isset($this->_templates)) $this->display(); 
	}
	
	
	/**
	 * 
	 * Additional initialization for controller. 
	 * This method used for init addition controller parameters. Controller always creates from module. 
	 * Defines in final class for controller  
	 */
	protected function _init(){ /* Implement this function in final controller */ }
	
	/**
	 * 
	 * Get model
	 * Model must be defined in parent module  
	 * @param String $modelName - Name of model
	 */
	protected function &getModel($modelName='') {
		if ($modelName == '') $modelName = $this->_defaultModelName; 
		return $this->_module->getModel($modelName); 
	}
	
	/**
	 *
	 * Display content for templates
	 */
	protected function display(){
		//Extract variables 
		if (isset($this->_variables)) extract($this->_variables);
		//Processing templates 
		if (isset($this->_templates)) {
			
		
			foreach($this->_templates as $template) {
				
				if (file_exists($template.'.php')) {
					include($template.'.php');
				} else {
					?>
<div style="font-family:monospace; font-size: 9px; color: red; padding:10px; margin:2px; border:1px solid red;">
	Template <strong style="font-size:11px; color:blue; "><?php echo $template; ?></strong> not found
</div>
					<?php
				}
			}
		}
		//Unset all variables and templates
		unset($this->_variables, $this->_templates); 
		
	}
	
	/**
	 * 
	 * Check if tamplate exists
	 * @param String $templateName - Name of template 
	 * @param String $templateType - Type of template 
	 */
	public function checkTemplate($templateName='', $templateType = self::TEMPLATE_TYPE_DEFAULT) {
		$templatePath = $this->getTemplatePath($templateName, $templateType) . '.php';
		if (file_exists($templatePath)) return true; 
		return false; 
	}
	
	/**
	 *
	 * Add template to controller
	 * @param String $templatePath - Path to template. Path base on main template directory and separate by '.'. Template name without extension.
	 */
	public function addTemplate($templateName='', $templateType = self::TEMPLATE_TYPE_DEFAULT) {
		
		$templatePath = $this->getTemplatePath($templateName, $templateType); 
		
		if (!isset($this->_templates)) $this->_templates = array();
		$this->_templates[] = $templatePath;
	}
	
	/**
	 * 
	 * Help function for get path ot template 
	 * @param String $templateName
	 * @param String $templateType
	 */
	private function getTemplatePath($templateName='', $templateType = self::TEMPLATE_TYPE_DEFAULT) {
		
		$templateName = str_replace('.', DS, $templateName); 
		switch($templateType) {
			case self::TEMPLATE_TYPE_EXTERNAL:
				
				if ($this->_useLocalTemplates) {
					$templatePath = $this->_module->_moduleRoot.DS.'templates'.DS.$templateName;   
				} else {
					$templatePath = ROOT.DS.'templates'.DS.$this->_module->_moduleName.DS.$templateName; 
				}
				break; 
			case self::TEMPLATE_TYPE_GLOBAL:
				//Global type always gets from global templates 
				$templatePath = ROOT.DS.'templates'.DS.$templateName;
				break; 
			default:
				//In default case template name treats as name for template from controller templates directory 
				if ($templateName == '') {
					$templateName = $this->_controllerName.DS.$this->_controllerTask; 
				} else {
					$templateName = $this->_controllerName.DS.$templateName;
				}
				if ($this->_useLocalTemplates) {
					$templatePath = $this->_module->_moduleRoot.DS.'templates'.DS.$templateName;   
				} else {
					$templatePath = ROOT.DS.'templates'.DS.$this->_module->_moduleName.DS.$templateName; 
				}
		}
		
		return $templatePath; 
	}
	
	
	/**
	 *
	 * Set variable for template
	 * @param String $varName - Variable name
	 * @param Mized $varValue - Variable value
	 */
	public function __set($varName, $varValue){
		if (!isset($this->_variables)) $this->_variables = array();
		$this->_variables[$varName] = $varValue;
	}

	/**
	 *
	 * Get variable name
	 * @param String $varName
	 */
	public function __get($varName) {
		if (isset($this->_variables)) {
			if (key_exists($varName, $this->_variables)) {
				return $this->_variables[$varName];
			}
		}
		return null;
	}
	
	/**
	 *
	 * Execute controller protected task
	 * @param unknown_type $task
	 */
	public function execute($task = '') {
		 
		
		
		$this->_postData =& Post::__(); 
		
		if (isset($this->_accessLevel)) {
			
			//Check if admin logged in
			if ($this->_accessLevel == self::ACCESS_LEVEL_ADMIN) {
				if (SessionHandler::__()->admin_loggedin == null) {
					if (isset($_POST['module'])) {
						echo '{result:false,session:true,redirect:"'.URL::getBaseURL().'/_admin"}'; 
					} else {
						header('Location:'.URL::getBaseURL());
					}
					return;
				}
			}
			//Check if user logged in
			if ($this->_accessLevel == self::ACCESS_LEVEL_USER) {
				if (SessionHandler::__()->user_loggedin == null) {
					if (isset($_POST['module'])) {
						echo '{result:false,session:true, redirect:"'.URL::getBaseURL().'/"}'; 
					} else {
						header('Location:'.URL::getBaseURL());
					}
					return;
				}
			}
			//Check if developer logged in 
			if ($this->_accessLevel == self::ACCESS_LEVEL_DEVELOPER) {
				
				if (SessionHandler::__()->dev_loggedin == null) {
					
					if (isset($_POST['module'])) {
						echo '{result:false,session:true, redirect:"'.URL::getBaseURL().'/_developer"}';
					} else {
						echo URL::getBaseURL();
						header('Location:'.URL::getBaseURL());
					}
					return;
				}
			}
		}
		
		
		
		$this->_controllerTask = null; 
		// Processing normal task 
		if ($task != '') {
			// If used authtntification check then check it 
			if ($this->_authCheckCondition !== '') {
				$varName = $this->_authCheckCondition; 
				if (method_exists($this, $task.'__auth')) {
					echo 'Ok';
					if (SessionHandler::__()->$varName !== true) {
						if (isset($_POST['module'])) {
							echo '{result:false, session:true, redirect:"'.$this->_authRedirectLink.'"}'; 
						} else {
							header('Location:'.$this->_authRedirectLink); 
						}
						exit; 
					}
					$this->_controllerTask = $task . '__auth';
				} 
					
			}
			
			
			
		} 
		
		if ($this->_controllerTask == null) {
			if (method_exists($this, $task)) $this->_controllerTask = $task;
		}
		
		if ($this->_controllerTask === null) $this->_controllerTask = '_default';
		
		
		
		/*
		 * Execute controller 
		 */
		$task = $this->_controllerTask; 		
		$this->$task();
		// Auto display if not already displayed
		if (isset($this->_templates)) $this->display();
	}
	
	/**
	 *
	 * Special abstact method. Must be implemented in child class
	 */
	protected function _default() { /**/  }

	/**
	 * 
	 * Common add function 
	 */
	protected function add(){
		$this->_newMode = true; 
		$this->execute('edit'); 
	}
	
}