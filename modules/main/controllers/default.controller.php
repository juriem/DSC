<?php
namespace modules\main\controllers;
use system\core\Controller; 

final class DefaultController extends Controller {
	
	protected function _default(){
		$this->addTemplate('header',self::TEMPLATE_TYPE_GLOBAL);
		$this->addTemplate();
	}
}