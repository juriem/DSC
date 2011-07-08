<?php
namespace modules\base\settings\models; 
use system\core\Model;
/**
 *
 * Settings table
 * @author Juri Em
 *
 */
final class SettingsModel extends Model {
	
	/**
	 *
	 * Select list of settings
	 * @param Int $moduleId - Module id. If $moduleId is Null then select settings for all system
	 */
	public function select($languageId, $moduleId = null) {

		$sql = "Select #settings.*, #setting_names.setting_name, #setting_values.value 
				From #settings 
					Left Outer Join #setting_names On #settings.id = #setting_names.item_id And #setting_names.language_id = $languageId
					Left Outer Join #setting_values On #settings.id = #setting_values.item_id And #setting_values.language_id = $languageId"; 

		if ($moduleId == null) {
			//Select global settings
			$sql .= " Where module_id Is Null";
		} else {
			//Select module settings
			$sql .= " Where module_id = $moduleId";
		}
		return $this->_select($sql);
	}

	/**
	 *
	 * Get DATA for setting
	 * @param Int $id
	 */
	public function get($id) {
		$table = new DBTable('#settings'); 
		return $table->get('id:'.$id);
	}
	
	
	/**
	 * 
	 * Get full data for setting 
	 * @param Int $id
	 * @param Int $languageId
	 */
	public function getData($id, $languageId) {
		$sql = "Select * From `#settings`
					Left Outer Join #setting_names 
						On #settings.id = #setting_names.item_id And #setting_names.language_id = $languageId
				Where #settings.id = $id";
		
		return $this->_get($sql); 
	}
	
	/**
	 * 
	 * Update or Insert settings
	 * @param Array $data - Setting data 
	 */
	public function update($data) {
		
		$this->__start(); 
		//Processing settings 
		$table = new DBTable('#settings'); 
		$id = $table->update($data); 
		if (!$id) return false; 
		//Processing names 
		if (isset($data['names'])) {
			$names = $data['names'];
			$table = new DBTable('#setting_names'); 
			foreach($names as $name) { 
				$name['setting_id'] = $id; 
				$cnt = $table->getCount($name); 
				if ($cnt === false) return false; 
				if ($table->checkEmpty($name)) {
					if ($cnt != 0) {
						if (!$table->delete($name)) return false; 
					}
				} else {
					if (!$table->update($name)) return false;	
				}
			}
		}
		//Processing values 
		if (isset($data['values'])) {
			$values = $data['values'];
			$table = new DBTable('#setting_values'); 
			foreach($values as $value){ 
				$value['item_id'] = $id; 
				
				$cnt = $table->getCount($value);
				if ($cnt === false) return false;
				if ($table->checkEmpty($value)) {
					if ($cnt != 0) {
						if (!$table->delete($value)) return false; 
					}
				} else {
					if (!$table->update($value)) return false;	
				}
			}
		}
		
		$this->__commit(); 
		return $id;
	}
	

	/**
	 *
	 * Select all settings
	 * @param Int $moduleId - Filter by module
	 */
	public function selectForDev($moduleId = '') {

		$sql = "Select s.*, m.module_name From #settings s Left Outer Join #modules m On s.module_id = m.id";

		if ($moduleId != '') {
			if ($moduleId == 'global') {
				$sql .= " Where s.module_id Is Null";
			} else {
				$sql .= " Where s.module_id = $moduleId";
			}
		}
		$sql .= " Order by module_id, code";

		return $this->_select($sql);
	}

	/**
	 *
	 * Select modules for filter
	 */
	public function selectModules() {
		$sql = "Select Distinct m.id, m.module_name From #settings s Inner Join #modules m On s.module_id = m.id Order By m.module_name";
		return $this->_select($sql);
	}

	/**
	 *
	 * Delete setting
	 * @param Int $id - Setting id
	 */
	public function delete($id){
		Db::getInstance()->startTransaction();

		$sql = "Delete From #setting_names Where setting_id = $id";
		if (!$this->_execute($sql)) return false;
		$sql = "Delete From #setting_values Where setting_id = $id";
		if (!$this->_execute($sql)) return false;
		$sql = "Delete From #settings Where id = $id";
		if (!$this->_execute($sql)) return false;

		Db::getInstance()->commitTransaction();
		return true;
	}

	/**
	 *
	 * Get values for setting
	 * @param Int $settingId - Setting's id
	 */
	public function getValues($settingId) {
		$sql = "Select * From #setting_values Where setting_id = $settingId";
		return $this->_select($sql);
	}

	/**
	 *
	 * Update values for settings
	 * @param Array $data
	 */
	public function updateValues($data) {

		$data = $this->_escapeArray($data);

		$this->__start(); 
		$id = $data['id'];
		if (!isset($data['values'])) {
			$sql = "Update #settings Set single_value = '{$data['value']}' Where id = $id";
			if (!$this->_execute($sql)) return false;
		} else {
			//Update values
			$values = $data['values'];
			$table = new DBTable('#setting_values'); 
			foreach($values as $value) {
				$value['item_id'] = $id; 
				$cnt = $table->getCount($value); 
				if ($cnt === false) return false; 
				
				if ($table->checkEmpty($value)) {
					if ($cnt != 0) {
						if (!$table->delete($value)) return false;  
					}
				} else {
					if (!$table->update($value)) return false; 
				}
			}
		}

		$this->__commit(); 
		return true;

	}
	
	/**
	 * 
	 * Check if exists
	 * @param Array $data - New data for settings
	 */
	public function checkExists($data) {
		$sql = "Select Count(*) As cnt From `#settings` Where `code` = '{$data['code']}' And `id` <> '{$data['id']}'";
		if (empty($data['module_id'])) {
			$sql .= " And `module_id` Is Null"; 
		} else {
			$sql .= " And `module_id` = '{$data['module_id']}'"; 
		}
		$rs = $this->_get($sql); 
		if ($rs) {
			if ($rs->cnt == 0) return false; 
		} 
		return true; 
	}
	
	// PUBLIC 
	/**
	 * 
	 * Select values for module and current language
	 * @param int $moduleId
	 * @param int $languageId
	 */
	public function selectValues($moduleId, $languageId) {
		
		$sql = "Select s.code, s.type, s.single_value, v.value From `#settings` s 
					Left Outer Join `#setting_values` v On s.id = v.item_id And v.language_id = $languageId"; 
		if ($moduleId == null) {
			$sql .= " Where module_id Is Null"; 
		} else {
			$sql .= " Where module_id = $moduleId"; 
		}		
		return $this->_select($sql); 
	} 
	
	/**
	 * 
	 * Get setting value
	 * @param int $moduleId - Module id 
	 * @param string $code - Code 
	 * @param int $languageId - Language ID
	 */
	public function getValue($moduleId, $code, $languageId) {
		$sql = "Select * From #settings 
				Left Outer Join #setting_values
				On #settings.id = #setting_values.item_id And #setting_values.language_id = $languageId
				Where #settings.code = '$code'"; 
		if ($moduleId == null) {
			$sql .= " And #settings.module_id Is Null"; 
		} else {
			$sql .= " And #settings.module_id = $moduleId"; 
		}
		return $this->_get($sql);
	}
	
}