<?php
namespace modules\base\developer\models;
use system\core\Model;
/**
 * 
 * Modules model 
 * @author Juri Em
 *
 */
final class ModulesModel extends Model {
	
	/**
	 * 
	 * Select module_names 
	 */
	public function selectNames(){
		$sql = "Select * From #modules"; 
		return $this->_select($sql);
	}
	
	/**
	 * 
	 * Get list of modules 
	 */
	public function select($languageId) {
		$sql = "Select * From #modules 
			Left Outer Join #module_values On #modules.id = #module_values.item_id And #module_values.language_id = $languageId";

		$modules = $this->_select($sql); 
		//checking file system
		if ($modules) {
			$_buffer = array();  
			foreach($modules as $module) {
				$module->_isBroken = false; 
				$modulePath = ROOT.DS.'modules'.DS.'system'.DS.'mod_'.$module->module_name.DS.'index.php';
				if (!file_exists($modulePath)) {
					$modulePath = ROOT.DS.'modules'.DS.'mod_'.$module->module_name.DS.'index.php';
					if (!file_exists($modulePath)) {
						$module->_isBroken = true;		
					}
				}
				$_buffer[] = $module; 
			}
			$modules = $_buffer; 
		}
		return $modules; 
	}
	
	/**
	 * 
	 * Get data for module
	 * @param Int $id
	 */
	public function get($id) {
		$sql = "Select * From #modules Where id = $id"; 
		return $this->_get($sql); 		
	}
	
	/**
	 * 
	 * Check if module doesnt exists
	 * @param int $id
	 * @param string $moduleName
	 */
	public function checkNotExists($id, $moduleName) {
		
		$sql = "Select Count(*) As cnt From #modules Where id <> $id And module_name = '$moduleName'"; 
		$rs = $this->_get($sql); 
		if ($rs) {
			if ($rs->cnt == 0) return true; 
		}
		return false; 
	}
	
	/**
	 * 
	 * Update data 
	 * @param array $data
	 */
	public function update($data) {
		$data = $this->_escapeArray($data); 
		
		$this->__start();

		$table = new DBTable('#modules'); 
		$id = $table->update($data); 
		if ($id === false) return false; 
		
		if (isset($data['values'])) {
			$values = $data['values'];
			$table = new DBTable('#module_values');  
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
		return $id; 
	}
	
	/**
	 * 
	 * Delete module from system
	 * @param int $id
	 */
	public function delete($id) {
		//Get module data 
		$sql = "Select module_name From #modules Where id = $id"; 
		$rs = $this->_get($sql); 
		if ($rs === false) return false; 
		$moduleName = $rs->module_name;
		//Check if exists in system 
		$foundIt = true; 
		$path = ROOT.DS.'modules'.DS.'system'.DS.'mod_'.$moduleName; 
		if (!is_dir($path)) {
			$path = ROOT.DS.'modules'.DS.'mod_'.$moduleName; 
			if (!is_dir($path)) $foundIt = false; 
		}
		
		
		//Delete data 
		$sql = array();
		//Delete token values  
		$sql[] = "Delete From #token_values Where item_id In (Select id From #tokens Where module_id = $id)";
		//Delete tokens  
		$sql[] = "Delete From #tokens Where module_id = $id"; 
		//Delete module values 
		$sql[] = "Delete From #module_values Where item_id = $id"; 
		//Delete menu related
		$sql[] = "Delete From #menu_module_items Where module_id = $id"; 
		$sql[] = "Delete From #menu_modules Where module_id = $id"; 
		$sql[] = "Delete From #module_image_names Where module_id = $id"; 
		$sql[] = "Delete From #module_images Where module_id = $id"; 
		$sql[] = "Delete From #modules Where id = $id"; 
		if (!$this->_execute($sql)) return false;

		//Delete images related to module  
		$dirname = ROOT.DS.'storage'.DS.'system'.DS.$id;		
		if (is_dir($dirname)) {
			//Delete dir
			@rmdir($dirname); 
		}
		
		return true; 
	}
}