<?php
namespace modules\base\files\models;
/**
 *
 * Model for files
 * @author Juri Em
 *
 */
final class FilesModel extends \system\base\Model {

	/**
	 *
	 * Update image name
	 * @param  $data {module_name,item_column_name,column_name,id}
	 * 			
	 */
	public function uploadImage($data){
		$moduleName = $data['module_name'];
		$id = $data['id'];
		if (!isset($data['item_column_name'])) $data['item_column_name'] = 'item_id'; 
		$columnName = $data['column_name'];
		$tableName = $data['table_name']; 
		
		//Checking path
		$dir = ROOT.DS.'storage';
		if (!file_exists($dir) || (file_exists($dir) && !is_dir($dir))) mkdir($dir, 0777);
		//Checking htaccess
		$file = $dir.DS.'.htaccess';
		if (!file_exists($file) || (file_exists($file) && !is_file($file))) {
			$fh = fopen($file, 'w');
			fwrite($fh, 'Deny From All');
			fclose($fh);
		}
		//Checking module directory 
		$dir .= DS.'system'.DS.$moduleName.DS.$id;
		if (!file_exists($dir) || (file_exists($dir) && !is_dir($dir))) mkdir($dir, 0777, true);
		//New file
		$fileParts  = pathinfo($_FILES['Filedata']['name']);
		$fileName = time().'.'.strtolower($fileParts['extension']);
		$targetFileName = $dir.DS.$fileName;  
		
		
		//Check if already exists
		$sql = "Select `{$data['column_name']}` As file_name From `{$data['table_name']}` Where `{$data['item_column_name']}` = {$data['id']}";
		$rs = $this->_get($sql);
		if (!$rs) return false; //System error
		if ($rs->file_name !== null) {
			//Need to delete
			if (file_exists($dir.DS.$rs->file_name)){
				@unlink($dir.DS.$rs->file_name);  //Delete old file 
			} 
		}
		 
		$this->__start();
		//Update value for file_name 
		$sql = "Update `{$data['table_name']}` Set `{$data['column_name']}` = '$fileName' Where `{$data['item_column_name']}` = $id"; 
		if (!$this->_execute($sql)) return false;
		//Move uploaded file  
		$sourceFileName = $_FILES['Filedata']['tmp_name'];
		if (!move_uploaded_file($sourceFileName, $targetFileName)) {	
			$this->__rollback(); 
			return false;  
		}
		$this->__commit(); 
		return $fileName;
	}
	
	/**
	 * 
	 * Upload file into temp location
	 * @param array $data
	 * 	- session_id - Session id
	 * 	- module - System name of module 
	 * 	- model - System name of model
	 * 	- field_name - System name of field 
	 * 	- id - Id of record, if new then used 0		 
	 */
	public function upload($data) {
		
		// Checking for directory
		$path = ROOT.DS.'storage'.DS.'uploads'; 
		if (!file_exists($path)) {
			mkdir($path); 
		}
		
		$fileParts = pathinfo($_FILES['Filedata']['name']); 
		$fileName = md5($data['session_id'].$data['module_name'].$data['table_name'].$data['id']) . '.' . $fileParts['extension']; 
		$targetFileName = $path . DS . $fileName;

		if (file_exists($targetFileName)) unlink($targetFileName); 
		
		if (!move_uploaded_file($_FILES['Filedata']['tmp_name'], $targetFileName)) {
			return false;
		}
		return true;
	}

	/**
	 * 
	 * Move file to right location 
	 * @param string $moduleName
	 * @param string $tableName
	 * @param string $fileColumnName
	 * @param int $uploadId
	 * @param int $id
	 * @param int $idColumnName
	 */
	public function updateFiles($moduleName, $tableName, $fileColumnName, $uploadId, $id, $idColumnName=null) {
		
		
		// Source path without extension
		$sourceFileName = md5(session_id().$moduleName.str_replace('#', '', $tableName).$uploadId);
		
		$sourcePath = ROOT.DS.'storage'.DS.'uploads';
		$dh = opendir(ROOT.DS.'storage'.DS.'uploads');
		$foundIt = false; 
		while ($file = readdir($dh)) {
			
			if (strpos($file, $sourceFileName) !== false) {
				$sourceFileName = $sourcePath . DS . $file;
				$foundIt = true; 
				break; 
			}
		}
		
		closedir($dh); 
		if (file_exists($sourceFileName)) {
			
			// Source file 
			
			// Checking for existed 
			$destinationPath = ROOT.DS.'storage'.DS.'system'.DS.$moduleName.DS.str_replace('#','',$tableName).DS.$id; 
			if (!file_exists($destinationPath)) mkdir($destinationPath, 0777, true); 
			
			// Checking for existing 
			if ($idColumnName == null) {
				$sql = "Select $fileColumnName From $tableName Where id = $id"; 
				$result = $this->_get($sql); 
				if ($result) {
					if ($result->$fileColumnName !== null) {
						// Remove old file
						unlink($destinationPath.DS.$result->$fileColumnName); 
					}
					
					// Generate new file name 
					$fileParts = pathinfo($sourceFileName); 
					$fileName = time() . '.' . $fileParts['extension'];
					$destination = $destinationPath . DS . $fileName;
					
					if (copy($sourceFileName, $destination)) {
						// Delete uploaded file 
						unlink($sourceFileName); 
						
						// Update data base 
						$sql = "Update $tableName Set $fileColumnName = '$fileName' Where id = $id"; 
						if ($this->_execute($sql)) {
							return true;
						}
					} 
				}
			} 
		}
		
		
		
	}
	
}