<?php
namespace modules\base\modules\models;
use system\core\Model;

final class ModulesModel extends Model {
	
	/**
	 * 
	 * Select modules for developer module 
	 */
	public function selectDev() {
		$sql = "Select id, module_name, is_system From #modules Order By module_name"; 
		return $this->_select($sql);
	}
	
	/**
	 * 
	 * Get module id 
	 * @param String $moduleName - Name of module 
	 */
	public function getId($moduleName) {
		$sql = "Select id From #modules Where module_name = '$moduleName'"; 
		$rs = $this->_get($sql); 
		if ($rs) {
			return $rs->id; 
		}
		return null; 
	}
	
	/**
	 * 
	 * Get module data 
	 * @param Int $id - Id of module 
	 */
	public function get($id) {
		$sql = "Select * From #modules Where id = $id";
		return $this->_get($sql);
	}
	
	/**
	 * 
	 * Get all links for site 
	 */
	public function getLinks($languageId){
		
		//Get all menus 
		$sql = "Select * From #menus 
				Left Outer Join #menu_values On #menus.id = #menu_values.item_id And #menu_values.language_id = $languageId
				Where #menus.is_admin = 0 And #menus.is_group = 0
				Order By #menus.p_id, #menus.sort_index"; 
		$menus = $this->_select($sql); 
		
		$links = array();
		if ($menus) {
			foreach($menus as $menu) {
				$link = new Object(); 
				$link->url = '/'.$menu->url_code; 
				$link->title = $menu->title; 
				$links[] = $link;
			}
		}
		
		return $links; 
	}
	
}