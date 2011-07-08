<?php
namespace system\core;
/**
 *
 * Model base class
 * @author Juri Em
 *
 */

use system\base\Router;
use modules\base\db\system\Db; // Database connector

abstract class Module{
		
	/**
	 *
	 * Module is system module. Old and will be remove in future.
	 * All system module resides in modules/system directory
	 * @var boolean
	 */
	protected $_systemModule = 'no'; 

	/**
	 *
	 * Allow direct access for module
	 * e.g. make calls in next form _modules/module_name/controller/task/params
	 * @var Boolean
	 */
	public $_allowDirectAccess = 'no';

	/**
	 *
	 * Path to module directory
	 * @var string
	 */
	public $_moduleRoot; //Root of module

	/**
	 *
	 * Name of module
	 * @var string
	 */
	public $_moduleName;

	/**
	 *
	 * Instance of internal module router
	 * @var object
	 */
	public $_moduleRouter;

	/**
	 *
	 * Module namespace
	 * @var string;
	 */
	private $_moduleNamespace;

	/**
	 *
	 * Class constructor
	 *
	 */
	public function __construct() {
		$foundIt = false;
		
		if (preg_match('/(.*)Module/i', get_class($this), $matches)) {
			$this->_moduleName = strtolower($matches[1]);

			//Get location
			$path = ROOT.DS.'modules'.DS.'base'.DS.$this->_moduleName;
			if (file_exists($path) && is_dir($path)) {
				$this->_moduleRoot = $path;
				$this->_moduleNamespace = 'modules\\base\\'.$this->_moduleName;
				$foundIt = true;
			} else {
				//Check user modules		 
				$path = ROOT.DS.'modules'.DS.$this->_moduleName;
				if (file_exists($path) && is_dir($path)) {
					$this->_moduleNamespace = 'modules\\'.$this->_moduleName;
					$this->_moduleRoot = $path;
					$foundIt = true;
				}
			}
		}
		
		if (!$foundIt) die('Module not found!');
		
		// Loding configuration 
		$configFile = $path.DS.'config'.DS.'config.ini'; 
		if (file_exists($configFile)) {
			$config = new \system\core\Config($configFile); 
			//TODO:: It can be extended
			$this->_systemModule = $config->common__systemModule; 
			$this->_allowDirectAccess = $config->common__allowDirectAccess; 
		}
	}


	/**
	 *
	 * Module root
	 */
	public function getRoot() {
		return $this->_moduleRoot;
	}

	/**
	 *
	 * Get module name
	 */
	public function getName() {
		return $this->_moduleName;
	}

	/**
	 *
	 * Get module router
	 */
	public function &getRouter() {
		return $this->_moduleRouter;
	}

	/**
	 *
	 * Get module id
	 */
	public function getModuleId() {
		$sql = "Select id From #modules Where module_name = '{$this->_moduleName}'";
		$rs = Db::__()->getRow($sql);
		if ($rs) {
			return $rs->id;
		}
		return null;
	}

	/**
	 *
	 * Get settings for module
	 * @param Int $languageId - Language id
	 */
	public function getSettings($languageId) {
		$settings = new Object();
		$moduleId = $this->getModuleId(); //Get module id
			
		$sql = "Select * From #settings
					Left Outer Join #setting_values 
						On #settings.id = #setting_values.item_id And #setting_values.language_id = $languageId 
				Where #settings.module_id = $moduleId";
		$rs = Db::__()->getRows($sql);
		if ($rs) {
			foreach($rs as $setting) {
				$key = $setting->code;
				if (in_array($setting->type, array('string','text'))) {
					$settings->$key = $setting->value;
				} else {
					$settings->$key = $setting->single_value;
				}
			}
		}
		return $settings;
	}

	/**
	 *
	 * Execute module
	 * @param String $url
	 */
	public function execute($url='', $returnContent=false){
		$content = '';
		$this->_moduleRouter = new Router($url, 'controller/task/params');
		//Get controller for module
		$controllerName = $this->_moduleRouter->controller;

		if ($controllerName == null) $controllerName = 'default';
		$controller =& $this->getController($controllerName);
			
		if ($controller != null) {
				
			//Get task for module
			$task = $this->_moduleRouter->task;
			if ($task == null) $task = '_default';
				
			if ($returnContent) ob_start();
			$controller->execute($task);
			if ($returnContent) $content = ob_get_clean();
				
			unset($controller);
		} else {
			Logger::register("{$this->_moduleName}::execute(\$url = '$url') - Controller '$controllerName' not found!", Logger::ERROR_MESSAGE);
		}
		if ($returnContent) return $content;
	}

	/**
	 *
	 * Get module controller
	 * @param String $controllerName
	 * @return - Instance of controller or NULL if doesnt exist
	 */
	public function &getController($controllerName='default'){

		$controllerInstance = null;
		$controllerPath = $this->_moduleRoot.DS.'controllers'.DS.strtolower($controllerName).'.controller.php';
		
		if (file_exists($controllerPath)) {	
			$controllerClass = $this->_moduleNamespace.'\\controllers\\'.$controllerName.'Controller';
			if (!class_exists($controllerClass)) require ($controllerPath);
			if (class_exists($controllerClass)) {
				$controllerInstance = new $controllerClass($this);
			}
		} else {
			die('Controller not found!'); 
		}
		return $controllerInstance;
	}

	/**
	 *
	 * Get module's model by name
	 * @param String $modelName
	 * @return Instance of model or NULL if doesnt exist
	 */
	public function &getModel($modelName) {
		$modelInstance = null;
		$modelPath = $this->_moduleRoot.DS.'models'.DS.strtolower($modelName).'.model.php';
		
		if (file_exists($modelPath)) {
			
			$modelClass = $this->_moduleNamespace.'\\models\\'.$modelName.'Model';
			
			if (!class_exists($modelClass)) require($modelPath);
			if (class_exists($modelClass)) $modelInstance = new $modelClass();
		} else {
			die('Model \''.$modelName.'\' sources not found!'); 
		}
		
		return $modelInstance;
	}

}