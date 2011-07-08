<?php
namespace modules\base\db\system;


use modules\base\db\system\interfaces\IDbDriver; // Database driver interface 
use modules\base\config\system\Config; // System configuration 
/**
 * 
 * Database driver base class
 * @author juriem
 *
 */
abstract class DbDriver implements IDbDriver{
	
	/**
	 * 
	 * Connection with database 
	 * @var resource
	 */
	protected $_connection;
	
	
	/*
	 * Query related 
	 */
	/**
	 * 
	 * Last executed query
	 * @var string
	 */
	protected $_lastQuery='';
	/**
	 * 
	 * Last execution error
	 * @var string
	 */ 
	protected $_lastQueryError='';
	
	
	/*
	 * Transaction related 
	 */
	/**
	 * 
	 * Transaction level
	 * If in transaction, value of transaction level bigger then zero
	 * @var int
	 */
	protected $_transactionLevel = 0;
	 
	/**
	 * 
	 * Table prefix. Table prefix used for store more than one database in one.  
	 * @var string
	 */
	protected $_tablePrefix=''; 
	
	/*
	 * Protected methods 
	 */
	/**
	 * 
	 * Checking if connected to database 
	 */
	protected function _checkConnected(){
		if (isset($this->_connection)) {
			return true;
		} 
		return false;
	}
	
	/**
	 * 
	 * Replace systen # with table prefix 
	 * @param string $sql - SQL query
	 */
	protected function _replaceTablePrefix($sql) {
		return str_replace('#', $this->_tablePrefix, $sql);
	}
	
	/**
	 * 
	 * Constructor
	 */
	public function __construct() {
		// Get data from configuration 
		$host = $config->database->host;
		$user = $config->database->user;
		$password = $config->database->password;
		$database = $config->database->database;
		$this->_tablePrefix = $config->database->table_prefix;
		
		if (!$this->_connect($host, $user, $password, $database)) {
			//TODO:Register error information 	
		}
	}
	
	/**
	 * 
	 * Magic getters
	 * @param string $varName - Allowed types 
	 */
	public function __get($varName) {
		if (in_array($varName, array('lastQuery', 'lastQueryError', 'connected','transactionLevel','tablePrefix'))) {
			if ($varName == 'connected') {
				return $this->_checkConnected();
			} else {
				$varName = '_'.$varName;
				return $this->$varName;
			}
		} 
	}
	
	
	/**
	 * 
	 * Get one row 
	 * @param string $sql - SQL query 
	 */
	public function getRow($sql) {
		return $this->_get($sql);
	}
	
	public function getRows($sql) {
		return $this->_select($sql);
	}
	
	public function execute($sql) {
		return $this->_execute($sql);
	}
	
	
	/*
	 * Transactions related 
	 */
	/**
	 * 
	 * Start transaction 
	 */
	public function startTransaction(){
		if ($this->_checkConnected()) {
			$this->_transactionLevel++;
			return $this->_tranStart(); 
		}
		return false;
	}
	
	/**
	 * 
	 * Commit transaction 
	 */
	public function commitTransaction(){
		if ($this->_checkConnected()) {
			$this->_transactionLevel--;
			return $this->_tranCommit(); 
		}
		return false;
	}
	
	/**
	 * 
	 * Rollback transaction
	 */
	public function rollbackTransaction(){
		if ($this->_checkConnected()) {
			$this->_transactionLevel = 0;
			return $this->_tranRollback();
		}
		return false;
	}
}