<?php
namespace modules\base\developer\controllers; 
use system\core\Controller; 
/**
 * 
 * Default controller for menu 
 * @author Juri Em
 *
 */
final class MenuController_Developer extends Controller {

	protected function _init(){
		$this->_useLocalTemplates = true; 
		$this->_accessLevel = Controller::ACCESS_LEVEL_DEVELOPER;
	}
	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 */
	protected function _default() {
		//Get list of groups 
		$model = $this->getModel('menus'); 
		$this->menus = $model->select(); 
		$this->addTemplate();
	}
	
	/**
	 * 
	 * Add new menu 
	 */
	protected function add(){
		$this->_newMode = true; 
		$this->execute('edit'); 
	}
	
	/**
	 * 
	 * Edit menu 
	 */
	protected function edit(){
		if ($this->_newMode) {
			$id = 0; 
			$p_id = 0; 
			if (isset($this->_router->params[0])) $p_id = $this->_router->params[0]; 
		} else {
			$id = $this->_router->params[0]; 
		}
		$model = $this->getModel('menus'); 
		$data = $model->get($id); 
		if (!$data) {
			$data = new Object();
			$data->id = 0;  
			$data->p_id = $p_id; 
			$data->is_admin = 0;
		}
		
		$this->data = $data; 
		$this->addTemplate();	
	}
	
	/**
	 * 
	 * Update data for menu
	 */
	protected function update(){
		if (isset($_POST['data'])) {
			$data = $_POST['data']; 
			$model = $this->getModel('menus'); 
			if (($id = $model->updateMenu($data)) !== false) {
				echo '{result:true,id:'.$id.'}'; 
			} else {
				echo '{result:false,error_msg:"'.Languages::getJText('global.errors.update_data').'"}'; 
			}
		}
	}
	
}