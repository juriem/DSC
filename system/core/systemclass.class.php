<?php
namespace system\core;

/**
 * 
 * Base for system classes 
 * System classes used in modules 
 * 
 * @author juriem
 *
 */
use system\base\Object;

abstract class SystemClass {
	
	
	protected static $_instances = array();
	public static function &__(){
		$className = get_called_class(); 
		if (!isset(self::$_instances[$className])) {
			self::$_instances[$className] = new $className();
		}
		return self::$_instances[$className];
	} 
	
	/**
	 * 
	 * Instance of module for system class 
	 * @var modules\base\modules\system\Module
	 */
	protected $_moduleInstance; 
	
	/**
	 * 
	 * Проверка кэша для моделей. Все вызываемые модели хранятся в кэше. Если вызываемая модель не существует в кэше,
	 * то она добавляется.
	 * @var array
	 */
	protected $_modelsCache; 
	
	/**
	 * 
	 * Contstructor 
	 * @param string $moduleName - Name of module
	 */
	protected function __construct() {
		
		
		// Processing name of class and get location of class 
		$className = get_class($this);
		$parts = explode('\\', $className);
		// index = 0 - modules 
		// check for second 
		if ($parts[1] == 'base') {
			// Look for next 
			$moduleName = $parts[2]; 
		} else {
			$moduleName = $parts[1]; 
		}
		if (!isset($this->_moduleInstance)) $this->_moduleInstance =& App::getModule($moduleName);
	}
	
	/**
	 * 
	 * Get model from module namespace
	 * @param string $modelName
	 */
	protected function &_getModel($modelName) {
		
		// Checking cache
		if (isset($this->_modelsCache)) {
			foreach($this->_modelsCache as $model) {
				if ($model->name == $modelName) {
					$instance = $model->instance;
					return $instance;
				}
			}
		}
		
		// Store model in cache
		$model =& $this->_moduleInstance->getModel($modelName);
		if (!isset($this->_modelsCache)) $this->_modelsCache = array();
		$_model = new Object(array('name'=>$modelName,'instance'=>$model));
		$this->_modelsCache[] = $_model; 
		return $model;
	}
	
}