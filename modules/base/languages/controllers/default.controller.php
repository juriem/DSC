<?php
namespace modules\base\languages\controlles;

use system\core\Controller;

/**
 * 
 * Default controller for languages 
 * @author Juri Em
 *
 */
final class DefaultController extends Controller {

	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 * Default action  - show languages 
	 */
	protected function _default() {
		
		//Languages model 
		$languagesModel = $this->getModel('languages');
		
		$this->data = $languagesModel->getValue($this->_languageId, $this->_languageId); 
		
		// Get languages
		$this->languages = $languagesModel->select($this->_languageId); 
		
		$this->addTemplate(); 
		$this->display(true);
	}
	
	/**
	 * 
	 * Show all languages 
	 */
	protected function show_all(){
		//Languages model 
		$languagesModel = $this->getModel('languages');
		$this->data = $languagesModel->getValue($this->_languageId, $this->_languageId); 
		
		// Get languages
		$this->languages = $languagesModel->selectAll($this->_languageId); 
		
		$this->addTemplate('_default'); 
		$this->display(true);
	}
	
}