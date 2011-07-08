<?php
namespace modules\base\languages\system;

use modules\base\sessions\system\SessionHandler; // Session handler class 
use system\base\Router; // Router class 
use system\core\SystemClass; // Base for system classes 
use system\core\App; // Application class
use system\core\URL; // Main URL class  
use modules\base\config\system\Config; //Configuration module
use modules\base\db\system\DBTable; // Database table class

/**
 * 
 * Система управления языками
 * - определение текущего языка по данным _POST или _GET
 * - получение переводов по коду 
 * @author juriem
 *
 */
final class Languages extends SystemClass {


	/**
	 *
	 * Class constructor
	 */
	protected function __construct(){
		parent::__construct();


		/*
		 * Инициализация системы языков
		 *
		 */
		if (isset($_POST['lang']) || isset($_POST['language'])) {
			// Processing in post mode
			if (isset($_POST['language'])) $_POST['lang'] = $_POST['language'];
			$this->_currentLanguage = $_POST['lang'];
		} else {
			// Processing in normal mode
			$url = '';
			if (isset($_GET['url'])) $url = $_GET['url'];
			$router = new Router($url, 'language_url_code/module');

			//Processing language
			$langIsFirst = false;
			if ($router->language_url_code != null) {
				//Checking language
				$model = $this->_getModel('languages');
				$languageId = $model->getId($router->language_url_code);
					
				if ($languageId != null) {
					$langIsFirst = true;
					//Check if not admin or developer
					if (!in_array($router->module, array('_admin','_developer'))) {
						//Check if language enabled
						if ($model->checkEnabled($languageId)) {
							$this->setCurrent($router->language_url_code);
							SessionHandler::__()->public_lang = $router->language_url_code;
						} else {
							//Get default
							$url_code = $this->getSystemDefault(); // Get default language
							if ($url_code == null) die('Language system is broken'); // Stop system if not found languages
							header('Location:'.$router->getURL(1)); //Redirect to default
							exit;
						}
					} else {
						//Set any language
						//TODO: add checking for enabled
						$this->setCurrent($router->language_url_code);
						if ($router->module == '_admin') {
							SessionHandler::__()->admin_lang = $router->language_url_code;
						} elseif ($router->module == '_developer') {
							SessionHandler::__()->dev_lang = $router->language_url_code;
						}
					}
					//Remove language from url
					URL::__()->add($router->language_url_code);
					$_GET['url'] = $router->getURL(1);
					
				}
			}


			// Init global router
			App::getRouter()->_useLanguage = $langIsFirst;

			//Check if there are not language
			if (!$langIsFirst) {

				if ($router->module == '_admin') {

					//Set current language from default for admin
					if (SessionHandler::__()->admin_lang == null){
						$this->setCurrent($this->getSystemDefault(self::LANG_AREA_TYPE_ADMIN));
						SessionHandler::__()->admin_lang = $this->getCurrent();
					} else {
						$this->setCurrent(SessionHandler::__()->admin_lang);
					}
				} elseif ($router->module == '_developer') {
					//Set current language from default for developer
					if (SessionHandler::__()->dev_lang == null) {
						$this->setCurrent($this->getSystemDefault(self::LANG_AREA_TYPE_DEVELOPER)); 
						SessionHandler::__()->dev_lang = $this->getCurrent();
					}else {
						$this->setCurrent(SessionHandler::__()->dev_lang);
					}
				} else {
					//Set current language from default
					if (SessionHandler::__()->public_lang == null) {
						$this->setCurrent($this->getSystemDefault()); 
						SessionHandler::__()->public_lang = $this->getCurrent();
					} else {
						$this->setCurrent(SessionHandler::__()->public_lang); 
					}
				}
			}
		}

	}


	/**
	 *
	 * Default system language
	 * @var string
	 */
	protected $_defaultLanguage = null;
	/**
	 *
	 * Current system language
	 * @var string
	 */
	protected $_currentLanguage = null;
	/**
	 *
	 * Current system language id
	 * @var int
	 */
	private $_currentLanguageId;

	/**
	 *
	 * Public language type
	 * @var string
	 */
	const LANG_AREA_TYPE_PUBLIC = 'public';
	/**
	 *
	 * Admin language type
	 * @var string
	 */
	const LANG_AREA_TYPE_ADMIN = 'admin';
	/**
	 *
	 * Developer language type
	 * @var string
	 */
	const LANG_AREA_TYPE_DEVELOPER = 'developer';


	/**
	 *
	 * Set default language
	 * @param string $type - public - use for all users, admin - used for admin area, 'developer' - used for developer
	 */
	public function setSystemDefault($type=self::LANG_AREA_TYPE_PUBLIC) {
		$defaultField = 'is_default'; // Set checking field for public
		// Check if type is admin or developer language
		if ($type != self::LANG_AREA_TYPE_PUBLIC) {
			$defaultField = 'is_' . $type . '_default'; // Set checking field for admin of developer
		}
		$model = $this->_getModel('languages');
		$urlCode = $model->getDefault($defaultField);
		$this->_currentLanguage = $urlCode;
	}

