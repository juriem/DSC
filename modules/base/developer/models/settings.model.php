<?php
namespace modules\base\developer\models;
use system\core\Model;
/**
 * 
 * Settings model 
 * @author Juri Em
 *
 */
final class SettingsModel extends Model {

	/**
	 * Select all settings 
	 * @param Int $moduleId - Filter by module  
	 */
	public function select($moduleId = '') {
		
		$sql = "Select #settings.*, #modules.module_name From #settings 
					Left Outer Join #modules
						On #settings.module_id = #modules.id";
		
		if ($moduleId != '') {
			if ($moduleId == 'global') {
				$sql .= " Where #settings.module_id Is Null"; 
			} else {
				$sql .= " Where #settings.module_id = $moduleId"; 
			}
		}
		$sql .= " Order By #settings.module_id, #settings.code";
		return $this->_select($sql);
	}
	
	/**
	 * 
	 * Select modules for filter 
	 */
	public function selectModules() {
		$sql = "Select Distinct m.id, m.module_name 
					From #settings s Inner Join #modules m On s.module_id = m.id Order By m.module_name"; 
		return $this->_select($sql); 
	}
	
	/**
	 * 
	 * Get data for setting
	 * @param int $id
	 */
	public function get($id) {
		$sql = "Select * From #settings Where id = $id"; 
		return $this->_get($sql); 
	}
	
	/**
	 * 
	 * Delete setting 
	 * @param Int $id - Setting id 
	 */
	public function delete($id){
		$this->__start();
		$sql = array(); 
		$sql[] = "Delete From #setting_names Where item_id = $id";
		$sql[] = "Delete From #setting_values Where item_id = $id";
		$sql[] = "Delete From #settings Where id = $id"; 
		if (!$this->_batch($sql)) return false;
		$this->__commit();  
		return true; 
	}
	
	/**
	 * 
	 * Update setting and values 
	 * @param array $data
	 */
	public function update($data){
		$this->__start(); 
		
		$data = $this->_escapeArray($data); 
		
		$table = new DBTable('#settings'); 
		$id = $table->update($data); 
		
		//Update names
		$names = $data['names'];
		$table = new DBTable('#setting_names'); 
		foreach($names as $name) {
			$name['item_id'] = $id; 
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
		
		 
		//Update values 
		if (in_array($data['type'], array('text','string'))) {
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
		} else {
			//Delete values for settings 
			$sql = "Delete From #setting_values Where item_id = $id";
			if (!$this->_execute($sql)) return false; 
		}
		
		$this->__commit(); 
		return $id; 
	}
	
}