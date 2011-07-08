<?php
namespace modules\base\developer\controllers; 
use system\core\Controller;
/**
 * 
 * Cron jobs controller
 * 
 *  Uses for system cron jobs managements 
 * @author juriem
 *
 */
final class CronController extends Controller {
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::_init()
	 * Initialization
	 */
	protected function _init(){
		$this->_accessLevel = Controller::ACCESS_LEVEL_DEVELOPER; 
		$this->_useLocalTemplates = true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 * List of jobs
	 */
	protected function _default(){
		
		$model = $this->getModel('cronjobs');
		$this->jobs = $model->select();
		$this->addTemplate();
	}
	
	/**
	 * Add or Edit jobs
	 */
	protected function edit(){
		if ($this->_newMode) {
			$id = 0;
		} else {
			$id = $this->_router->params[0];
			if ($id == 0) {
				$this->_newMode = true;
				$id = 0;
			}
		}
		
		$model =& $this->getModel('cronjobs');
		$data = $model->get($id);
		if (!$data) {
			$data = new Object(array('id'=>0));
		}
		$this->data = $data;
		$this->addTemplate();
	}
	
	/**
	 * 
	 * Update job data 
	 * AJAX.action
	 * POST:
	 * 	- data{id,module,interval,is_enabled,description}
	 */
	protected function update(){
		if ($this->_postData->isValid()) {
			$data = $this->_postData->getData();
			$model =& $this->getModel('cronjobs');
			if (($id = $model->update($data)) !== false) {
				echo '{result:true,id:'.$id.'}'; 
			} else {
				echo '{result:false,error_msg:"'.Languages::getJText('global.errors.update_data').'"}'; 
			}
		}
	}
	
	/**
	 * 
	 * Delete job data 
	 * AJAX.action
	 * POST: 
	 * 	- data{id}
	 */
	protected function delete(){
		if ($this->_postData->isValid()){
			$data = $this->_postData->getData();
			$model = $this->getModel('cronjobs');
			if ($model->delete($data['id']) != false) {
				echo '{result:true}';
			} else {
				echo '{result:false,error_msg:"'.Languages::getJText('global.errors.delete_data').'"}'; 
			}
		}
	}
	
	/**
	 * 
	 * Change state for cron job 
	 */
	protected function change_state(){
		if ($this->_postData->isValid()){
			$data = $this->_postData->getData();
			
			$model = $this->getModel('cronjobs');
			if ($model->changeState($data)) {
				echo '{result:true}';
			} else {
				echo '{result:false,error_msg:"Error changing state!"}';
			}
		}
	}
}