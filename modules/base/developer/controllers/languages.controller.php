<?php
namespace modules\base\developer\controllers; 
use system\core\Controller;
/**
 * 
 * Controller for languages 
 * All tasks related to adding new language into system
 * Settings related to how to use languages in system 
 * 
 * @author Juri Em
 *
 */
final class LanguagesController extends Controller {
	
	
	protected function _init(){
		$this->_useLocalTemplates = true;
		$this->_accessLevel = Controller::ACCESS_LEVEL_DEVELOPER;
	}
	
	protected function _default() {
		
		$languagesModel = $this->_module->getModel('languages'); 
		$this->languages = $languagesModel->getLanguages(); 
		$this->addTemplate(); 
		$this->display(); 
	}
	
	/**
	 * 
	 * Add new language
	 */
	protected function add(){
		$this->_newMode = true; 
		$this->execute('edit'); 
	}
	
	/**
	 * 
	 * Edit language
	 */
	protected function edit() {
		
		
		
		if ($this->_newMode) {
			$id = 0; 
		} else {
			$id = $this->_router->params[0]; 
		}
		$languagesModel = $this->getModel('languages');
		$this->languages = $languagesModel->getLanguages();
		$data = $languagesModel->get($id); 
		if (!$data) {
			$data = new Object();
			$data->id = 0;  
		} 
		$this->data = $data; 
		$this->addTemplate(); 
		$this->display(); 
	}
	
}