<?php
namespace modules\base\languages\models;

/**
 *
 * Languages model
 * @author Juri Em
 *
 */
use system\core\Model;

final class LanguagesModel extends Model {
	
	/**
	 *
	 * Select languages
	 * @param Int $languageId - Current active language
	 */
	public function select($languageId){
		$sql = "Select * From #languages l
					Left Outer Join #language_values v On l.id = v.item_id And v.language_id = $languageId
				Where is_enabled = 1
				Order By sort_index";
		return $this->_select($sql);
	}

	/**
	 * 
	 * Select all languages 
	 * Uses in admin or developer interface 
	 * @param Int $languageId - Current language id 
	 */
	public function selectAll($languageId) {
		$sql = "Select * From #languages l
					Left Outer Join #language_values v On l.id = v.item_id And v.language_id = $languageId
				Order By sort_index";
		return $this->_select($sql); 
	}
	
	/**
	 *
	 * Get data for language
	 * @param Int $id - Language id
	 */
	public function get($id) {
		$sql = "Select * From #languages Where id = $id";
		$result = $this->_get($sql);
		return $result;
	}

	/**
	 * 
	 * Delete language 
	 * @param Int $id
	 */
	public function delete($id) {
		
		$this->__start(); 
		 
		$sql = "Select `table_name` From `#language_tables`"; 
		$rs = $this->_select($sql); 
		if ($rs)  {
			//Delete data for language
			foreach($rs as $tableName) {
				$sql = "Delete From `$tableName` Where `language_id` = $id"; 
				if (!$this->_execute($sql)) return false; 
			}
		}
		//Delete language 
		$sql = "Delete From #languages Where language_id = $id"; 
		if (!$this->_execute($sql)) return false; 
		
		$this->__commit();  
		return true;
	}

	/**
	 * 
	 * Update language values 
	 * @param Array $data
	 */
	public function update($data) {
		$this->__start(); 
		if ($data['id'] == 0) {
			//get sort index 
			$sql = "Select Max(sort_index) As max_sort_index From `#languages`"; 
			$rs = $this->_get($sql); 
			if (!$rs) return false; 
			$data['sort_index'] = (($rs->max_sort_index === null)?0:$rs->max_sort_index) + 1;
		}
		$table = new DBTable('#languages'); 
		$id = $table->update($data); 
		if (!$id) return false; 
		
		//Update values 
		$values = $data['values']; 
		$table = new DBTable('#language_values'); 
		foreach($values as $value) {
			$value['item_id'] = $id; 
			$cnt = $table->getCount($data); 
			if ($cnt === false) return false;
			if ($table->checkEmpty($data)) {
				if ($cnt != 0) {
					if (!$table->delete($data)) return false; 
				}
			} else {
				if (!$table->update($value)) return false; 
			}
		}
		$this->__commit(); 
		return $id; 
	}
	
	
	/**
	 *
	 * Update values for language
	 * @param unknown_type $data
	 */
	public function updateValues($data) {

		$result = true;

		$languageId = $data['id'];
		$values = $data['values'];
		 
		foreach($values as $_languageId=>$value) {
			
			$sql = "Select Count(*) As cnt From #language_values Where item_id = $languageId And language_id = $_languageId";
			$rs = $this->_get($sql);
			if ($rs->isEmpty()) {
				$result = false;
				break;
			}
			unset($sql);
			if ($value != '') {
				if ($rs->cnt == 0) {
					//Insert new record
					$sql = "Insert Into #language_values (item_id, language_id, language_name)
								Values($languageId, $_languageId, '$value')";
				} else {
					//Update values
					$sql = "Update #language_values Set language_name = '$value'
								Where item_id = $languageId 
										And language_id = $_languageId"; 
				}
			} else {
				if ($rs->cnt != 0) {
					//Processing delete
					$sql = "Delete From #language_values Where item_id = $languageId And language_id = $_languageId";
				}
			}
			if (isset($sql)) {
				if (!$this->_execute($sql)) {
					$result = false;
					break;
				}
			}
		}

		return $result;

	}

	/**
	 *
	 * Get language id by url_code
	 * @param String $urlCode - url_code of language
	 * @deprecated
	 */
	public function getLanguageId($urlCode) {
		$sql = "Select id From #languages Where url_code = '{$urlCode}'";
		$result = $this->_get($sql);
		if ($result) {
			return $result->id;
		}
		return null;
	}
	
	/**
	 * 
	 * Get language id by url code 
	 * @param String $urlCode
	 */
	public function getId($urlCode) {
		$urlCode = $this->_escape($urlCode); 
		$sql = "Select id From #languages Where url_code = '{$urlCode}'";
		$result = $this->_get($sql);
		if ($result) {
			return $result->id;
		}
		return null;
	}



	/**
	 *
	 * Get data with value
	 * @param Int $id - ID of menu
	 * @param Int $languageId - ID of language
	 */
	public function getValue($id, $languageId) {
		$sql = "Select * From #languages l
				Left Outer Join (Select * From #language_values Where language_id = $languageId) v
				On l.id = v.item_id
				Where l.id = $id"; 
		return $this->_get($sql);
	}
	
	
	/**
	 * 
	 * Check if language cant be deleted 
	 * @param Int $id - Language id 
	 */
	public function checkCanDelete($id){
		$sql = "Select * From #languages Where id = $id"; 
		$rs = $this->_get($sql); 
		if ($rs) {
			if ($rs->is_default == 0 && $rs->is_admin_default == 0 && $rs->is_developer_default == 0) return true; 
		}
		return false;
	}
	
	/**
	 * 
	 * Check if language doesnt exist
	 * @param Array $data
	 */
	public function checkNotExist($data) {
		$sql = "Select Count(*) As cnt From `#languages` Where `url_code` = '{$data['url_code']}' And id <> {$data['id']}"; 
		$rs = $this->_get($sql); 
		if ($rs) {
			if ($rs->cnt == 0) return true;
		}
		return false; 
	}
	
	
	// Languages.class.php
	/**
	 * 
	 * Check if language enabled 
	 * @param Int $id - ID of language 
	 */
	public function checkEnabled($id) {
		$sql = "Select is_enabled From #languages Where id = $id";
		$rs = $this->_get($sql); 
		if ($rs) {
			if ($rs->is_enabled == 1) return true; 
		}
		return false; 
	}

	/**
	 * 
	 * Checking if language enabled by code 
	 * @param unknown_type $urlCode
	 */
	public function checkEnabledByCode($urlCode) {
		$sql = "Select Count(*) As cnt From `#languages` Where `url_code` = '$url_code' And is_enabled = 1"; 
		$rs = $this->_get($sql); 
		if ($rs !== false){
			if ($rs->cnt != 0) return true; 
		}
		return false; 
	}
	/**
	 * 
	 * Get default language 
	 * @param String $typeName - column name for search. Used values: is_default, is_admin_default, is_developer_default
	 */
	public function getDefault($typeName = 'is_default') {
		$sql = "Select url_code From #languages Where `$typeName` = 1";
		$rs = $this->_get($sql);
		if ($rs) {
			return $rs->url_code; 
		} 
		return null; 
	}
	
}