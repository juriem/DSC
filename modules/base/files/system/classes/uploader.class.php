<?php
namespace modules\base\files\system\classes; 
/**
 * 
 * Special class for uploading
 * Used uploadify library   
 * @author Juri Em
 *
 */
final class Uploader {
	
	private $_targetFileName; 
	private $_sourceFileName;  
	
	/**
	 * 
	 * Constructor 
	 * @param String $targetFileName
	 */
	public function __construct($targetFileName) {
		if (!empty($_FILES)) {
			
			$fileParts  = pathinfo($_FILES['Filedata']['name']);
			$this->_targetFileName = $targetFileName.'.'.strtolower($fileParts['extension']);
			$this->_sourceFileName = $_FILES['Filedata']['tmp_name'];
		}
	}
	
	/**
	 * 
	 * Move uploaded file to destination
	 * @return BASE_NAME of file for storing  
	 */
	public function upload() {
		if (!file_exists($this->_targetFileName)) {
			if (move_uploaded_file($this->_sourceFileName, $this->_targetFileName)) {
				$fileParts = pathinfo($this->_targetFileName); 
				return $fileParts['basename']; 
			}
		}
		return false; 
	}
	
	
}