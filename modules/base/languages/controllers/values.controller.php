<?php
namespace modules\base\languages\controlles; 
/**
 *
 * Language value controller
 * @author Juri Em
 *
 */
use system\core\Controller;
use system\base\Router; 

final class ValuesController extends Controller {

	protected function _init(){
		$this->_useLocalTemplates = true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 *
	 * Display interface for values
	 * /module_name/#table_name/columns/item_id/use_editor
	 */
	protected function _default(){
			
		$params = new Router($this->_router->getParams(), 'module_name/table_name/columns/item_id/use_editor');

		//Params
		$this->module_name = $params->module_name;
		$this->table_name = $params->table_name;
		$this->holder_id = str_replace("#", "", $this->table_name);
		$columns = $params->columns;
		$item_id = $params->item_id;
		if ($item_id == 'null') $item_id = null; //Not use item params
		$this->item_id = $item_id;
		$this->use_editor = ($params->use_editor === null)?false:true;
		//Processing columns
		$_columns = array();
		$columns = explode(',', $columns);
		foreach($columns as $column) {
			$_column = new Object();
			$parts = explode(':', $column);
			$_column->name = $parts[0];
			$_column->type = $parts[1];
			$_column->label = $parts[2];
			$_columns[] = $_column;
		}
		$this->columns = $_columns;
			
		//Languages
		$model = App::getModel('languages','languages');
		$this->languages = $model->selectAll($this->_languageId);
			
		//Get values
		$model = $this->getModel('values');
		$this->values = $model->selectValues($this->table_name, $this->item_id);
			
		$this->addTemplate();
		$this->display();
	}

	/**
	 *
	 * Show dialog for editing languages values
	 * !!!AJAX
	 */
	protected function dialog(){
		if (isset($_POST['data'])) {
			//$settings = new Object($_POST['settings']); //Convert to object
			$settings = $_POST['data'];
			$this->id = $settings['id'];
			$this->id_name = $settings['id_name'];
			$this->table_name = $settings['table_name'];
			$this->module_name = $settings['module_name'];
				
			//Processing columns
			$_columns = explode(',',$settings['columns']);
				
			$columns = array();
			foreach($_columns as $column) {
				$parts = explode(':',$column);
				$tmp = new Object();
				$tmp->column_name = $parts[0];
				$tmp->value_type = $parts[1];
				$tmp->label_code = $parts[2];
				$columns[] = $tmp;
				unset($tmp);
			}
			$this->columns = $columns;
				
			$valuesModel = $this->_module->getModel('values');
			$this->values = $valuesModel->select($this->id, $this->id_name, $this->table_name);
				
			$languagesModel = $this->_module->getModel('languages');
			$this->languages = $languagesModel->selectAll($this->_languageId);
				
			$this->addTemplate();
			$this->display();
		}
	}

	/**
	 *
	 * Update values
	 */
	protected function update(){
		if (isset($_POST['data'])) {
			
			$data = $_POST['data'];
				
			$valuesModel = $this->_module->getModel('values');
			if ($valuesModel->update($data['values'], $data['id'],$data['table_name'],$data['id_name'])){
				echo '{result:true}';
			} else {
				echo '{result:false,error_msg:"'.Languages::getJText('global.errors.update_values').'"}'; 
			}
		}
	}

	/**
	 *
	 * Get value
	 */
	protected function get(){
		if (isset($_POST['data'])) {
			$data = $_POST['data'];
			$model = $this->getModel('values'); 
			echo $model->getValue($data, $this->_languageId); 	
		}
	}

}