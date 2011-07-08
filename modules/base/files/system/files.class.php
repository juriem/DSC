<?php
namespace modules\base\files\system; 
/**
 * 
 * Enter description here ...
 * @author Juri Em
 *
 */
final class Files extends SystemClass {
	
	// Static 
	
	protected static $_instance;
	public static function &__() {
		if (!self::$_instance) self::$_instance = new self(__DIR__); 
		return self::$_instance;
	}
	
	// Instance 
	/**
	 * 
	 * Build normal path from <folder>/<folder>/<folder>/file[<extension>] format 
	 * @param string $path
	 */
	public function buildPath($path) {
		return ROOT.DS.str_replace('/', DS, $path);
	}
	
	/**
	 * 
	 * Get path to resource 
	 * @param string $moduleName - System name of module 
	 * @param string $modelName - System name of model 
	 * @param string $resourceType - Type of resource: images, ...
	 * @param int $id - Id of record 
	 * @param string $fileName - Name of file 
	 * @param string $size - Size for resizing (widthxheight)
	 * @param string $imageType - Image type: png, jpg
	 */
	public function getResourceURL($moduleName, $modelName, $resourceType, $id, $fileName, $size = '', $imageType='') {
		$url = '__BASE_URL__/files/'.$moduleName.'/'.$resourceType.'/'.$modelName.'/'.$id; 
		if ($size != '') $url .= '/' . $size; 
		if ($imageType != '') $url .= '/type:'.$imageType; 
		$url .= '/' . $fileName; 
		return $url;
	}
	
}