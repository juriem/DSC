<?php
namespace modules\base\developer\models;
use system\core\Model;
/**
 * 
 * Enter description here ...
 * @author Juri Em
 *
 */
final class LibsModel extends Model {

	/**
	 * 
	 * Get list of libraries 
	 */
	public function getList(){
		$sql = "Select * From #libs"; 
		return $this->_select($sql); 
	}
	
	/**
	 * 
	 * Get data for library 
	 * @param Int $id
	 */
	public function get($id) {
		$sql = "Select * From #libs Where id = $id"; 
		return $this->_get($sql) ;
	}
	
	/**
	 * 
	 * Update lib 
	 * @param Array $data
	 */
	public function update($data){
		
		Db::getInstance()->startTransaction(); 
		
		$id = $data['id']; 
		$data['additional_script'] = Db::getInstance()->escapeString($data['additional_script']); 
		
		if ($data['id'] == 0) {
			$sql = "Insert Into #libs (code, additional_script, folder_name)
						Values('{$data['code']}', '{$data['additional_script']}', '{$data['folder_name']}')"; 
			if (!$this->_execute($sql)) {
				Db::getInstance()->rollbackTransaction();
				return false; 
			} 
			$id = $this->_insertedId();  
		} else {
			$sql = "Update #libs Set code = '{$data['code']}', 
									additional_script = '{$data['additional_script']}',
									folder_name = '{$data['folder_name']}'
								Where id = $id";
			if (!$this->_execute($sql)) {
				Db::getInstance()->rollbackTransaction();
				return false; 
			} 
		}
		
		
		//Processing items
		$items = $data['items'];
		foreach($items as $item) {
			if ($item['id'] == 0) {
				$sql = "Insert Into #lib_items (lib_id, item_type, item_name)
						Values($id, '{$item['item_type']}', '{$item['item_name']}')"; 
			} else {
				$sql = "Update #lib_items 
						Set item_type = '{$item['item_type']}', item_name = '{$item['item_name']}'
						Where id = {$item['id']}"; 
			}
			if (!$this->_execute($sql)) {
				Db::getInstance()->rollbackTransaction();
				return false;  
			} 
		}
		
		Db::getInstance()->commitTransaction(); 
		return $id; 
	}
	
	/**
	 * 
	 * Delete lib
	 * @param Int $id
	 */
	public function delete($id) {
		Db::getInstance()->startTransaction(); 
		
		$sql = "Delete From #lib_items Where lib_id = $id"; 
		if (!$this->_execute($sql)) return false;
		$sql = "Delete From #libs Where id = $id"; 
		if (!$this->_execute($sql)) return false;
		
		Db::getInstance()->commitTransaction(); 
		return true; 
	}
	
	//Items
	/**
	 * 
	 * Delete item
	 * @param unknown_type $id
	 */
	public function deleteItem($id) {
		$sql = "Delete From #lib_items Where id = $id";
		return $this->_execute($sql); 
	}
	
	public function getItems($id) {
		$sql = "Select * From #lib_items Where lib_id = $id"; 
		return $this->_select($sql);
	}
}