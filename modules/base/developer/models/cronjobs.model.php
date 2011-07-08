<?php
namespace modules\base\developer\models;
use system\core\Model;  
/**
 * 
 * Cron jobs model 
 * @author juriem
 *
 */
final class CronJobsModel extends Model {
	
	/**
	 * 
	 * Get list of jobs 
	 */
	public function select(){
		$sql = "Select * From #cron_jobs"; 
		return $this->_select($sql); 
	}
	
	/**
	 * 
	 * Get data 
	 * @param int $id
	 */
	public function get($id) {
		$sql = "Select * From #cron_jobs Where id=$id";
		return $this->_get($sql);
	}
	
	/**
	 * 
	 * Insert or Update data
	 * @param array $data
	 */
	public function update($data) {
		$data['description'] = $this->_escape($data['description']); 
		$table = new DBTable('#cron_jobs'); 
		$id = $table->update($data);
		return $id;
	}
	
	/**
	 * 
	 * Delete cron job
	 * @param int $id
	 */
	public function delete($id) {
		$id = (int)$id;
		$sql = "Delete From #cron_jobs Where id = $id"; 
		return $this->_execute($sql);
	}
	
	/**
	 * 
	 * Change state for cron job 
	 * @param array $data : Array of values {id, is_enabled}
	 */
	public function changeState($data) {
		$data['is_enabled'] = abs($data['is_enabled']-1);
		$sql = "Update #cron_jobs Set is_enabled = {$data['is_enabled']} Where id = {$data['id']}";
		return $this->_execute($sql);		
	}
	
}