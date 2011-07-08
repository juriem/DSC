<?php
namespace modules\base\db\system\drivers\mysql;
use modules\base\db\system\DbDriver;

use modules\base\config\system\Config;
use system\base\Object;
use modules\base\debug\system\Logger;
/**
 *
 * MYSQL Connection class
 * @author juriem
 *
 */
final class Db extends DbDriver{
	
	/**
	 *
	 * Object constructor
	 */
	public function __construct() {
		
		$this->_connected = false;

		$config =& Config::__();

		$host = $config->database__host;
		$user = $config->database__user;
		$password = $config->database__password;
		$database = $config->database__database;
		$this->_tablePrefix = $config->database__table_prefix;
		$this->_connection = mysql_connect($host, $user, $password);

		if ($this->_connection) {
			//Select database
			if (mysql_select_db($database, $this->_connection)) {
				if (mysql_query('Set Names utf8')) {
					//Connected
					$this->_connected = true;
				} else {
					Logger::register('Error set utf ('.mysql_error().')', Logger::DATABASE_MESSAGE);
				}
			} else {
				Logger::register('Error select database ('.mysql_error().')', Logger::DATABASE_MESSAGE);
			}
				
		} else {
			Logger::register('Error connecting to server ('.mysql_error().')', Logger::DATABASE_MESSAGE);
		}

		//if (isset($this->_error)) die('Error connecting to database server!');

	}
	
	/**
	 * 
	 * IDBDriver::_connect() implementation
	 * @param string $server - Server name 
	 * @param string $username - User name 
	 * @param string $password - Password
	 * @param string $database - Database name
	 */
	public function _connect($server, $username, $password, $database) {
		$this->_connection = mysql_connect($server, $username, $password); 
		if ($this->_connection) {
			// Select database 
			if (mysql_select_db($database)) {
				// Set UTF encoding 
				@mysql_query('Set Names utf8'); 
			}
		}
	}

	/**
	 *
	 * Get one row
	 * @param string $sql
	 */
	public function _get($sql) {
		$result = false;
		if ($this->_checkConnected()) {
			
			// Replace prefix
			$sql = $this->_replaceTablePrefix($sql);
			// Register query
			$this->_lastQuery = $sql;
			$link = mysql_query($sql, $this->_connection); 
			if ($link) {
				$result = mysql_fetch_assoc($link);
				if ($result) $result = new Object($result); 
				mysql_free_result($link);
			}
			if (!$result) {
				if (mysql_error() != '') {
					$this->_lastQueryError = mysql_error() . ' ('.mysql_errno().')'; // Register error 
					if ($this->_transactionLevel > 0) $this->rollbackTransaction();  // Checking transaction and rollback if in transaction
				}
			}
		}
		return $result;
	}

	
	/**
	 * 
	 * IDBDriver::_select() implementation 
	 * @param string $sql
	 */
	public function _select($sql) {
		$result = false;
		if ($this->_checkConnected()) {
			$sql = $this->_replaceTablePrefix($sql);
			$this->_lastQuery = $sql;
			$link = mysql_query($sql); 
			if ($link) {
				$result = array();
				while ($r = mysql_fetch_assoc($link)) {
					$result[] = new Object($r);
				}
				mysql_free_result($link);
			}
			
			if (!$result) {
				if (mysql_error() != '') {
					$this->_lastQueryError = mysql_error() . ' (' . mysql_errno() . ')';
					if ($this->_transactionLevel > 0) $this->rollbackTransaction();
				}
			}
			
		}	
		return $result;
	}
	
	/**
	 * 
	 * IDBDriver::_execute() implementation
	 * @param string $sql
	 */
	public function _execute($sql) {
		$result = false;
		if ($this->_checkConnected()) {
			$sql = $this->_replaceTablePrefix($sql); // Prepare table names 	
			$this->_lastQuery = $sql; // Register query
			$result = mysql_query($sql);
			
			if (!$result) {
				if (mysql_error() != '') {
					$this->_lastQueryError = mysql_error() . ' ('.mysql_errno().')'; 
					if ($this->_transactionLevel > 0) $this->rollbackTransaction();
				}
			}
		}
		return $result;
	}

	/**
	 * 
	 * Get last inserted ID 
	 */
	public function _insertedId(){
		if ($this->_checkConnected()) {
			return mysql_insert_id(); 
		}
		return null;
	}

	/**
	 *
	 * Get latest inserted ID
	 */
	public function getID() {
		if ($this->_connected) {
			return mysql_insert_id($this->_connection);
		}
		return null;
	}

	/**
	 *
	 * Escape string
	 * @param String $string - Unescaped string
	 */
	public function escapeString($string) {
		if ($this->_connected) {
			return mysql_real_escape_string($string, $this->_connection);
		}
		return $string;
	}

	/*
	 * Transaction implementation
	 */
	/**
	 *
	 * IDbDriver::_tranStart() implementation
	 */
	public function _tranStart(){
		return $this->execute('Start transaction');
	}

	/**
	 *
	 * IDBDriver::_tranCommit() implementation
	 */
	public function _tranCommit(){
		return $this->execute('Commit');
	}

	/**
	 *
	 * IDBDriver::_tranRollback implementation
	 */
	public function _tranRollback() {
		return $this->execute('Rollback');
	}
}