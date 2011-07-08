<?php
namespace modules\base\settings\models; 
use system\core\Model; 
/**
 * 
 * Setting names model 
 * @author Juri Em
 *
 */
final class SettingNamesModel extends Model {
	
	/**
	 * 
	 * Select names for setting 
	 * @param Int $settingId - ID of setting 
	 */
	public function select($settingId) {
		$sql = "Select * From #setting_names Where setting_id = $settingId"; 
		return $this->_select($sql); 
	}
	
}