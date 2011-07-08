<?php
namespace modules\base\db\system\interfaces;

interface IDbDriver {

	/**
	 * 
	 * Connected to databse server
	 * @param string $server
	 * @param string $username
	 * @param string $password
	 * @param string $database
	 */
	function _connect($server,$username,$password, $database);
	
	/**
	 * 
	 * Get one row 
	 */
	function _get($sql);
	
	/**
	 * 
	 * Get multiple rows 
	 */
	function _select($sql); 
	
	/**
	 * 
	 * Execute query 
	 * @param string $sql
	 */
	function _execute($sql); 
	
	/**
	 * 
	 * Get inserted ID 
	 */
	function _insertedId(); 
	
	/*
	 * Transaction related 
	 */
	function _tranStart();
	function _tranCommit();
	function _tranRollback();
	
}