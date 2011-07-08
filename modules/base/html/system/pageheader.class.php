<?php
namespace modules\base\html\system;

use modules\base\languages\system\Languages;

use system\base\Object;

use system\core\SystemClass;

final class PageHeader extends SystemClass{
	
	protected static $_instance; 
	public static function &__(){
		if (!isset(self::$_instance)) self::$_instance = new self();
		return self::$_instance; 
	}
	
	
	private $_holder; 
	
	protected function __construct() {
		parent::__construct(); 
		
		$this->register('content_type', '<meta http-equiv="Content-Type" content="__VALUE__">');
		$this->register('title', '<title>__VALUE__</title>');
		$this->register('description', '<meta name="description" content="__VALUE__">');
		$this->register('keywords','<meta name="keywords" content="__VALUE__">');
		$this->register('style','<link rel="stylesheet" href="__BASE_URL__css/style.css">'); 
		
		$this->content_type = 'text/html; charset=utf-8'; 
	}
	
	
	private $_headers; 
	
	/**
	 * 
	 * Register header
	 * @param string $varName - 
	 * @param unknown_type $template
	 */
	public function register($varName, $template) {
		if (!isset($this->_headers)) $this->_headers = array();
		$part = new Object(array('name'=>$varName,'template'=>$template)); 
		$this->_headers[] = $part;
	}
	
	/**
	 * 
	 * Add custom header 
	 * @param string $header
	 */
	public function addHeader($header){
		$this->register('', $header); 
	}
	
	/**
	 * 
	 * Render header 
	 */
	public function render($content) {
		$html = '<!DOCTYPE html>'.NL; // 
		$html .= '<html lang="'.Languages::__()->getCurrent().'">'.NL;  // Add language
		$html .= '<head>'.NL; 
		
		foreach($this->_headers as $template) {
			$key = $template->name;
			$html .= str_replace('__VALUE__', $this->$key, $template->template).NL;
		}
		
		$html .= '<meta name="generator" content="DCS framework - http://www.gizlab.eu">'.NL;
		$html .= '<meta name="author" content="GiZystems">'.NL; 
		
		$html .= '</head>'.NL; 
		$html .= '<body>'.NL. $content . '</body></html>'; 
		return $html; 
	}
	
	/**
	 * 
	 * Set value for header part 
	 * @param string $varName
	 * @param string $varValue
	 */
	public function __set($varName, $varValue) {
		if (!isset($this->_holder)) $this->_holder = array();
		$this->_holder[$varName] = $varValue;	
	}
	
	/**
	 * 
	 * Get value for header part 
	 * @param string $varName
	 */
	public function __get($varName) {
		if (isset($this->_holder)) {
			if (key_exists($varName, $this->_holder)) {
				return $this->_holder[$varName]; 
			}
		}
		return null;
	}
	
	
	
	
}