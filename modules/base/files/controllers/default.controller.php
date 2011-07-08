<?php
namespace modules\base\files\controllers;

use system\core\Controller;
use system\core\URL;

/**
 *
 * Default controller for file system module
 * @author Juri Em
 *
 */
final class DefaultController extends \system\core\Controller {

	/**
	 * (non-PHPdoc)
	 * @see Controller::_default()
	 *
	 * Get files from outside
	 * For accessing from outside use /files/<module name>/<type: images,css,js,assets>
	 * Global files from directory css and images accessed directly and redirected bby .htaccess
	 */
	protected function _default() {
		/*
		 * Checking for parameters. If no parameters - return error 403
		 *
		 */
		if ($this->_router->params === null) {
			header('HTTP/1.0 403 Forbidden');
			return;
		}
		$params = $this->_router->params;

		// Get module name
		$moduleName = array_shift($params);
		
		//Checking module name
		if ($moduleName === null) {
			header('HTTP/1.0 403 Forbidden');
			return;
		}
		$url = (($moduleName == '_global_')?'':$moduleName) . '/';

		// Get type of requested resources
		$type = array_shift($params);
		if ($type === null) {
			header('HTTP/1.0 403 Forbidden');
			return;
		}
		
		if ($moduleName != '_global_') {
			if ($type == 'images') {
				// Load data from storage
				$url = 'storage/system/'.$moduleName.'/';
			} else {
				// Load data from module
				// Find module
				$_path = 'modules/'.$moduleName;
				$foundIt = true;
				if (!file_exists(ROOT.DS.str_replace('/', DS, $_path))) {
					// Try system
					$_path = 'modules/base/'.$moduleName;
					if (!file_exists(ROOT.DS.str_replace('/', DS, $_path))) $foundIt = false;
				}
				if (!$foundIt) {
					// Error

				} else {
					$url = $_path . '/' . $type . '/';
				}
				
			}
		} else {
			// Access to global resources
			$url .= $type . '/';
		}

		
		// Checking rest
		// Empty request
		if (count($params) == 0) {
			header('HTTP/1.0 403 Forbidden');
			return;
		}

		// Add parameters to url
		$url .= implode('/', $params);
		
		// Get type
		if (!preg_match('/([a-z\/]*)\.([a-z0-9]+)/i', $url, $matches)) {
			header('HTTP/1.0 403 Forbidden');
			return;
		}

		// Restore filepath
		$fileType = $matches[2];


		$_type = strtolower($fileType);
		// TODO: remake it to settings
		$mimeTypes = array('png'=>'image/png',
										'jpg'=>'image/jpeg',
										'gif'=>'image/gif', 
										'css'=>'text/css;',
										'js'=>'text/javascript',
										'swf'=>'application/x-shockwave-flash');


		
		if (in_array($_type, array('jpg','jpeg','png','gif'))) {
			// Processing images
			$filePath = $url;
			$patterns = array();
			//Pattern with desired type of image
			$patterns[] = '/\/([0-9]+)x([0-9]+)\/type:(.*)\//i';
			//Pattern with desired type of image and one dimension
			$patterns[] = '/\/([0-9]+)x\/type:(.*)\//i';
			//Standart pattern
			$patterns[] = '/\/([0-9]+)x([0-9]+)\//i';
			//Pattern for one dimension
			$patterns[] = '/\/([0-9]+)x\//i';
			//Just type
			$patterns[] = '/\/type:(.*)\//i';
			//Processing patterns
			foreach($patterns as $pattern) {
				if (preg_match($pattern, $filePath, $matches)) {
					 
					
					
					$imageSizeInfo = strtolower(str_replace('/', '', $matches[0]));
					$imageSize = new Object();

					if (!in_array($matches[1], array('jpeg','png'))) {
						$imageSize->width = $matches[1];
						if (isset($matches[2]) && !in_array($matches[2], array('jpeg','png'))) {
							//Set height
							$imageSize->height = $matches[2];

							//Checking type
							if (isset($matches[3]) && in_array($matches[3], array('jpeg','png'))) {
								$imageSize->type = strtolower($matches[3]);
							}
						} else {
							//Set as width
							$imageSize->height = $matches[1];

							//Checking type
							if(isset($matches[2]) && in_array($matches[2], array('jpeg','png'))) {
								$imageSize->type = strtolower($matches[2]);
							}
						}

					} else {
						$imageSize->type = $matches[1];
					}
					

					//Remove size info from filePath
					$filePath = str_replace($matches[0], '/', $filePath);
					break;
				}
			}

			//Make file path
			$filePath = ROOT.DS.str_replace('/', DS, $filePath);
			
			if (file_exists($filePath)) {
				//header('Cache-control: public');
				header('Expires:'.date('D, d M Y H:i:s', time()+3600));
				header("Last-Modified: ".date("D, d M Y H:i:s", filemtime($filePath)));
				if (isset($imageSize)) {
					$image = new Image($filePath, $imageSize->type);
					$image->resize($imageSize->width, $imageSize->height);
					$image->show();
					
				} else {
					if (key_exists($fileType, $mimeTypes)) {
						header('Content-type: '.$mimeTypes[$fileType]);
					} else {
						header('Content-type: image/*');
					}
					readfile($filePath);
				}
			} else {
				//Generate no-image
				header('HTTP/1.0 404 Not Found');
			}
		} elseif (in_array($_type, array('css','js'))) {
			// Processing css and js
			$filename = ROOT.DS.str_replace('/', DS, $url);

			// Checking if file exists
			if (is_file($filename)) {
				if (key_exists($fileType, $mimeTypes)) {
					header('Content-type: '.$mimeTypes[$fileType]);
				} else {
					header('Content-type: *');
				}
				
				ob_start();
				include($filename);
				$html = ob_get_clean();
				$html = str_replace('__BASE_URL__', URL::getBaseURL(), $html);
				// remove @depends
				if (preg_match('/\/\/@depends:.*\/\//', $html, $matches)) {
					$html = str_replace($matches[0], '', $html);
				}
				echo $html;
			} else {
				header('HTTP/1.0 404 Not Found');
			}
		} else {
			// All other files
			$filename = ROOT.DS.str_replace('/', DS, $url);
			if (is_file($filename)) {
				if (key_exists($fileType, $mimeTypes)) {
					header('Content-type: '.$mimeTypes[$fileType]);
				} else {
					header('Content-type: *');
				}
				// Set header
				readfile($filename);
			} else {
				header('HTTP/1.0 404 Not found');
			}
		}



	}

