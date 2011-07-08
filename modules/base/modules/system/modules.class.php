<?php
namespace modules\base\modules\system;

use system\core\SystemClass;

final class Modules extends SystemClass {
	
	/**
	 * 
	 * Get module id 
	 * @param string $module - System name of module 
	 */
	public function getId($module) {
		$model = $this->_getModel('modules'); 
		return $model->getId($module);	
	}
	
	/**
	 * 
	 * Checking if module has admin interface 
	 * @param string $moduleName
	 */
	public function hasAdminUI($moduleName) {
		
		$path = ROOT.DS.'modules'.DS.'base'.$moduleName.DS.'controlles'.DS.'admin';
		$foundIt = false; 
		if (file_exists($path) && is_dir($path)) $foundIt = true;
		if (!$foundIt) {
			$path = ROOT.DS.'modules'.DS.$moduleName.DS.'controllers'.DS.'admin';
			if (file_exists($path) && is_dir($path)) $foundIt = true;
		}
		
		return $foundIt;
	}
	
}