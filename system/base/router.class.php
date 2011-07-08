<?php
namespace system\base;
/**
 * 
 * Router base class 
 * @author Juri Em
 *
 */
class Router {
	
	protected $_url; 
	
	protected $_holder; 
	public function __construct($url, $urlPattern='', $delimiter='/'){
		
		
		
		//Store url 
		$this->_url = $url; 
		//Parse url 
		$urlParts = explode('/', $url);
		//Cleanup url parts array
		$buffer = array(); 
		foreach($urlParts as $urlPart) {
			if ($urlPart != '') {
				$buffer[] = $urlPart; 
			}
		}
		$urlParts = $buffer; 
		unset($buffer);
		
		//processing url patterns 
		if ($urlPattern != '') {
			$buffer = $urlParts;
			$parts = explode('/',$urlPattern);
			//Cleanup 
			$partsBuffer = array(); 
			foreach($parts as $part) {
				if (!empty($part)) $partsBuffer[] = $part; 
			}
			$parts = $partsBuffer; unset($partsBuffer);
			$cnt = count($parts); 
			for($ix=0; $ix < $cnt; $ix++) {
				$part = $parts[$ix];
				 
				if (count($buffer) != 0) {
					if ($ix+1 == $cnt) {
						//Last part 
						$this->$part = $buffer; 
					} else {
						$this->$part = $buffer[0]; 
						array_shift($buffer); 
					}
				}
			}
		}
		
		
		//Processing url array 
		$cnt = count($urlParts); 
		for($ix=0; $ix < $cnt; $ix++) {
			$key = 'key_'.$ix; 
			$this->$key = $urlParts[$ix]; 
		}
		
	}
	
	/**
	 * 
	 * Vaozvrat bazobogo URL 
	 * @param Int $startPosition - Pozicija s kotoroj nado nachinat, po umolchanije vozvrashajet ves' URL
	 * 
	 */
	public function getURL($startPosition=0) {
		$result = ''; 
		if (isset($this->_holder)) {
			//Filter values
			//Get only values with key like 'key_'
			$tmp = array();
			foreach ($this->_holder as $key=>$value) {
				if (strstr($key, 'key_')) {
					$tmp[$key] = $value; 
				}	
			}
			
			$ix = $startPosition; 
			$cnt = count($tmp);
			for($ix=$startPosition;$ix<$cnt;$ix++) {
				$key = 'key_'.$ix; 
				$urlPart = $tmp[$key];
				$result .= '/'.$urlPart;  
			}
		}
		if ($result == '') $result = '/'; 
		return $result; 
	}
	
	public function getParams() {
		if ($this->params !== null) {
			if (is_array($this->params)) {
				return implode('/', $this->params); 
			}
		}
		return ''; 
	}
	
	/**
	 * 
	 * Return array of url pieces 
	 */
	public function getArray() {
		
		return $this->_holder; 
	}
	
	/**
	 * 
	 * Set value 
	 * @param String $urlName - name for url part
	 * @param Mixed $urlValue - value  
	 */
	public function __set($urlName, $urlValue) {
		if (!isset($this->_holder)) $this->_holder = array(); 
		$this->_holder[$urlName] = $urlValue; 
	}
	
	/**
	 * 
	 * Get value for url part
	 * @param String $urlName - name of url part 
	 */
	public function __get($urlName) {
		if (isset($this->_holder)) {
			if (key_exists($urlName, $this->_holder)) {
				return $this->_holder[$urlName];  
			}
		}
		return null; 
	}
	
}