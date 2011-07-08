<?php
namespace modules\base\languages\controllers\admin;
/**
 * 
 * Langugaes controller 
 * @author Juri Em
 *
 */
final class LanguagesController extends AdminController {
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::_init()
	 */
	protected function _init(){
		$this->_useLocalTemplates = true; 
		$this->_urlTasks = array('_default','edit','add');
	}
	
	/**
	 * 
	 * Show list of languages 
	 */
	protected function _default(){
		$languagesModel = $this->_module->getModel('languages');
		$this->languages = $languagesModel->selectAll($this->_languageId);
		$this->addTemplate();
		$this->display();
	}
	
	
	/**
	 * 
	 * Edit language 
	 */
	protected function edit() {
		if ($this->_newMode) {
			$id = 0; 
		} else {
			$id = $this->_router->params[0]; 
		}
		$model = $this->getModel('languages'); 
		$data = $model->get($id); 
		if ($data === false) {
			$data = new Object(); 
			$data->id = 0; 
		}
		$this->data = $data; 
		$this->addTemplate(); 
		$this->display(); 
	}
	
	
	
	/**
	 * 
	 * Update language's data 
	 * !!!AJAX
	 */
	protected function update(){
		extract($_POST); 
		if (isset($data)) {
			$model = $this->getModel('languages'); 
			
			if ($model->checkNotExist($data)) {
				if (($id = $model->update($data)) !== false) {
					echo '{result:true,id:'.$id.'}'; 
				} else {
					echo '{resul:false,error_msg:"'.Languages::getJText('global.errors.update_data').'"}'; 
				}
			} else {
				echo '{resul:false,error_msg:"'.Languages::getJText('global.errors.already_exists').'"}';
			}
		}
	}
	
	/**
	 * 
	 * Delete language
	 * !!!AJAX 
	 */
	protected function delete(){
		if (isset($_POST['id'])) { 
			$id = $_POST['id']; 
			//Processing checking 
			$model = $this->getModel('languages');
			
			if ($model->checkCanDelete($id)) {
				if ($mode->delete($id)) {
					echo '{result:true}';
				} else {
					echo '{result:false,error_msg:"'.Languages::getJText('global.errors.delete_data').'"}'; 
				}
			} else {
				echo '{result:false,error_msg:"'.Languages::getJText('global.errors.cant_delete').'"}'; 
			}
			
		}
	}

}