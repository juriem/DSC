<?php
namespace modules\base\developer\models;
use system\core\Model;
/**
 *
 * Model for menus module
 * @author Juri Em
 *
 */
final class MenusModel extends Model {

	/**
	 * 
	 * Select site menus 
	 */
	public function select() {
		$sql = "Select * From #menus Where is_admin = 0 Order By p_id, sort_index"; 
		return $this->_select($sql); 
	}

	/**
	 *
	 * Get menus
	 * @param Int $groupId
	 */
	public function getMenus($groupId) {
		$sql = "Select * From #menus Where group_id = $groupId Order By sort_index";
		return $this->_select($sql);
	}

	/**
	 *
	 * Get data by id
	 * @param unknown_type $id
	 */
	public function get($id) {
		$sql = "Select * From `#menus` Where `id` = $id";
		return $this->_get($sql);
	}


	/**
	 *
	 * Check if can delete menu
	 * @param int $id
	 */
	public function checkCanDelete($id) {
		$id = (int)$id;
		$sql = "Select Count(*) As cnt From #menus Where p_id = $id";
		$rs = $this->_get($sql);
		if ($rs) {
			if ($rs->cnt == 0) return true;
		}
		return false;
	}

	/**
	 * 
	 * Delete menu 
	 * @param int $id - Id of menu
	 */
	public function delete($id) {
		$id = (int)$id; 
		$this->__start();
		//Select sort_index and is_admin 
		$sql = "Select sort_index, is_admin, p_id From #menus Where id = $id"; 
		$rs = $this->_get($sql);
		if ($rs === false) return false; 
		//Delete values  
		$sql = "Delete From #menu_values Where item_id = $id"; 
		if (!$this->_execute($sql)) return false; 
		//Delete modules 
		$sql = "Delete From #menu_modules Where menu_id = $id"; 
		if (!$this->_execute($sql)) return false;

		//Update sortindex
		$model = App::getModel('helper','sortindex');
		$adWhere = null; 
		if ($rs->is_admin == 1) $adWhere = 'is_admin = 1';
		if (!$model->delete($id,'#menus', 'p_id', $rs->p_id, $adWhere)) return false; 
		//Delete menu  
		$sql = "Delete From #menus Where id = $id"; 
		if (!$this->_execute($sql)) return false;
		
		$this->__commit(); 
		return true; 
	}
	
	// ADMIN MENUS
	/**
	 *
	 * Get data for admin menus
	 * @param Int $id - Id of menu
	 */
	public function getAdmin($id) {
		$id = (int)$id;
		$sql = "Select #menus.*, #menu_modules.module_id, #menu_modules.default_controller, #menu_modules.default_task
						From #menus Left Outer Join #menu_modules On #menus.id = #menu_modules.menu_id Where #menus.id = $id"; 
		return $this->_get($sql);
	}

	/**
	 *
	 * Select administrative menu
	 * @param Int $languageId - Language id
	 */
	public function selectAdminMenus($languageId){
		$languageId = (int)$languageId;
		$sql = "Select #menus.*, #menu_values.title, #modules.module_name, #menu_modules.default_controller, #menu_modules.default_task
					From `#menus` 
					Left Outer Join #menu_values  On #menus.id = #menu_values.item_id And #menu_values.language_id = $languageId
					Left Outer Join #menu_modules On #menus.id = #menu_modules.menu_id
					Left Outer Join #modules On #modules.id = #menu_modules.module_id
				Where #menus.is_admin = 1 
				Order By #menus.p_id, #menus.sort_index"; 
		return $this->_select($sql);
	}

	/**
	 * 
	 * Update non admin menus 
	 * @param Array $data
	 */
	public function updateMenu($data) {
		$data = $this->_escapeArray($data); 
		$this->__start(); 
		if ($data['id'] == 0) {
			
			$sql = "Select Max(sort_index) As max_sort_index From #menus Where id <> {$data['id']} And is_admin = 0"; 
			if ($data['p_id'] == 0) {
				$sql .= " And p_id Is Null"; 
			} else {
				$sql .= " And p_id = {$data['p_id']}"; 
			}
			$rs = $this->_get($sql);
			if (!$rs) return false;
			$data['sort_index'] = (($rs->max_sort_index == null)?0:$rs->max_sort_index) + 1;  
		}
		$table = new DBTable('#menus'); 
		$id = $table->update($data); 
		
		//Update values 
		$values = $data['values'];
		$table = new DBTable('#menu_values');  
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
		
		$this->__commit(); 
		return $id;
	}

	/**
	 *
	 * Update data
	 * @param Array $data
	 */
	public function update($data) {
		$this->__start();
		
		if ($data['is_group'] === 0) $data['url_code'] = null; 
		$data['is_admin'] = 1; 
		if ($data['id'] == 0) {
			//Get sort_index
			$sql = "Select Max(sort_index) As max_sort_index
					From #menus Where is_admin = 1"; 
			if ($data['p_id'] == 0) {
				$sql .= " And p_id Is Null";
			} else {
				$sql .= " And p_Id = {$data['p_id']}";
			}
			$rs = $this->_get($sql);
			if (!$rs) return false;
			$data['sort_index'] = ((int)$rs->max_sort_index)+1;
		}
		$table = new DBTable('#menus');
		$id = $table->update($data);
		if (!$id) return false;

		//Update modules
		if ($data['is_group']) {
			$sql = "Delete From #menu_modules Where menu_id = $id"; 
			if (!$this->_execute($sql)) return false; 
		} else {
			$table = new DBTable('#menu_modules');
			$tmp = array();
			$tmp['menu_id'] = $id;
			$tmp['module_id'] = $data['module_id'];
			$tmp['default_controller'] = $data['default_controller'];
			$tmp['default_task'] = $data['default_task'];
			if (!$table->update($tmp)) return false;
		}
		
		$values = $data['values']; 
		$table = new DBTable('#menu_values'); 
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

		$this->__commit();
		return $id;
	}



}