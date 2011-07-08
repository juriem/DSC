<?php
namespace modules\base\languages\models;

use system\core\Model;

/**
 * 
 * Model for tokens 
 * @author Juri Em
 *
 */
final class TokensModel extends Model {
	
	/**
	 * 
	 * Select tokens 
	 * @param Int $languageId - Id of current language 
	 * @param Array $filters - array of filters
	 */
	public function select($languageId, $filters) {
		$sql = "Select * From #tokens t 
				Left Outer Join (Select * From #token_values Where language_id = $languageId) v 
					On t.id = v.item_id
				Left Outer Join (Select id As m_id, module_name From #modules) m 
					On t.module_id = m.m_id Where 1=1";
		 
		if (isset($filters['module_id'])) {
			$module_id = $filters['module_id'];
			if ($module_id !== '') { 
				if ($module_id == 0) {
					$sql .= " And t.module_id Is Null"; 
				} else {
					$sql .= " And t.module_id = $module_id"; 
				}
			}
		}
		$sql .= " Order By module_name, group_name"; 
		return $this->_select($sql); 
	}
	
	/**
	 * 
	 * Select modules for filter 
	 */
	public function selectModules() {
		$sql = "Select Distinct t.module_id, m.module_name From #tokens t 
					Left Outer Join #modules m On t.module_id = m.id"; 
		
		return $this->_select($sql); 
	}
	
	/**
	 * 
	 * Get token data 
	 * @param int $id
	 */
	public function get($id) {
		$sql = "Select * From #tokens t Left Outer Join (Select id As m_id, module_name From #modules) m On t.module_id = m.m_id Where t.id = $id"; 
		return $this->_get($sql); 
	}
	
	/**
	 * 
	 * Get ID of token by hash code and module_id
	 * @param string $hashCode
	 * @param int $moduleId
	 */
	public function getIdByHash($codeHash, $moduleId = null) {
		$sql = "Select id From #tokens Where code_hash = '$codeHash'";
		if ($moduleId === null) {
			$sql .= " And module_id Is Null";  
		} else {
			$sql .= " And module_id = $moduleId"; 
		}
		$result =  $this->_get($sql);
		if ($result !== false) {
			return $result->id; 
		} 
		return false; 
	}
	
	
}