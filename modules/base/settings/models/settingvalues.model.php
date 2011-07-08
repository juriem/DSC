<?php
namespace modules\base\settings\models; 
use system\core\Model;
final class SettingValuesModel extends Model {
	
	/**
	 * 
	 * Select values for setting
	 * @param Int $settingId - Id of setting 
	 */
	public function select($settingId) {
		$sql = "Select * From #setting_values Where item_id = $settingId"; 
		return $this->_select($sql); 
	}
	
}