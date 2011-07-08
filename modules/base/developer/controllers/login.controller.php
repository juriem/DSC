<?php
namespace modules\base\developer\controllers; 
use system\core\Controller; 

/**
 * 
 * Login controller for developer interface 
 * @author juriem
 *
 */
final class LoginController extends Controller {
	
	
	protected function _init(){
		$this->_useLocalTemplates = true;
	}
	
	protected function _default() {
		$this->addTemplate();
	}


	/**
	 *
	 * Login into developer area
	 * AJAX
	 */
	protected function login(){
		if ($this->_postData->isValid()) {
			$data = $this->_postData->getData();
			$password = $data['password'];
			if ($password == Config::getInstance()->security->password) {
				SessionHandler::__()->dev_loggedin = true;
				echo '{result:true}';
				return;
			}
		}
		echo '{result:false, error_msg:\'Bad password! Your IP was logged!\'}';
	}
	
	/**
	 * 
	 * Logout 
	 * AJAX.action
	 */
	protected function logout(){
		if (SessionHandler::__()->dev_loggedin !== null) {
			SessionHandler::__()->unsetVariables('dev_loggedin'); 
		}
		echo '{result:true}';
	}
}