	/**
	 *
	 * Upload file
	 * POST[data]:
	 * 		module_name - Name of module
	 * 		table_name - Name of table
	 * 		item_column_name - Name of id
	 * 		id - Value of id
	 * 		column_name - Name of column where store name of file
	 * 		_secured - special flag for secure access. If not set then upload canceled
	 *
	 */
	protected function upload_module_file(){

		if (isset($_POST['data']) && !empty($_FILES)) {

			$data = $_POST['data'];

			if (isset($data['_secured'])) {
				$model = $this->getModel('files');
				if (($fileName = $model->uploadImage($data)) !== false) {
					echo '{result:true,image_file:"'.$fileName.'"}';
				} else {
					echo '{result:false,error_msg:"'.Languages::getJText('global.errors.upload_file').'"}';
				}
			} else {
				echo '{result:false,error_msg:"'.Languages::getText('global.errors.upload_file').'"}';
			}
		}
	}
	
	/**
	 * 
	 * Upload image to temporary folder 
	 * POST[]
	 * 	- module - Name of module
	 * 	- data: 
	 * 			- model - System name of model
	 * 			- field_name - Name of field in table 
	 * 			- id - Id of record 
	 */
	protected function upload(){
		
		if (isset($_POST['data']) && !empty($_FILES)) {
			
			// Checking data and prepare data  
			$data = $_POST['data']; 
			//$data['module'] = $_POST['module']; 
			$data['session_id'] = session_id();
			$filesModel = $this->getModel('files'); 
			$filesModel->upload($data);
			echo '{result:true}'; 
		} 
	}
	
	/**
	 *
	 * Processing images
	 */
	protected function get_image() {

	}


}