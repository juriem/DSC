<?php

namespace modules\base\config\system;

//use modules\base\sessions\system\SessionHandler;

//use system\core\SystemClass;

//use system\base\Object;

/**
 * 
 * Configuration class 
 * @author Juri Em
 *
 */
final class Config extends \system\core\SystemClass {
	
	private $_config; 
	
	/**
	 * 
	 * Object constructor 
	 */
	protected function __construct() {
		parent::__construct();
		// Init configuration class 
		$this->_config = new \system\core\Config(ROOT.DS.'config'.DS.'config.ini');
	}
	
	/**
	 * 
	 * Get configuration value 
	 * @param string $configName - Configuration name in next format 
	 */
	public function __get($configName) {
		return $this->_config->$configName; 
	}
	
	
	/**
	 * 
	 * Set value for configuration 
	 * @param string $configName - Configuration name in next format section__configName
	 * @param mixed $value - Value for configuration
	 */
	public function __set($configName, $value) {
		$this->_config->$configName = $value;
	}
}