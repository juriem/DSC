<?php
/**
 * 
 * Extension for settings controller 
 * @author Juri Em
 *
 */
abstract class SettingsController extends Controller {
	protected $_accessLevel = self::ACCESS_LEVEL_ADMIN; 
	
	
	protected function _default(){
		$id = $this->_module->getModuleId();
		App::executeModule('settings', 'settings/_default/'.$id);
	}
}