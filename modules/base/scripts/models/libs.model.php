<?php
namespace modules\base\scripts\models;

/**
 * 
 * Model for libs 
 * @author Juri Em
 *
 */
use system\core\Model;

final class LibsModel extends Model{

	
	// System 
	/**
	 * 
	 * Get data by code 
	 * @param String $code - Code of library 
	 */
	public function getByCode($code) {
		$code = (string)$code; 
		$sql = "Select * From `#libs` Where code = '$code'"; 
		return $this->_get($sql); 
	} 
	
	/**
	 * 
	 * Select items for lib
	 * @param Int $libId - ID of lib
	 */
	public function selectItems($libId) {
		$libId = (int)$libId; 
		$sql = "Select * From `#lib_items` Where lib_id = $libId"; 
		return $this->_select($sql); 
	}
	
}