<?php
namespace modules\admin\controllers;

use modules\base\modules\system\Modules;

use system\base\Router;

use modules\base\sessions\system\SessionHandler;

use system\core\Controller;

final class DefaultController extends Controller {
	
	
	protected function _init(){
		$this->_useLocalTemplates = true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see system\core.Controller::_default()
	 * Default task
	 */
	protected function _default(){
		
		if (SessionHandler::__()->admin_logged == null) {
			// No system admin logged in
			$this->content = $this->_module->execute('login/_default');
		} else {
			// Processing parameters
			$router = new Router($this->_router->getURL(1), 'module/params');	
			// Checking module
			if ($router->module == null) {
				// Load default
				
			} else {
				// Checking for module 
				
				
			}
		}
		
		
		$this->addTemplate('header',self::TEMPLATE_TYPE_EXTERNAL);
		$this->addTemplate();
		$this->addTemplate('footer',self::TEMPLATE_TYPE_EXTERNAL);
	}
}