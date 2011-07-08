<?php
namespace modules\base\developer\controllers;
use system\core\Controller;
final class AdminMenuController extends Controller {
	
	protected $_controllerName = 'adminmenu';
	protected $_accessLevel = Controller::ACCESS_LEVEL_DEVELOPER; 
	protected $_useLocalTemplates = true; 
	
	
	protected function _init(){
		$this->_useLocalTemplates = true;
		$this->_accessLevel = Controller::ACCESS_LEVEL_DEVELOPER;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 * 
	 * List of menus 
	 */
	protected function _default(){
		$adminMenusModel = $this->getModel('menus'); 
		$this->menus = $adminMenusModel->selectAdminMenus($this->_languageId); 
		
		$this->addTemplate(); 
		$this->display();
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
	 * Display edit interface 
	 */
	protected function edit() {
		
		//Get modules 
		$modulesModel = $this->getModel('modules'); 
		$modules = $modulesModel->select($this->_languageId);
		//Filter administrable modules 
		if ($modules) {
			$_buffer = array(); 
			foreach($modules as $module) {
				//try to get admin controller
				$foundIt = true; 
				$path = ROOT.DS.'modules'.DS.'system'.DS.'mod_'.$module->module_name.DS.'controllers'.DS.'admin.controller.php'; 
				if (!file_exists($path)) {
					$path = ROOT.DS.'modules'.DS.'mod_'.$module->module_name.DS.'controllers'.DS.'admin.controller.php';
					if (!file_exists($path)) {
						$foundIt = false; 
					}
				}
				
				if ($foundIt) $_buffer[] = $module;
			}
			$modules = $_buffer; 
		}
		$this->modules = $modules; 
		
		unset($modulesModel); 
		
		//Get menu data 
		if ($this->_newMode) {
			$id = 0; 
			if (isset($this->_router->params[0])) {
				$pId = $this->_router->params[0]; 
			} else {
				$pId = 0; 
			}
		} else {
			$id = $this->_router->params[0]; 
		}

		$model = $this->getModel('menus');
		$data = $model->getAdmin($id);
		
		if (!$data) {
			$data = new Object();
			$data->id = 0; 
			$data->p_id = $pId; 
			$data->is_admin = 1; 
		}
		$this->data = $data;
		$this->addTemplate(); 
		$this->display(); 
	}
	
	//AJAX
	/**
	 * 
	 * Update data for menu 
	 */
	protected function update() {
		if (isset($_POST['data'])) {
			$data = $_POST['data']; 
			$model = $this->getModel('menus'); 
			if ($model->update($data)) {
				echo '{result:true}';
			} else {
				echo '{result:false,error_msg:"'.Languages::getJText('global.errors.update_data').'"}'; 
			}
		}
	}
	
	/**
	 * 
	 * Delete menu 
	 */
	protected function delete() {
		if (isset($_POST['data'])) {
			$id = $_POST['data']['id']; 
			$model = $this->getModel('menus'); 
			if ($model->checkCanDelete($id)) {
				if ($model->delete($id)) {
					echo '{result:true}'; 
				} else {
					echo '{result:false,error_msg:"'.Languages::getJText('global.errors.delete_data').'"}'; 
				}
			} else {
				echo  '{result:false,error_msg:"'.Languages::getJText('global.errors.cant_delete').'"}'; 
			}
		}
	}
	
	/**
	 * 
	 * Change sort index 
	 */
	protected function change_sort_index() {
	
	}
	
}