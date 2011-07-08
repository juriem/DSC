<?php
declare(ENCODING='utf-8');

use modules\base\settings\system\Settings;
use modules\base\html\system\PageHeader;
use modules\base\debug\system\Timer;
use modules\base\debug\system\Logger;
use modules\base\ajax\system\Post;
use modules\base\languages\system\Lang;
use system\core\App;
use system\base\Router;
use system\core\URL;
use modules\base\config\system\Config; 
use modules\base\sessions\system\Session;
use modules\base\sessions\system\SessionHandler;
use modules\base\languages\system\Languages;
use modules\base\db\system\Db;
use modules\base\scripts\system\Scripts;


/**
 * Start application
 */
//DEFINES
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('APP_STARTED', true);
define('NL',"\r\n");

/**
 * 
 * Autoload function for system classes 
 * @param string $className
 */
function __autoload($className) {
	$className = strtolower($className);
	$filePath = ROOT.DS.str_replace('\\', DS, $className).'.class.php';
	if (file_exists($filePath)) {
		require $filePath;
		return;
	}
	// interfaces 
	$filePath = ROOT.DS.str_replace('\\', DS, $className).'.interface.php';
	if (file_exists($filePath)) {
		require $filePath;
		return;
	}
}

 

/*
 * Base initalization
 */

// Init error reporting 
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$timer = new modules\base\debug\system\Timer(); 

//Timer::__(); // Init timer

if (function_exists('mb_internal_encoding')) mb_internal_encoding('utf-8'); // Init multibyte settings


date_default_timezone_set(Config::__()->system__timezone); // Init system timezone 

/*
 * Processing session
 */
if (Config::__()->system__use_dbsession == "yes" && Db::__()->connected) {
	if (class_exists('session', true)) {
		$session = new Session();
	}
}

//Tricks for flash
if (isset($_POST['PHPSESSION'])) {
	session_id($_POST['PHPSESSION']);
}
session_start();

//Debuging
//if (Config::__()->system__debug_mode == 'yes') {	
//	$timer =& Timer::getInstance();
//	$timer->start();
//}


/*
 * Checking if use command line. Used only for cron module 
 */

if (isset($argv)){
	App::executeModule('cron', 'default/_default');
	exit; 
}




/*
 * Init URL
 */
URL::__();


// Init languages system 
Languages::__(); 

/*
 * Init post system	
 */
Post::__(); // If was made post then control switched to ajax module and 



// Get main router 
$router =& App::getRouter(); 

//Start application
$_module = $router->key_0;
switch($_module) {
	case '_files':
		$module = App::getModule('files');
		$url = $router->getURL(2); //Get url without _files/files
		$url = 'default/_default'.$url;
		$module->execute($url);
		exit;

	case '_modules':
		//Direct access to module
		$module = $router->key_1;
		$moduleInstance = App::getModule($module);
		if ($moduleInstance != null) {
			// Checking if direct access allowed
			$moduleUrl = $router->getURL(2);
			$moduleInstance->execute($moduleUrl);
			exit;
		} else {
			die('Module "'.$module.'" doesn\'t exist!'); 
		}
		break;
	case '_admin':
		//Administrative module
		$module = 'admin';
		$moduleInstance = App::getModule('admin');
		if ($moduleInstance != null) {
			ob_start();
			$moduleUrl = $router->getURL(1);
			$moduleUrl = 'default'.$moduleUrl;
			$moduleInstance->execute($moduleUrl);
		} else {
			die('Admin module wasn\'t installed!');
		}
		break;
	case '_developer':
		$module = 'developer';
		$moduleInstance = App::getModule('developer');
		if ($moduleInstance != null) {
			ob_start();
			$moduleUrl = $router->getURL(1);
			$moduleUrl = 'default'.$moduleUrl;
			$moduleInstance->execute($moduleUrl);
		} else {
			die('Developer module not installed!');
		}
		break;
	default:
		// Load site module
		$moduleUrl = $router->getURL();
		ob_start();
		App::executeModule('main', 'default/_default' . $router->getURL());
}

$html = ob_get_clean();

$html = PageHeader::__()->render($html);

//Rearrange scripts and styles on HTML
$html = Scripts::__()->parse($html);
//Replace base url
$html = URL::__()->parse($html);


 

//Add timer information
if (Config::__()->system__debug_mode == 'yes') {
	Logger::register('TOTAL_TIME::'.$timer->stop(), Logger::DEBUG_MESSAGE);
	Logger::register('MEMORY_USED::'.memory_get_usage(), Logger::DEBUG_MESSAGE);
}

var_dump(Config::__());

//Output html
echo $html;


if (Config::__()->system__debug_mode == 'yes') {
	var_dump(Logger::dump());
	//App::executeModule('debug', 'dumper');
}

//echo Timer::__()->stop();
 

