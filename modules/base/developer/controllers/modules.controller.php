<?php
namespace modules\base\developer\controllers; 
use system\core\Controller; 
/**
 *
 * Modules controller
 * @author Juri Em
 *
 */
final class ModulesController extends Controller {
	
	protected function _init(){
		$this->_useLocalTemplates = true;
		$this->_accessLevel = Controller::ACCESS_LEVEL_DEVELOPER;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 */
	protected function _default(){
		$modulesModel = $this->getModel('modules');
		$this->modules = $modulesModel->select($this->_languageId);
		$this->addTemplate();
	}


	/**
	 *
	 * Edit or add module data
	 */
	protected function edit() {
			
		if ($this->_newMode) {
			$id = 0;
		} else {
			$id = $this->_router->params[0];
		}

		$model = $this->getModel('modules');
		$data = $model->get($id);
		if (!$data) {
			$data = new Object();
			$data->id = 0;
		}
		$this->data = $data;
		$this->addTemplate();
		$this->display();
	}

	/**
	 *
	 * Update data for module
	 */
	protected function update(){
		if (isset($_POST['data'])) {
			$data = $_POST['data'];
			$model = $this->getModel('modules');

			if ($model->checkNotExists($data['id'], $data['module_name'])) {
				if (($id = $model->update($data)) !== false) {
					echo '{result:true,id:'.$id.'}';
				} else {
					echo '{result:false,error_msg:"'.Languages::getJText('global.errors.update_data').'"}';
				}
			} else {
				echo '{result:false,error_msg:"'.Languages::getJText('global.errors.already_exists').'"}';
			}
		}
	}

	/**
	 *
	 * Uninstall module
	 */
	protected function delete(){
		if (isset($_POST['id'])){
			$model = $this->getModel('modules');
			if ($model->delete($_POST['id'])) {
				echo '{result:true}';
			}  else {
				echo '{result:false, error_msg:"'.Languages::getJText('global.errors.delete_data').'"}';
			}
		}
	}


}