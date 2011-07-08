<?php
final class TokensController_Languages extends Controller {
	
	protected $_useLocalTemplates = true; 
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 * Default actions - Interface for tokens 
	 */
	protected function _default(){
		$tokensModel = $this->_module->getModel('tokens');
		$this->modules = $tokensModel->selectModules();
		$this->addTemplate();
		$this->display();
	}
	
	/**
	 * 
	 * Load tokens 
	 * !!!AJAX
	 */
	protected function load_list(){		
		if (isset($_POST['filters'])) {
			
			$filters = $_POST['filters']; 
			$tokensModel = $this->_module->getModel('tokens');
			$this->tokens = $tokensModel->select($this->_languageId, $filters);
			$this->addTemplate();
			$this->display(); 
		}
	}
	
	/**
	 * 
	 * Get token value via ajax
	 * 
	 */
	protected function get_value(){
		if (isset($_POST['token_code'])) {
			echo Languages::getText($_POST['token_code']); 
		}
	}
	
}	