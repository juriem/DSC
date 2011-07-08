<?php
namespace modules\base\settings\controllers\admin;
use system\core\Controller; 
final class SettingsController extends Controller {

	protected function _init(){
		$this->_useLocalTemplates = true;
		$this->_accessLevel = self::ACCESS_LEVEL_ADMIN; 
	}
	
	protected function _default(){

		
		$moduleId = $this->_router->params[0];
		if ($moduleId == $this->_module->getModuleId()) {
			$moduleId = null; 	
		}
		
		//Get settings
		$settingsModel = $this->_module->getModel('settings');
		$items = $settingsModel->select($this->_languageId, $moduleId);
		
		$this->items = $items;
		$this->addTemplate();
		$this->display();
	}

	/**
	 * 
	 * Edit values for settings 
	 */
	protected function edit(){
		if (isset($_POST['id'])) {
			$id = $_POST['id'];
			$model =& $this->getModel('settings');
			$this->setting = $model->getData($id, $this->_languageId);
			
			$this->addTemplate(); 
			$this->display();
		}
	}

	/**
	 *
	 * Update data
	 */
	protected function update() {
		if (isset($_POST['data'])) {
			$data = $_POST['data'];
			$settingsModel = $this->_module->getModel('settings');
			if ($settingsModel->updateValues($data)) {
				echo '{result:true}';
			} else {
				echo '{result:false,error_msg:"'.Languages::getJText('global.errors.update_data').'"}';
			}
		}
	}

	/**
	 * 
	 * Get value for setting
	 * !!! AJAX
	 */
	protected function get_value(){
		if (isset($_POST['id'])) {
			$id = $_POST['id']; 
			$model = $this->getModel('settings'); 
			echo $model->getValue($id, $this->_languageId); 
		}
	}

}