<?php
namespace modules\base\db\system;
/**
 *
 * Database table class
 * @author Juri Em
 *
 */
final class DBTable {

	/**
	 *
	 * Table name
	 * @var String
	 */
	protected $_tableName;

	/**
	 *
	 * List of primary keys
	 * @var Array
	 */
	protected $_primaryKeys;

	/**
	 *
	 * List of columns
	 * @var Array
	 */
	protected $_columns;

	/**
	 *
	 * Flag if primary key has autoincreament
	 * @var Boolean
	 */
	protected $_primaryAutoIncrement = false;

	/**
	 *
	 * List of columns for check before update
	 * @var Array
	 */
	protected $_existingCheckColumns;

	/**
	 *
	 * List of tables for checking before delete
	 * @var Array of object(object{table, column})
	 */
	protected $_deleteCheckTables;

	protected $_db;

	/**
	 *
	 * Constructor
	 * @param String $tableName - Table name
	 * @param String $existsCondition - Condition for check before add or update record
	 * @param String $deleteCondition - Conditions for check before delete record
	 */
	public function __construct($tableName, $existsCondition = null, $deleteCondition = null) {
		//Get database handler
		$this->_db =& Db::getInstance();

		if (!empty($tableName)) {
			$this->_tableName = $tableName;
			$sql = "Describe " . $this->_tableName;
			$result = $this->_db->getRows($sql);
			if ($result) {
				foreach($result as $row) {
					if (!isset($this->_columns)) $this->_columns = array();
					$this->_columns[] = $row;

					if ($row->Key == 'PRI'){
						if (!isset($this->_primaryKeys)) $this->_primaryKeys = array();
						//Processing primary key
						$pk = new Object();
						$pk->key = $row->Field;
						if ($row->Extra == 'auto_increment') {
							$pk->auto_increment = true;
						} else {
							$pk->auto_increment = false;
						}
						$this->_primaryKeys[] = $pk;
					}
				}
			}
			//Processing exists conditions
			if (!empty($existsCondition)) $this->_existingCheckColumns = explode(',', $existsCondition);

			//Processing delete_conditions
			if (!empty($deleteCondition)) {
				$pairs = explode(',', $deleteCondition);
				var_dump($pairs);
				foreach($pairs as $pair) {
					if (!isset($this->_deleteCheckTables)) $this->_deleteCheckTables = array();
					$tmp = explode(':', $pair);
					$object = new Object();
					$object->table = $tmp[0];
					$object->column = $tmp[1];
					$this->_deleteCheckTables[] = $object;
				}
			}
		}
	}

	/**
	 *
	 * Get condition for ids
	 * @param String $ids - Ids of table
	 */
	public function getIds($ids) {
		$sql = '';
		//Processing array of ids
		$pairs = explode(',',$ids);
		$pKeys = array();

		foreach($pairs as $pair) {
			$tmp = explode(':', $pair);
			$object = new Object();
			$object->key = $tmp[0];
			$object->value = $tmp[1];
			$pKeys[] = $object;
		}


		$first = true;
		foreach($pKeys as $pKey) {
			$keyExists = false;
				
			foreach($this->_primaryKeys as $pk) {
				if ($pk->key == $pKey->key) {
					$keyExists = true;
					break;
				}
			}
			if ($keyExists) {
				if ($first) {
					$sql .= " `{$pKey->key}` = '{$pKey->value}'";
					$first = false;
				} else {
					$sql .= " And `{$pKey->key}` = '{$pKey->value}'";
				}
			}
		}

		return $sql;
	}

	/**
	 *
	 * Get record by id
	 * @param String $ids - ID of record in next form pk1:value,pk2:value
	 */
	public function get($ids) {
		$sql = 'Select * From `'.$this->_tableName.'`';

		
		if (!is_array($ids)) {
			$sql .= " Where " . $this->getIds($ids);	
		} else {
			$sql .= " Where"; 
			$first = true;
			foreach($this->_primaryKeys as $pk) {
				foreach($ids as $key=>$value) {
					if ($key == $pk->key){
						$sql .= (($first)?'':' And')." `$key`='$value'"; 
						break; 
					}
				}
				$first = false; 
			}
		}

		$rs = $this->_db->getRow($sql);
		if (!$rs) {
			//Return empty result
			$rs = array();
			foreach($this->_columns as $column){
				$keyDefault = 'Default';
				$keyNull = 'Null';
				if ($column->$keyNull == 'Yes') {
					$rs[$column->Field] = null;
				} else {
					$rs[$column->Field] = $column->$keyDefault;
				}
				//Primary keys
				foreach($this->_primaryKeys as $pk) {
					$rs[$pk->key] = null;
				}
			}
			$rs = new Object($rs);
		}
		return $rs;
	}

	/**
	 *
	 * Delete record
	 * @param String or Array $ids - List of ids
	 */
	public function delete($ids) {
		$sql = "Delete From `{$this->_tableName}`";
		if (!is_array($ids)) {
			$sql .= " Where " . $this->getIds($ids);	
		} else {
			$sql .= " Where"; 
			$first = true;
			foreach($this->_primaryKeys as $pk) {
				foreach($ids as $key=>$value) {
					if ($key == $pk->key){
						$sql .= (($first)?'':' And')." `$key`='$value'"; 
						break; 
					}
				}
				$first = false; 
			}
		}
		
		return $this->_db->execute($sql);
	}

