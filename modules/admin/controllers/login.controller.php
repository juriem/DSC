<?php
namespace modules\admin\controllers; 

use system\core\Controller;

final class LoginController extends Controller {
	
	protected function _init(){
		$this->_useLocalTemplates = true;
	}
	
	protected function _default(){
		$this->addTemplate();
	}
	
	protected function login(){
		
	}
	
	protected function logout(){
		
	}
	
}