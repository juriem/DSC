<?php
namespace modules\base\db\system;

use modules\base\config\system\Config; //
/**
 * 
 * Wrapper for database class
 * Get information about driver from configuration file 
 * and automatically switch to it  
 * @author juriem
 *
 */
final class Db {
	
	protected static $_instance; 
	public static function &__(){
		if (!isset(self::$_instance)) {
			$databaseDriver = Config::__()->database__driver;
			if ($databaseDriver === null) $databaseDriver = 'mysql'; 
			$databaseClass = __NAMESPACE__.'\\drivers\\'.$databaseDriver.'\\Db';
			self::$_instance = new $databaseClass();
			//self::$_instance =& $databaseClass::__();
		}
		return self::$_instance;
	}
}