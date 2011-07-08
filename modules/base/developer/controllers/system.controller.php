<?php
namespace modules\base\developer\controllers; 
use system\core\Controller; 
/**
 * 
 * System configuration controller 
 * @author Juri Em
 *
 */
final class SystemController extends Controller {
	
	protected function _init(){
		$this->_useLocalTemplates = true;
		$this->_accessLevel = self::ACCESS_LEVEL_DEVELOPER;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 * System configuration 
	 */
	protected function _default(){
		$this->addTemplate();
	}
	
	/**
	 * 
	 * Generate uniqID
	 */
	protected function generate_guid(){
		$appId = md5($_SERVER['HTTP_HOST']).'-'.uniqid().'-'.md5(time()); 
		echo '{result:true,app_id:\''.$appId.'\'}'; 
	}
	
	/**
	 * 
	 * Update configuration for system
	 */
	protected function update_configuration(){
		if (isset($_POST['data'])) {
			
			 
			$data = $_POST['data']; 
			$section = $data['section']; 
			$values = $data['values'];
			$xml = simplexml_load_file(ROOT.DS.'config'.DS.'config.xml'); 
			foreach($values as $key=>$value) {
				$xml->$section->$key = $value;	
			}
			//Save data 
			$f = fopen(ROOT.DS.'config'.DS.'config.xml', 'w'); 
			fwrite($f, $xml->asXml()); 
			fclose($f);
			echo '{result:true}'; 
			return; 
		}
	}
}
