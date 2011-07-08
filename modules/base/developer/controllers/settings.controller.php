<?php
namespace modules\base\developer\controllers; 
use system\core\Controller; 
/**
 * 
 * Controller for settings 
 * @author Juri Em
 *
 */
final class SettingsController extends Controller {
	
	protected function _init(){
		$this->_useLocalTemplates = true;
		$this->_accessLevel = self::ACCESS_LEVEL_DEVELOPER; 
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 * Interface for settings 
	 */
	protected function _default(){
		//Get data for filter 
		$model = $this->getModel('settings'); 
		//$settingsModel = App::getModel('settings', 'settings');
		$this->modules = $model->selectModules(); 
		$this->addTemplate(); 
		$this->display(); 
	}
	
	
	/**
	 * 
	 * Get list of settings 
	 * AJAX
	 */
	protected function get_list(){
		$moduleId = ''; 
		if (isset($_POST['data'])) {
			$data = $_POST['data'];
			$moduleId = $data['module_id']; 
		}
		
		//$settingsModel = App::getModel('settings', 'settings');
		$model = $this->getModel('settings');  
		//$this->settings = $settingsModel->selectForDev($moduleId);
		$this->settings = $model->select($moduleId);
		$this->addTemplate(); 
		$this->display();
	}
	
	
	/**
	 * 
	 * Edit setting 
	 */
	protected function edit() {
		if ($this->_newMode) {
			$id = 0; 
		} else {
			$id = $this->_router->params[0]; 
		} 
		//Load modules 
		$modulesModel = $this->getModel('modules');
		//$modulesModel = App::getModel('modules', 'modules'); 
		$this->modules = $modulesModel->selectNames();
		unset($modulesModel); 
		$model = $this->getModel('settings'); 
		//$settingsModel = App::getModel('settings', 'settings'); 
		$data = $model->get($id);
		if (!$data) {
			$data = new Object(); 
			$data->id = $id;
		}
		$this->data = $data;
		$this->addTemplate(); 
		$this->display(); 
	}
	
	/**
	 * 
	 * Load setting value UI
	 * AJAX!!! 
	 */
	protected function get_value() {
		if (isset($_POST['data'])) {	
			$this->id = $_POST['data']['id']; 
			$this->type = $_POST['data']['type'];
			$this->addTemplate();
			$this->display();
		}
	}
	
	
	/**
	 * 
	 * Update data 
	 * AJAX
	 */
	protected function update(){
		if (isset($_POST['data'])) {
			$data = $_POST['data'];
			$model = $this->getModel('settings'); 
			if (($id = $model->update($data)) !== false) {
				echo '{result:true,id:'.$id.'}';
			} else {
				echo '{result:false,error_msg:"'.Languages::getJText('global.errors.update_data').'"}'; 
			}
		}
	}
	
	/**
	 * 
	 * Delete setting
	 * AJAX
	 */
	protected function delete(){
		if (isset($_POST['id'])) {
			$id = $_POST['id']; 
			
			$model = $this->getModel('settings'); 
			//ettingsModel = App::getModel('settings', 'settings'); 
			if ($model->delete($id)) {
				echo '{result:true}'; 
			} else {
				Logger::dump(Logger::DATABASE_MESSAGE);
				echo '{result:false,error_msg:"'.Languages::getJText('global.errors.delete_data').'"}'; 		
			}
		} 
	}
	
}