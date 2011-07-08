<?php
namespace modules\base\debug\system; 
use system\core\SystemClass;
final class Timer extends SystemClass {

	
	private $_start; 
	
	/**
	 * 
	 * Construtor. 
	 */
	public  function __construct(){
		parent::__construct();
		$this->_start = time();
		
	}
	
	public function stop(){
		
		$seconds = time() - $this->_start;
		
		return $seconds; 
	}
	
}