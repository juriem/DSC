<?php
namespace modules\base\settings\system;

use system\base\Object;

use system\core\SystemClass;
use system\core\App;
use modules\base\languages\system\Languages; 

/**
 *
 * Settings class
 * @author Juri Em
 *
 */
final class Settings extends SystemClass {
	
	private $_cache;
	
	/**
	 *
	 * Get all settings
	 * @param String $moduleName
	 */
	public function getAll($moduleName='global') {

		if ($moduleName != 'global') {
			//Get module id
			$model =& App::getModel('modules.modules');
			$moduleId = $model->getId($moduleName);
			if ($moduleId === false) return false;
		} else {
			$moduleId = null;
		}

		//Get values
		$languageId = Languages::__()->getCurrentId();
		$model = $this->_getModel('settings');
		$settings = $model->selectValues($moduleId, $languageId);
		unset($model);
		$result = false;
		if ($settings) {
			$result = new Object();
			foreach($settings as $setting) {
				$code = $setting->code;
				if (in_array($setting->type, array('text','string'))) {
					$result->$code = $setting->value;
				} else {
					$result->$code = $setting->single_value;
				}
			}
		}
		return $result;
	}

	/**
	 *
	 * Get setting's value
	 * @param String $settingName - Setting's code. If need get module settings then user <module_name>.<setting_code>
	 */
	public function get($settingName){
		$settingName = strtolower($settingName);

		//Get language ID
		$languageId = Languages::__()->getCurrentId();
		
		//Check cache
		if (!isset($this->_cache)) $this->_cache = array();
		foreach($this->_cache as $_setting) {
			if ($_setting->code == $settingName && $_setting->language_id == $languageId) return $_setting->value;
		}

		//Processing setting's name
		$tmp = explode('.', $settingName);

		if (count($tmp) == 1) {
			// Processing global setting
			$settingCode = $tmp[0];
			$moduleId = null;
		} else {
			// Processing module's setting
			$settingCode = $tmp[1]; 
			$moduleName = $tmp[0];
			// Search for module
			$model = App::getModel('modules.modules'); 
			// 
			$moduleId = $model->getId($moduleName);	 
			if ($moduleId == null) return null;
		}
		
		// Get settings model 
		$model = $this->_getModel('settings');
		$value = $model->getValue($moduleId, $settingCode, $languageId);
		
		if ($value) {
			$_setting = new Object(array('code'=>$settingName,'language_id'=>$languageId)); 
			if ($value->type == 'string' || $value->type == 'text') {
				$_setting->value = $value->value;
			} else {
				$_setting->value = $value->single_value;
			} 	
			$this->_cache[] = $_setting; 
			return $_setting->value; // Return value for setting
		}
		return null; // Always return null
	}


}