	/**
	 *
	 * Set current language
	 * @param string $urlCode - Code of language
	 */
	public function setCurrent($urlCode) {
		$this->_currentLanguage = $urlCode;
		$model = $this->_getModel('languages');
		$this->_currentLanguageId = $model->getId($urlCode);
	}

	/**
	 *
	 * Get current language
	 */
	public function getCurrent(){
		return $this->_currentLanguage;
	}

	public function getCurrentId(){
		return $this->_currentLanguageId;
	}

	/**
	 *
	 * Get system default language
	 * @param string $type - Type of language
	 */
	public function getSystemDefault($type = self::LANG_AREA_TYPE_PUBLIC){

		$defaultField = 'is_default'; // Set checking field for public
		// Check if type is admin or developer language
		if ($type != self::LANG_AREA_TYPE_PUBLIC) {
			$defaultField = 'is_' . $type . '_default'; // Set checking field for admin of developer
		}
		$model = $this->_getModel('languages');
		$urlCode = $model->getDefault($defaultField);
		return $urlCode;
	}

	/**
	 *
	 * Cache for texts
	 * @var
	 */
	private $_textCache;

	/**
	 *
	 * Preloading all text for modules
	 * @param string $modules
	 */
	public function preloadTexts($modules) {
		$languageId = $this->_currentLanguageId;
		$model = $this->_getModel('tokenvalues');
		$values = $model->selectValues($languageId, $modules);
		if ($values){
			foreach($values as $text) {
				$_text = new Object(array(
					'language_id'=>$languageId,
					'token'=>$text->module_name.'.'.$text->group_name.'.'.$text->value_name,
					'value'=>$text->value
				));
				if (!isset($this->_textCache)) $this->_textCache = array();
				$this->_textCache[] = $_text;
			}
		}
	}

	/**
	 *
	 * Get text
	 * @param string $tokenCode
	 * @param string $defaultValue
	 * @param string $lang
	 */
	public function getText($tokenCode, $defaultValue='', $lang='') {

		$text = '{'.$tokenCode.'}';
			
		//Get languageId
		$languageId = null;
		if ($languageId === null) {
			$languageId = $this->_currentLanguageId;
		}

		//Checking cache
		if (!isset($this->_textCache)) $this->_textCache = array();
			
		foreach($this->_textCache as $_text) {
			if ($_text->language_id == $languageId && $_text->token == $tokenCode) {
				return $_text->value;
			}
		}


		//Processing token's code
		$tmp = explode('.', $tokenCode);
		if (isset($tmp[0])) $moduleName = $tmp[0];
		if (isset($tmp[1])) $groupName = $tmp[1];
		if (isset($tmp[2])) $valueName = $tmp[2];

		if (!isset($moduleName) || !isset($groupName) || !isset($valueName)) {
			//Error - bad code format
			return '!{bad_code_format}!';
		}

		if ($moduleName == 'global') {
			$moduleId = null;
		} else {
			$model =& App::getModel('modules', 'modules');
			$moduleId = $model->getId($moduleName);
			if ($moduleId === null) return '!{unknow_module_name}!';
			unset($model);
		}
		/*
		 * Create code hash and get token id
		 *
		 */
		$codeHash = md5($groupName.'.'.$valueName);
		$model =& $this->_getModel('tokens');
		$tokenId = $model->getIdByHash($codeHash, $moduleId);

		unset($model);
			
		if ($tokenId !== false) {
			//Get value
			$model =& $this->_getModel('tokenvalues');
			$value = $model->getValue($tokenId, $languageId);
			unset($model);
			$valueExists = false;
			if ($value !== false){
				if ($value !== '' && $value !== null) {
					$text = $value;
					$valueExists = true;
				}
			}
			if (!$valueExists) $text = '?'.$text.'?';

		} else {
			//Create new token
			if (Config::__()->system->auto_create_tokens == 'yes') {
				$table = new DBTable('#tokens');
				$data = array();
				$data['id'] = 0;
				$data['group_name'] = $groupName;
				$data['value_name'] = $valueName;
				$data['code_hash'] = $codeHash;
				if ($moduleId !== null) $data['module_id'] = $moduleId;
				$tokenId = $table->update($data);

				if ($defaultValue !== '') {

					if ($lang != '') {
						$model =& $this->_getModel('languages');
						$languageId = $model->getId($lang);
					}
					$table = new DBTable('#token_values');
					$data = array();
					$data['item_id'] = $tokenId;
					$data['language_id'] = $languageId;
					$data['value'] = $defaultValue;
					if (!$table->update($data)) return '!'.$text.'!';
					$text = $defaultValue;
				}
			}
		}

		//Store value in cache
		$_text = new Object();
		$_text->language_id = $languageId;
		$_text->token = $tokenCode;
		$_text->value = $text;
		$this->_textCache[] = $_text;

		return $text;
	}

	/**
	 *
	 * Get text for Java
	 * @param String $tokenCode
	 * @param Default value $defaultValue
	 */
	public function getJText($tokenCode, $defaultValue='', $lang = '') {
		$result = $this->getText($tokenCode, $defaultValue, $lang);
		$result = str_replace("\n", "\\n", $result);
		return $result;
	}

}