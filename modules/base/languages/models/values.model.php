<?php
namespace modules\base\languages\models;
/**
 * 
 * Special model for values 
 * @author Juri Em
 *
 */
use system\core\Model;
final class ValuesModel extends Model {
	
	//New 
	/**
	 * 
	 * Select language values 
	 * @param String $tableName
	 * @param Int $itemId
	 */
	public function selectValues($tableName, $itemId = null) {
		$tableName = (string)$tableName; 
		$itemId = (int)$itemId; 
		$sql = "Select * From `$tableName`"; 
		if ($itemId !== null) {
			$sql .= " Where `item_id` = $itemId"; 
		}
		
		return $this->_select($sql); 
	}
	
	/**
	 * 
	 * Select value for item 
	 * @param Int $id - Id of item
	 * @param String $idName - Name of id field
	 * @param String $tableName - Table of values 
	 */
	public function select($id, $idName, $tableName) {
		$sql = "Select * From `$tableName` Where `$idName` = '$id'";
		return $this->_select($sql);
	}
	
	
	/**
	 * 
	 * Update values 
	 * @param array $values - Array of values 
	 * @param int $id - Value for item_id 
	 * @param string $tableName - Name of table 
	 * @param string $itemColumnName - (optional) Name of column 
	 */
	public function update($values, $id, $tableName, $itemColumnName = 'item_id') {
		$this->__start();
		
		$table = new DBTable($tableName); 
		foreach($values as $value){
			$value[$itemColumnName] = $id;
			$value = $this->_escapeArray($value);
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
		return true;
	}
	
	/**
	 * 
	 * Get value 
	 * @param Array $data {id,id_name,table_name,value_name}
	 * @param Int $languageId
	 */
	public function getValue($data, $languageId) {
		$sql = "Select `{$data['value_name']}` As value From `{$data['table_name']}` Where `{$data['id_name']}` = {$data['id']} And language_id = $languageId"; 
		$rs = $this->_get($sql); 
		if ($rs) {
			return $rs->value; 
		}
		return null; 
	}
	
}