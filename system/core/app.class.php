<?php
namespace system\core;
use system\tools\Arrays;

use system\base\Router;
use system\base\Object;
/**
 *
 * Application class
 * Makes loading of modules and models
 * @author Juri Em
 *
 */
final class App {


	/*
	 * Template type
	 */
	/**
	 *
	 * Default template for controller. Location of templates depends of type of controller.
	 * If controller has _useLocalTemplate value TRUE then used local module templates directpry.
	 * In other cases uses global templates directory
	 * @var string
	 */
	const TEMPLATE_TYPE_DEFAULT = 'default';
	/**
	 *
	 * External template.
	 * @var string
	 */
	const TEMPLATE_TYPE_EXTERNAL = 'external'; // External template
	/**
	 *
	 * Global templates. Always get templates from global template directory
	 * @var string
	 */
	const TEMPLATE_TYPE_GLOBAL = 'global'; // Global template
	/**
	 *
	 * Local templates. Use controller's templates directory
	 * @var string
	 */
	const TEMPLATE_TYPE_LOCAL = 'local'; // Local template

	/**
	 *
	 * Modules cache
	 * @var array
	 */
	protected static $_modulesCache;
	
	/**
	 * 
	 * Configuration for application 
	 * @var Object 
	 */
	protected static $_configuration; 
	
	
	/**
	 *
	 * Global system router
	 * @var Router
	 */
	protected static $_router;
	
	/**
	 *
	 * Get global system router
	 */
	public static function getRouter() {
		if (!isset(self::$_router)) {
			$url = '';
			if (isset($_GET['url'])) $url = $_GET['url'];
			self::$_router = new Router($url);
		}
		return self::$_router;
	}

	/**
	 *
	 * Direct execute module
	 * @param String $moduleName - Name of module
	 * @param String $url - Url for module
	 * @param Boolean $return - Flag for return content of module, If not module will render output
	 */
	public static function executeModule($moduleName, $url, $return = false){
		$moduleInstance =& self::getModule($moduleName);
		if ($moduleInstance != null) {
			if ($return) {
				return $moduleInstance->execute($url, $return);
			} else {
				$moduleInstance->execute($url, $return);
			}
		} else {
			Logger::register("App::executeModule(moduleName = '$moduleName', \$url = $url)", Logger::ERROR_MESSAGE);
		}
	}

	/**
	 *
	 * Get module instance
	 * @param String $moduleName - Name of module
	 */
	public static function &getModule($moduleName){
		$moduleName = strtolower($moduleName);
		$moduleInstance = null;

		//Check cache
		if (!isset(self::$_modulesCache)) self::$_modulesCache = array();
		foreach(self::$_modulesCache as $module) {
			if ($module->name == $moduleName) {
				$moduleInstance =  $module->instance;
				return $moduleInstance;
			}
		}

		//Search in systemn modules
		$foundModule = false;

		$dirname = ROOT.DS.'modules'.DS.'base'.DS.$moduleName;
		$namespace = 'modules';
		if (file_exists($dirname) && is_dir($dirname)) {
			$foundModule = true;
			$namespace .= '\\base';
		} else {
			// Processing system modules 
			$dirname = ROOT.DS.'modules'.DS.$moduleName;
			if (file_exists($dirname) && is_dir($dirname)) {
				$foundModule = true;
			}
		}

		if ($foundModule) {
			$moduleClass = $moduleName.'Module';
			// Checking if class exists	
			if (!class_exists($moduleClass)) {
				// Create class
				eval('final class '.$moduleClass.' extends \\system\\core\\Module {}');
			}
			$moduleInstance = new $moduleClass();
			
				
		} else {
			Logger::register("App::getModule(\$moduleName = '$moduleName') - not found!", Logger::SYSTEM_MESSAGE);
		}

		$module = new Object(array('name'=>$moduleName,'instance'=>$moduleInstance));
		//$module->name = $moduleName ;
		//$module->instance = $moduleInstance;
		self::$_modulesCache[] = $module;
		return $moduleInstance;
	}


	/**
	 *
	 * Get template
	 * @param String $template - Name of template
	 */
	public static function getTemplate($template) {
		$templatePath = ROOT.DS.'templates'.DS.str_replace('.','DS',$template).'.php';
		if (file_exists($templatePath)) {
			return $templatePath;
		}
		return ROOT.DS.'templates'.DS.'dummy.php';
	}


	/**
	 *
	 * Get module templates
	 * @param String $moduleName - Name of module
	 * @param String $template - Name of template
	 */
	public static function getModuleTemplate($moduleName, $template) {
		//Search for module
		$helper = new Helper();
		$filePath = ROOT.'.modules.mod_'.strtolower($moduleName).'.templates.'.strtolower($template);
		$filePath = $helper->buildPath($filePath).'.php';
		if (file_exists($filePath)) {
			return $filePath;
		}
		//Return dummy template
		return ROOT.DS.'templates'.DS.'dummy.php';

	}

	/**
	 *
	 * Get model
	 * @param String $moduleName - Name of module
	 * @param String $modelName - Name of model
	 */
	public static function &getModel($moduleName, $modelName='') {
		$modelInstance = null;

		$data = Arrays::__()->explodeToObject($moduleName, 'module,model');
		$module =& self::getModule($data->module);

		if ($module != null) {
			$modelInstance =& $module->getModel($data->model);
				
		}
		return $modelInstance;
	}




	/**
	 *
	 * Display content
	 */
	public static function display($contents){

		if( headers_sent() ){
			$encoding = false;
		}elseif( strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false ){
			$encoding = 'x-gzip';
		}elseif( strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip') !== false ){
			$encoding = 'gzip';
		}else{
			$encoding = false;
		}

		if( $encoding ){
			header('Content-Encoding: '.$encoding);
			print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
			$size = strlen($contents);
			$contents = gzcompress($contents, 9);
			$contents = substr($contents, 0, $size);
			echo $contents;
			exit();
		}else{
			echo $contents;
			exit();
		}

	}
}