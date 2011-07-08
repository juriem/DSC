<?php
namespace system\base;
/**
 *
 * Object class
 * @author Juri Em
 *
 */
class Object {

	private $_holder;

	/**
	 *
	 * Constructor
	 * @param array $array - Array of values. Value can be array type.
	 * @param boolean $deep - Deep converting. If value is TRUE then deep processing of array and also converts internal arrays to object
	 */
	public function __construct($array=null, $deep=false) {
		if (is_array($array)) {
			foreach ($array as $key=>$value){
				if ($deep) {
					if (is_array($value)) $value =new self($value,$deep);
				}
				$this->$key = $value;
			}
		}
	}

	
	/**
	 *
	 * Return array
	 */
	public function getArray() {
		return $this->_holder;
	}

	/**
	 *
	 * Get list of keys delimited by default with colons :
	 * @return string
	 */
	public function getKeys($delimiter=':'){
		$keysList = '';
		if(isset($this->_holder)) {
			foreach($this->_holder as $key=>$value){
				$keysList .= (empty($keysList)?'':$delimiter).$key;
			}
		}
		return $keysList;
	}

	/**
	 *
	 * Set value
	 * @param String $valueName - Name of value
	 * @param Mixed $value - Value
	 */
	public function __set($valueName, $value) {
		if (!isset($this->_holder)) $this->_holder = array();
		$this->_holder[$valueName] = $value;
	}

	/**
	 *
	 * Get value
	 * @param String $valueName - Name of
	 */
	public function __get($valueName) {
		if (isset($this->_holder)) {
			if (array_key_exists($valueName, $this->_holder)) {
				return $this->_holder[$valueName];
			}
		}
		return null;
	}

}