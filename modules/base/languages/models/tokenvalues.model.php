<?php
namespace modules\base\languages\models;
/**
 *
 * Token values
 * @author Juri Em
 *
 */
use system\core\Model;

final class TokenValuesModel extends Model {

	/**
	 *
	 * Select token values
	 * @param Int $tokenId - Token id
	 */
	public function select($tokenId) {
		$sql = "Select * From #token_values Where item_id = $tokenId";
		return $this->_select($sql);
	}

	/**
	 *
	 * Update token value
	 * @param Array $data
	 */
	public function update($data) {
		
		$this->__start(); 
		
		$result = true;
		$tokenId = $data['id'];
		$values = $data['values'];
		foreach($values as $languageId=>$value) {
			$sql = "Select Count(*) As cnt From #token_values Where item_id = $tokenId And language_id = $languageId";

			$rs = $this->_get($sql);
			if (!$rs) return false;
				
			unset($sql);
			//Prepare sql query
			if ($value == '') {
				if ($rs->cnt != 0) {
					$sql = "Delete From #token_values Where item_id = $tokenId And language_id = $languageId";
				}
			} else {
				if ($rs->cnt == 0) {
					$sql = "Insert Into #token_values (item_id, language_id, value)
									Values($tokenId, $languageId, '$value')"; 
				} else {
					$sql = "Update #token_values Set value = '$value'
									Where item_id = $tokenId And language_id = $languageId"; 
				}
			}
			if (isset($sql)) {
				if (!$this->_execute($sql)) return false;
			}
				
		}

		$this->__commit(); 

		return true;
	}
	
	/**
	 * 
	 * Select values for modules by language and modules list 
	 * @param int $languageId
	 * @param string $modulesList
	 */
	public function selectValues($languageId, $modulesList){
		$modules = explode(',', $modulesList); 
		//Trim values
		$buffer = array();  
		foreach($modules as $module) {
			$buffer[] = trim($module);
		}
		$modules = $buffer; 
		unset($buffer); 
		//Create condition for SQL query 
		$_modules = '';  
		foreach($modules as $module) {
			$_modules = (($_modules === '')?'':',')."'$module'";
		}
		
		$sql = "Select #token_values.value, #token.group_name, #token.value_name, #modules.module_name
					From #tokens 
					Inner Join #modules On #tokens.module_id = #modules.id
					Inner Join #token_values On #tokens.id = #token_values.item_id And #token_values.language_id = $languageId
				Where #modules.module_name In ($_modules)";
		return $this->_select($sql); 
	}
	
	/**
	 * 
	 * Get data by Ids
	 * @param int $itemId - token Id
	 * @param int $languageId - language Id 
	 */
	public function getValue($itemId, $languageId) {
		
		$itemId = (int)$itemId; 
		$languageId = (int)$languageId; 
		$sql = "Select value From #token_values Where item_id = $itemId And $languageId = $languageId"; 
		$result =  $this->_get($sql);
		
		if ($result !== false) {
			return $result->value; 
		}
		return null; 
	}
	
}