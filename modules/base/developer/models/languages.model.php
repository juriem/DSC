<?php
namespace modules\base\developer\models;
use system\core\Model;
/**
 * 
 * Languages model 
 * @author Juri Em
 *
 */
final class LanguagesModel_Developer extends Model {

	public function getLanguages() {
		$sql = "Select * From #languages"; 
		return $this->_select($sql); 
	}

	/**
	 * 
	 * Get language's data 
	 * @param Int $id
	 */
	public function get($id) {
		$sql = "Select * From #languages Where id = $id"; 
		return $this->_get($sql); 
	} 
	
	
	
	/**
	 * 
	 * Select languages 
	 * @param Int $languageId
	 */
	public function select($languageId) {
		$sql = "Select * From #languages l 
			Left Outer Join (Select * From #language_values Where language_id = $languageId) v
			On l.id = v.item_id"; 
		return $this->_select($sql); 
	}

}