<?php
namespace modules\base\debug\controllers; 

use system\core\Controller;

final class DefaultController extends Controller {
	
	private function _init(){
		$this->_useLocalTemplates = true;
	}
	
	protected function _default(){
			
	}
	
	
	
}