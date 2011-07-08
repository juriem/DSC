<?php
/**
 * 
 * Admin controller 
 * @author Juri Em
 *
 */
final class AdminController_Modules extends Controller {
	
	protected $_controllerName = 'admin';
	protected $_accessLevele = self::ACCESS_LEVEL_ADMIN; 
	protected $_useLocalTemplates = true; 
	
	protected function _default(){
	
	}
	
}