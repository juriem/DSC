<?php
namespace system\core;
/**
 *
 * Class for processing and analyzing global URL path
 * @author Juri Em
 *
 */
final class URL {
	
	// >> Static 
	protected static $_instance; 
	public static function &__(){
		if (!isset(self::$_instance)) self::$_instance = new self();
		return self::$_instance;
	}
	// Static <<
	
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	private $_baseUrl; 
	
	private $_holder;
	
	/**
	 * 
	 * Private constructor
	 * @param string $url - 
	 */
	private function __construct(){
		// Init holder for url parts 
		$this->_holder = array(); 
		// Get base url
		if (preg_match('/(.*)index.php/i', $_SERVER['PHP_SELF'], $matches)) {
			$_base_url = $matches[1];
			$this->_baseUrl = $matches[1];
		} else {
			die('Error geting base url');
		}
	}
	
	/**
	 * 
	 * Add part in url 
	 * @param string $url - Part of url. 
	 */
	public function add($url) {
		$this->_holder[] = $url;	
	}
	
	/**
	 * 
	 * Remove last elements from url path
	 */
	public function removeLast() {
		array_pop($this->_holder);
	}
	
	
	
	/**
	 *
	 * Init URL routine with full url path
	 * @param string $url
	 */
	public function set($url) {
		$urlParts = explode('/', $url);
		foreach($urlParts as $urlPart) {
			if (!empty($urlPart)) $this->add($urlPart);
		}
	}

	
	/**
	 * 
	 * Get base url
	 */
	public function getBaseURL(){
		
		$_baseUrl = $this->_baseUrl;
		// Remove last slash 
		if ($_baseUrl == '/') {
			$_baseUrl = ''; 
		} else {
			$_baseUrl = substr($_baseUrl, 0, strlen($_baseUrl)-1);
		}
		return $_baseUrl;
	}

	/**
	 * 
	 * Get URL in brwoser format 
	 * @param int $shift - shifting to upper level
	 */
	public function get($shift=0){
		$url = '';
		if (isset($this->_holder)) {
			$cnt = count($this->_holder) - $shift; 
			for($index=0;$index < $cnt;$index++) {
				$part = $this->_holder[$index];
				$url .= (empty($url)?'':'/') . $part;
			}
		}
		return $this->_baseUrl . $url;
	}
	
	/**
	 * 
	 * Build URL
	 * @param string  $url - additional value for url 
	 * @param int $shift - shift to upper level
	 */
	public function build($url = '', $shift=0){
		if ($url === '') {
			return $this->get($shift); 
		} else {
			return $this->get($shift) . '/' . $url;	
		}
	}
	
	/**
	 * 
	 * Parse and replace __BASE_URL__
	 * @param string $html
	 */
	public function parse($html) {
		return str_replace('__BASE_URL__', $this->_baseUrl, $html);
	}
}