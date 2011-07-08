<?php
namespace system\tools; 
use system\base\Object;

final class Arrays {
	
	private static $_instance; 
	public static function &__(){
		if (!isset(self::$_instance)) self::$_instance = new self();
		return self::$_instance;
	}
	
	/**
	 * 
	 * Explode array to object with pattern
	 * @param array $array : Array of values
	 * @param string $keys : Pattern for keys
	 * @return system\base\Object 
	 */
	public function explodeToObject($string, $keys) {
		$values = $this->explode($string, '.'); 
		$keys = $this->explode($keys);
		$result = array();
		$ix = 0;
		foreach($values as $value) {
			$result[$keys[$ix++]] = $value;
		}
		$result = new Object($result);
		return $result;
	}
	
	/**
	 * 
	 * Explode Array
	 * @param array $array - Array of values 
	 * @param string $delimiter - Delimiter. By default used coma
	 */
	public function explode($string,$delimiter=",") {
		$parts = explode($delimiter, $string); 
		$_parts = array();
		foreach($parts as $part) {
			if (!empty($part)) $_parts[] = $part;
		}
		return $_parts;
	}
	
}