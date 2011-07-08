<?php
namespace modules\base\developer\controllers; 
use system\core\Controller; 


/**
 * 
 * Tokens system controller
 * @author juriem
 *
 */
final class TokensController extends Controller {
	
	protected function _init(){
		$this->_useLocalTemplates = true;
		$this->_accessLevel = self::ACCESS_LEVEL_DEVELOPER;
	}
	
	protected function _default(){
		
	}
	
}