	/**
	 *
	 * Update or Insert data into table
	 * @param Array $data
	 */
	public function update($data) {

		$keyNull = 'Null';
		$keyDefault = 'Default';

		//Determine type of operation
		$sql = "Select Count(*) As cnt From `{$this->_tableName}`";

		//Generate list of ids
		$first = true;
		$ids = '';
		foreach($this->_primaryKeys as $pk) {
			foreach($data as $_key=>$value) {
				if ($_key == $pk->key) {
					if (!$first) $ids .= ",";
					$ids .= "{$pk->key}:{$value}";
					break;
				}
			}
			$first = false;
		}

		$ids = $this->getIds($ids); //Need later for update
		$sql .= " Where " . $ids;

		$rs = $this->_db->getRow($sql);

		if (!$rs) return false;

		$queryType = 'update';
		if ($rs->cnt == 0) $queryType = 'insert';
		unset($rs);

		//Start build sql
		if ($queryType == 'insert') {
			$sql = "Insert Into";
		} else {
			$sql = "Update";
		}
		$sql .= " `{$this->_tableName}`";

		//Create list of values
		if ($queryType == 'insert') {
			//Generate param list for insert
			$first = true;
			$params = '';
			$values = ''; 
			foreach($data as $key=>$value) {
				foreach($this->_columns as $column) {
					if ($column->Field == $key) {
						$addParam = true; 
						//Check for primary key
						foreach($this->_primaryKeys as $pk) {
							if ($column->Field == $pk->key) {
								if ($pk->auto_increment) $addParam = false;
								break;
							}
						}
						if ($addParam) {
							$params .= ((!$first)?',':'')."`{$column->Field}`";
							
							if (empty($value) || strtolower($value) === 'null') {
								if ($column->$keyNull == 'YES') {
									$values .= ((!$first)?',':'')."NULL";
								} else {
									$values .= ((!$first)?',':'')."'{$value}'";
								}
									
							} else {
								$values .= ((!$first)?',':'')."'{$value}'";
							}
							$first = false; 			
						}
					}
				}
			
			}

			//Check for not added 
			foreach($this->_columns as $column){
				$found = false; 
				foreach($data as $key=>$value) {
					if ($column->Field == $key) {
						$found = true; 
						break; 
					}
				}
				if (!$found) {
					//Check if not primary key 
					$addParam = true; 
					foreach($this->_primaryKeys as $pk) {
						if ($pk->key == $column->Field){
							if ($pk->auto_increment) $addParam = false; 
						}
					}
					if ($addParam) {
						$params .= ((!$first)?',':'')."`{$column->Field}`";
						if ($column->$keyNull == 'YES') {
							$values .= ((!$first)?',':'')."NULL";	
						} else {
							$values .= ((!$first)?',':'')."'{$column->$keyDefault}'";
						}
						$first = false; 
					}
					
				} 
			}
			//Add to query
			$sql .= " ({$params}) Values({$values})";
		}

		//Processing values
		if ($queryType == 'update') {
			$sql .= ' Set ';
			$first = true;
			$params = '';
			
			foreach($data as $key=>$value) {
				
				//Search in columns
				foreach($this->_columns as $column) {
					if ($key == $column->Field) {
						
						//Check for primary key 
						$addParam = true; 
						foreach($this->_primaryKeys as $pk) {
							if ($column->Field == $pk->key) {
								if ($pk->auto_increment) $addParam = false; 
								break;  
							}
						}
						
						if ($addParam) {
							$params .= (($first)?'':',')."`{$column->Field}` = ";
							$first = false;  
							if (empty($value) || strtolower($value) == 'null') {
								if ($column->$keyNull == 'YES') {
									$params .= "NULL";
								} else {
									$params .= "'".$value."'";
								}
							} else {
								$params .= "'".$value."'"; 
							}
						}
						break; 	
					}
				}
			}
			$sql .= $params;
		} 

		if ($queryType == 'update') {
			if ($ids) $sql .= " Where " . $ids;
		}
			
		//Get new inserted id
		if ($this->_db->execute($sql)) {
			//Check if table has only one key
			if (count($this->_primaryKeys) == 1) {
				$pk = $this->_primaryKeys[0];
				if ($pk->auto_increment) {
					if ($queryType == 'update') {
						$newId = $data[$pk->key];
					} else {
						$newId = $this->_db->getId();
					}
					return $newId;
				}
			}
			//In other case return always true
			return true;
		}

		//In error case return always false
		return false;
	}
	
	/**
	 * 
	 * Gert count of record by ids
	 * Used for queries with multiple primaruy keys    
	 * @param Array $data
	 * @return Number of record or false in error case 
	 */
	public function getCount($data) {
		$sql = "Select Count(*) As cnt From `{$this->_tableName}`";
		$first = true;  
		foreach($this->_primaryKeys as $pk) {
			foreach($data as $key=>$value) {
				if ($key == $pk->key) {
					$sql .= (($first)?" Where ":" And")." `$key` = '$value'"; 
					break; 
				}
			}
			$first = false; 
		}
		$rs = $this->_db->getRow($sql); 
		if ($rs) {
			return $rs->cnt; 
		}
		return false; 
	}
	
	/**
	 * 
	 * Check if all values except ids are empty 
	 * @param Array $data
	 */
	public function checkEmpty($data) {
		$allEmpty = true; 
		foreach($data as $key=>$value) {
			$itsPK = false; 
			foreach($this->_primaryKeys as $pk) {
				if ($key == $pk->key) {
					$itsPK = true; 
					break; 
				}
			}
			if (!$itsPK) {
				if ($value != '') {
					$allEmpty = false; 
					break; 
				}
			}
		}
		
		return $allEmpty;
	}
	
}