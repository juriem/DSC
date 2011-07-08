<?php
namespace modules\base\developer\controllers; 
use system\core\Controller;
/**
 * 
 * Controller for developer 
 * @author Juri Em
 *
 */
final class DefaultController extends Controller {
	
	protected function _init(){
		$this->_useLocalTemplates = true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 * Only one task for this controller 
	 */
	protected function _default() {
		
		$this->addTemplate('header', TEMPLATE_TYPE_EXTERNAL);
		
		if (SessionHandler::__()->dev_loggedin == null) {
			$this->content = $this->_module->execute('login/_default', true); // Execute login controller
		} else {
			
			$router = new RouterBase($this->_router->getURL(1), 'controller/params');
			
			if ($router->controller !== null) {
				$url = $router->controller . $router->getURL(1);
				$this->content = $this->_module->execute($url, true); 
			}
		}
		$this->addTemplate();
		//Render output
		$this->addTemplate('footer', TEMPLATE_TYPE_EXTERNAL);
		$this->display(); 
	}
	
	
	
	
	
	/**
	 * 
	 * Close deveoper area 
	 */
	protected function logout(){
		SessionHandler::__()->unsetVariables('dev_loggedin');
		//session_write_close();  
		echo '{result:true}';
	}
}