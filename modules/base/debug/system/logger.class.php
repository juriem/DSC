<?php
namespace modules\base\debug\system;

use system\core\SystemClass;
use system\base\Object;
/**
 * 
 * Logger class 
 * Low level logger class 
 * 
 * @author Juri Em
 *
 */
final class Logger extends SystemClass {
	
	
	/**
	 * 
	 * Holder for messages 
	 * @var array
	 */
	private static $_holder; 
	
	
	
	/*
	 * List of messages type 
	 */
	const SYSTEM_MESSAGE = 'system';  
	const DATABASE_MESSAGE = 'database'; 
	const DEBUG_MESSAGE = 'debug'; 
	const ERROR_MESSAGE = 'error';
	 
	/**
	 * 
	 * Register message into logger 
	 * @param String $message
	 */
	public static function register($message, $type=Logger::SYSTEM_MESSAGE) {
		if (!isset(self::$_holder)) self::$_holder = array();
		$msg = new Object(); 
		$msg->_type = $type; 
		$msg->_content = $message; 
		$msg->_date = date('Y/d/m H:i:s', time());
		self::$_holder[] = $msg;
	}
	
	/**
	 * 
	 * Dump messages 
	 * @param string $messageType - Used for filter messages 
	 */
	public static function dump($messageType = null) {
		$messages = array();
		if (isset(self::$_holder)) {
			
			foreach(self::$_holder as $message) {
				$add = true; 
				if ($messageType !== null) {
					if ($messageType !== $message->_type) $add = false; 
				} 
				if ($add) $messages[] = $message; 
			} 
		}
		return $messages; 
	}
	
	 

}