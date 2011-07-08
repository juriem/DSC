<?php
namespace modules\base\sessions\models;
use system\core\Model;

/**
 * 
 * Database session 
 * @author Juri Em
 *
 */
final class SessionsModel extends Model {
	
	/**
	 * 
	 * Get data for session 
	 * @param unknown_type $id
	 */
	public function get($id) {
		$time = $this->_escape((string)time());
		$id = $this->_escape($id); 
		$sql = "Select `session_data` From `#sessions` Where `session_id` = '$id' And `expires` > $time";
		
		return $this->_get($sql); 
	}
	
	/**
	 * 
	 * Update session 
	 * @param Array $data
	 */
	public function update($data){
		$data = $this->_escapeArray($data); 
		$table = new DBTable('#sessions'); 
		return $table->update($data); 
	}
	
	/**
	 * 
	 * Delete session 
	 * @param Int $id - ID of session 
	 */
	public function delete($id) {
		$id = $this->_escape($id); 
		$sql = "Delete From `#sessions` Where `session_id` = $id"; 
		return $this->_execute($sql); 
	}
	
	/**
	 * 
	 * Delete expired 
	 */
	public function deleteExpired(){
		$time = time(); 
		$sql = "Delete From `#sessions` Where `expires` < $time"; 
		return $this->_execute($sql); 
	}
	
}