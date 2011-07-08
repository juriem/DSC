<?php
namespace modules\base\ajax\system;

use modules\base\languages\system\Languages;

use system\core\SystemClass; // SystemClass base
use system\core\URL; // URL processing class
use system\core\App; // App class 

final class Post extends SystemClass{

	/*
	 * Static - BEGIN
	 */
	protected static $_instance;
	public static function &__() {
		if (!isset(self::$_instance)) self::$_instance = new self();
		return self::$_instance;
	}
	/*
	 * Static - END
	 */

	/*
	 * Dynamic - BEGIN
	 */

	private $_isValid = false;
	private $_holder;

	protected function __construct() {

		parent::__construct(); // Call parent constructor

		if (isset($_POST) && !empty($_POST)) {

			//Checking for files
			$fileUpload = (!empty($_FILES));

			if (isset($_POST['module'])) {
				//All post always used ajax
				$moduleName = $_POST['module'];
				$moduleAction = $_POST['module_action'];
				$module = App::getModule($moduleName);

				// Set base url
				URL::set($_POST['base_url']);
				
				if (isset($_POST['lang']) || isset($_POST['language'])) {
					if (isset($_POST['language'])) $_POST['lang'] = $_POST['language'];
					if ($module != null) {
						// Processing post
						if (isset($_POST['data'])) {
							$this->_isValid = true;
							// Move all data from _POST to
							$data = $_POST['data'];
							foreach($data as $key=>$value) {
								if (!isset($this->_holder)) $this->_holder = array();
								$this->_holder[$key] = $value;

							}
							unset($_POST['data']); // Unset post
						}
						// Start output 
						ob_start();
						$module->execute($moduleAction);
						$content = ob_get_clean();
						if (mb_strlen($content) == 0 && $fileUpload) {
							$content = '{result:false, error_msg:"Bad upload receiver!"}';
						}
						echo $content;
					} else {
						echo '{result:false,error_msg:"Unknown module name!"}';
					}
				} else {
					echo '{result:false,error_msg:"Language not specified!"}';
				}
			} else {
				echo '{result:false,error_msg:"Direct post not allowed!"}';
			}
			exit(); //Dont allow direct post !!!!
		}
	}

	/**
	 *
	 * Check if post is valid
	 */
	public function isValid() {
		return $this->_isValid;
	}

	/**
	 *
	 * Return holder
	 */
	public function getData(){
		return $this->_holder;
	}

	/**
	 *
	 * Checking content of post
	 * @param string $keys - keys to check. Example: id,title, ...
	 */
	public function check($keys) {
		// Check if POST valid
		if (isset($this->_holder)) {
			// Processing keys
			if ($keys != '') {
				$keys = explode(',', $keys);

				$buffer = array();
				foreach($keys as $key) {
					if ($key != '') {
						$buffer[] = trim($key);
					}
				}
				$keys = $buffer;
				// Check number of keys
				if (count($keys) == 0) return false;
				// Compare keys with keys in POST
				foreach($keys as $key) {
					$notFound = true;
					foreach($this->_holder as $_key=>$value) {
						if ($_key == $key) {
							$notFound = false;
							break;
						}
					}
					if ($notFound) return false;
				}

				return true;

			}
		}
		return false;
	}

	/**
	 *
	 * Get value for post
	 * @param string $key
	 */
	public function __get($key) {
		if (isset($this->_holder)) {
			if (key_exists($key, $this->_holder)) return $this->_holder[$key];
		}
		return null;
	}

}