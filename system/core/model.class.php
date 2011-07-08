<?php
namespace system\core;
use modules\base\db\system\Db;
/**
 *
 * Base class for model
 * Implements methods for getting data from sql database
 * @author Juri Em
 *
 */
abstract class Model {

	/**
	 * 
	 * Owner module instance
	 * @var Module
	 */
	protected $_moduleInstance;
	
	/**
	 *
	 * Constructor
	 */
	public function __construct() {
		// Get module instance 
		$parts = explode('\\',get_class($this));
		if ($parts[1] == 'base') {
			$moduleName = $parts[2];
		} else {
			$moduleName = $parts[3];
		}
		$this->_moduleInstance =& App::getModule($moduleName);
	}

	/*
	 * System related methods 
	 */
	
	/**
	 *
	 * Get model from module namespace
	 * @param string $modelName
	 */
	protected function &_getModel($modelName) {
		// Return model 
		return $this->_moduleInstance->getModel($modelName);
	}

	
	/*
	 * Query related methods 
	 */
	
	/**
	 *
	 * Wrapper for getRows method
	 * @param String $sql - SQL Query
	 */
	protected function _select($sql) {
		return Db::__()->getRows($sql);
	}

	/**
	 *
	 * Wrapper for getRow method
	 * @param String $sql - SQL Query
	 */
	protected function _get($sql) {
		return Db::__()->getRow($sql);
	}

	/**
	 *
	 * Wrapper for execute method
	 * @param string or array $sql - SQL Query or array of queries
	 */
	protected function _execute($sql) {
		if (is_array($sql)) {
			foreach($sql as $_sql) {
				// Always use transaction
				$this->__start();
				if (Db::__()->execute($_sql) === false) return false;
				$this->__commit();
			}
			return true;
		} else {
			return Db::__()->execute($sql);
		}
	}

	/**
	 *
	 * Get latest inserted id
	 */
	protected function _insertedId() { return Db::getInstance()->getID(); }

	/**
	 *
	 * MYSQL_REAL_ESCAPE
	 * @param mixed $value - Single value or Array of values 
	 */
	protected function _escape($value) { 
		if (is_array($value)) {
			$_buffer = array();
			foreach($value as $item) {
				if (is_string($item)){
					$item = Db::__()->escapeString($item);
				} else if (is_array($item)) {
					$item = $this->_escape($item);
				}
				$_buffer[] = $item; 
			}
			return $_buffer;
		} else {
			return Db::__()->escapeString((string)$value);	
		}
	}

	/**
	 *
	 * Escape array
	 * @param Array $data - Array of data
	 */
	protected function _escapeArray($data) {
		if (is_array($data)) {
			foreach($data as &$item){
				if (is_string($item)){
					$item = Db::__()->escapeString($item);
				} else if (is_array($item)) {
					$item = $this->_escapeArray($item);
				}
			}
		}
		return $data;
	}

	/*
	 * Transactions START
	 * Methods related to transactions.    
	 */
	
	/**
	 *
	 * Start transaction
	 */
	protected function __start(){ Db::__()->startTransaction(); }

	/**
	 *
	 * Commit transaction
	 */
	protected function __commit(){ Db::__()->commitTransaction(); }

	/**
	 *
	 * Rollback transaction
	 */
	protected function __rollback(){ Db::__()->rollbackTransaction(); }

	/*
	 * Transaction END 
	 */

	
	
	
}