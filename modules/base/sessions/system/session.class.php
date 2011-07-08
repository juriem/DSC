<?php
namespace modules\base\sessions\system;
/**
 * 
 * Session handler
 * @author Juri Em
 *
 */
final class Session {
	 
	private $life_time;
	private $_model; 

	/**
	 * Constructor
	 */
	public function __construct(){
		$this->_model = App::getModel('sessions', 'sessions');
		session_set_save_handler(array(&$this, 'open'),
		array(&$this, "close"),
		array(&$this, "read"),
		array(&$this, "write"),
		array(&$this, "destroy"),
		array(&$this, "gc"));
	}
	
	/**
	 * 
	 * Get session 
	 * @param unknown_type $varName
	 */
	public function __get($varName) {
		// Ecrypt session 
		$varName = md5(Config::getInstance()->system->app_id.$varName);
		
		if (isset($_SESSION[$varName])) return $_SESSION[$varName];
		return null;
	}
	
	/**
	 * Open session
	 * @param string $save_path
	 * @param string $session_name
	 */
	public function open($save_path, $session_name) {
		global $sess_save_path;
		$sess_save_path = $save_path;
		return true;
	}

	/**
	 * Close session
	 *
	 */
	public function close(){

		return true;
	}

	/**
	 * Read data from session
	 * @param unknown_type $id
	 */
	public function read($id) {
		$data = $this->_model->get($id);
		if ($data !== false) {
			return $data->session_data; 
		}
		return null;
	}

	/**
	 * Write data to session
	 * @param unknown_type $id
	 * @param unknown_type $data
	 */
	public function write( $id, $data ) {
		$tmp = array(); 
		$tmp['session_id'] = $id;
		$tmp['session_data'] = $data;
		$tmp['expires'] = time() + ini_get('session.gc_maxlifetime');
		$this->_model->update($tmp);
		return TRUE;
	}

	/**
	 * Destroy session
	 * @param Mixed $id
	 */
	public function destroy( $id ) {
		$this->_model->delete($id); 
		return TRUE;

	}

	/**
	 * Garbage collector
	 */
	public function gc() {
		$this->_model->deleteExpired();
		return true;
	}


}