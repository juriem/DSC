<?php
namespace modules\base\sessions\system;

use modules\base\config\system\Config;

final class SessionHandler {
	
	protected static $_instance; 
	public static function &__() {
		if (!isset(self::$_instance)) self::$_instance = new self();
		return self::$_instance; 
	}
	
	/**
	 * 
	 * Encode session variable name 
	 * @param string $varName
	 */
	protected function encode($varName) {
		return md5(Config::__()->system__app_id.$varName);
	}
	
	/**
	 * 
	 * Get session variable
	 * @param  string $varName
	 */
	public function __get($varName) {
		$varName = $this->encode($varName);
		if (isset($_SESSION)) {
			if (key_exists($varName, $_SESSION)) return $_SESSION[$varName];
		}
		return null;
	}
	
	/**
	 * 
	 * Set SESSION variables 
	 * @param string $varName
	 * @param mixed $value
	 */
	public function __set($varName, $value) {
		
		$varName = $this->encode($varName);
		$_SESSION[$varName] = $value;
		sleep(1);
	}
	
	/**
	 * 
	 * Uset variables 
	 * @param list $varList
	 */
	public function unsetVariables($varList) {
		// Explode variables from list and clear them
		$list = explode(',', $varList); 
		$buffer = array(); 
		foreach($list as $item) {
			if (trim($item) != '') $buffer[] = $item; 
		}
		$list = $buffer;
		// Unset all variables 
		foreach($list as $variable) {
			$variable = $this->encode($variable); 
			unset($_SESSION[$variable]); 
		}
		sleep(1);
	}
	
}