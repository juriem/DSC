<?php
namespace modules\base\developer\controllers; 
use system\core\Controller; 
/**
 *
 * Controller for libraries
 * @author Juri Em
 *
 */
final class LibsController extends Controller {


	protected function _init(){
		$this->_useLocalTemplates = true;
		$this->_accessLevel = Controller::ACCESS_LEVEL_DEVELOPER;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 */
	protected function _default() {

		$libsModel = $this->_module->getModel('libs');
		$this->libs = $libsModel->getList();
		//Show list of libs
		$this->addTemplate();
		$this->display();

	}

	/**
	 *
	 * Add mode
	 */
	protected function add() {
		$this->_newMode = true;
		$this->execute('edit');
	}

	/**
	 *
	 * Edit mode
	 */
	protected function edit() {
		if (isset($this->_newMode)) {
			$id = 0;
		} else {
			$id = $this->_router->params[0];
		}
		
		$libsModel = $this->_module->getModel('libs');
		$data = $libsModel->get($id);
		if (!$data) {
			$data = new Object(); 
			$data->id = 0; 
		}
		$this->data = $data;
		$this->items = $libsModel->getItems($id); 
		$this->addTemplate();
		$this->display();
	}

	/**
	 *
	 * Update via AJAX
	 */
	protected function update() {
		if (isset($_POST['data'])) {	
			$data = $_POST['data'];
			$libsModel = $this->_module->getModel('libs'); 
			if ($id = $libsModel->update($data)) {
				echo '{result:true, id:'.$id.'}';
				return; 	
			}
		}
		echo '{result:false}'; 
	}

	/**
	 *
	 * Delete lib
	 * AJAX
	 */
	protected function delete() {

		if (isset($_POST['id'])) {
				
			$id = $_POST['id'];
				
			//Processing delete
			$libsModel = $this->_module->getModel('libs');
			if ($libsModel->delete($id)) {
				//Deleted
				echo '{result:true}';
				return;
			}
				
		}
		echo '{result:false}';
	}
	
	/**
	 * 
	 * Delete item 
	 * @param Int $id
	 */
	public function delete_item() {
		if (isset($_POST['id'])) {
			$id = $_POST['id']; 
			$libsModel = $this->_module->getModel('libs'); 
			if ($libsModel->deleteItem($id)) {
				echo '{result:true}'; 
				return; 
			}
		}
		echo '{result:false}'; 
	}
	
}