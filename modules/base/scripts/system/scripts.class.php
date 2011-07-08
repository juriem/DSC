<?php
declare(ENCODING='utf-8');
namespace modules\base\scripts\system;

use system\core\SystemClass;
use modules\base\config\system\Config;
use system\core\URL;

/**
 *
 * Special class for processing scripts
 * @author Juri Em
 *
 */
final class Scripts extends SystemClass{

	const MODULE_LIB_JS = 'js'; 
	const MODULE_LIB_CSS = 'css'; 
	
	
	private $_moduleLibs;
	private $_moduleStyles;
	private $_libs;

	
	/**
	 *
	 * Add Lib
	 * @deprecated
	 * @param string $scriptLibCode
	 */
	public function addLib($scriptLibCode) {
		if (!isset($this->_libs)) $this->_libs = array();

		$alreadyAdded = false;
		foreach($this->_libs as $lib) {
			if ($lib == strtolower($scriptLibCode)) {
				$alreadyAdded = true;
				break;
			}
		}
		if (!$alreadyAdded) $this->_libs[] = $scriptLibCode;
	}

	/**
	 * 
	 * Add reference for module script or styles 
	 * @param string $moduleName - Name of module 
	 * @param string $type - type of reference. By default use js
	 * @param boolean $topmost - add reference at top of array. By default <strong>false</strong>  
	 */
	public function addModuleLib($moduleName, $type=self::MODULE_LIB_JS, $topmost=false){
		$moduleName = strtolower($moduleName);
		
		switch($type){
			case 'js':
				if (!isset($this->_moduleLibs)) $this->_moduleLibs = array();
				$libs = $this->_moduleLibs;
				$defaultFile = 'default.js';
				break;
			case 'css':
				if (!isset($this->_moduleStyles)) $this->_moduleStyles = array();
				$libs = $this->_moduleStyles;
				$defaultFile = 'default.css';
				break;
			default:
				return true;			
		}
		
		//Search in cache
		if (in_array($moduleName, $libs)) return true;
		
		$foundIt = true;
		//Processing module name
		$parts = explode('.', $moduleName);
		$_moduleName = $parts[0];
		$filename = '';
		for($i=1;$i<count($parts);$i++) $filename .= DS.$parts[$i];
		$systemPath = ROOT.DS.'modules'.DS.'base'.DS.$_moduleName.DS.$type; 
		$userPath = ROOT.DS.'modules'.DS.$_moduleName.DS.$type;
		$path = $systemPath.$filename;  
		if (!file_exists($path)) {
			//Check user modules
			$path = $userPath . $filename;  
			if (!file_exists($path)) $foundIt = false;
		} 
		//If not found 
		if ($foundIt) {
			if (!is_file($path)) {
				$path .=  DS.$defaultFile;
				if (!is_file($path)) $foundIt = false; 
			}
		}
		
		//Add to cache 
		if ($foundIt) { 
			
			//Check for dependencies and add it before any
			$content = file_get_contents($path); 
			if (preg_match('/\/\/@depends: (.*)\/\//i', $content, $matches)) {
				
				$_libs = explode(';',$matches[1]);
				foreach($_libs as $_lib) {
					$_parts = explode(':',$_lib); 
					$__moduleName = $_parts[0]; 
					$__type = trim(str_replace(array("\n","\r"),'',$_parts[1]));
					$this->addModuleLib($__moduleName, $__type); 
				}
			}
			switch($type) {
				case 'js':
					$this->_moduleLibs[] = $moduleName;
					break;  
				case 'css':
					$this->_moduleStyles[] = $moduleName; 
					break; 
			}
		}
		
		return true;
	}

	/**
	 *
	 * Parser inline scripts
	 * @param String $html - HTML code
	 */
	public function parse($html) {
		$injection = '';

		//Processing libs
		$scriptsLibs = '';
		$stylesLibs = '';

		if (Config::__()->system__encode_scripts == 'yes') {
			$scriptsLibs .= "\n".'<script type="text/javascript" src="'.URL::__()->getBaseURL().'/files/scripts/js/jquery.base64.min.js">';
			$scriptsLibs .= "\n".'<script type="text/javascript">'
						.'<!--'."\n"
						."\n".'$.base64.is_unicode = true;'
						."\n".'//-->'
						."\n".'</script>'; 
		}
		
		
		//Processing module libs
		$moduleScripts = "";
		if (isset($this->_moduleLibs)) {
			foreach($this->_moduleLibs as $module) {
				
				$parts = explode('.',$module);
				$module = $parts[0];
				$file = '';
				for($i=1;$i<count($parts);$i++) $file .= '/'.$parts[$i];
				if (!preg_match('/\.([a-z]?)$/i',$file)) $file .= '/default.js';
				$moduleScripts .= "\n".'<script type="text/javascript" src="'.URL::__()->getBaseURL().'/files/'.$module.'/js'.$file.'"></script>';
			}
		}
		$moduleStyles = '';
		if (isset($this->_moduleStyles)) {
			
			foreach($this->_moduleStyles as $module) {
				$parts = explode('.',$module);
				$module = $parts[0];
				$file = '';
				for($i=1;$i<count($parts);$i++) $file .= '/'.$parts[$i];
				if (!preg_match('/\.([a-z]?)$/i',$file)) $file .= '/default.css';
				$moduleStyles .= "\n<link type=\"text/css\" rel=\"stylesheet\" href=\"".URL::__()->getBaseURL()."/files/$module/css$file\">";
			}
		}

		//Processing libs
		if (isset($this->_libs)) {
			$libsModel = $this->_getModel('libs'); 
			
			foreach($this->_libs as $code) {

				
				$lib = $libsModel->getByCode($code); 
				
				if ($lib) {
					//Processing lib
					$items = $libsModel->selectItems($lib->id);
					foreach($items as $item) {
						if ($item->item_type == 'js') {
							$scriptsLibs .= "\n<script type=\"text/javascript\" src=\"".URL::__()->getBaseURL()."/scripts/".$lib->folder_name."/".$item->item_name."\"></script>";
						} elseif ($item->item_type == 'css') {
							$stylesLibs .="\r\n<link type=\"text/css\" rel=\"stylesheet\" href=\"".URL::__()->getBaseURL()."/scripts/".$lib->folder_name."/".$item->item_name."\">";
						}
					}
					unset($items);
					if ($lib->additional_script != '') {
						$scriptsLibs .= "\n<script type=\"text/javascript\">
										<!--
											".$lib->additional_script."
										//-->
										</script>"; 
					}

				}
				unset($lib);
			}
		}
		
		if (!empty($stylesLibs)) $injection .= $stylesLibs . "\n";
		if (!empty($moduleStyles)) $injection .= $moduleStyles . "\n";
		if (!empty($moduleScripts)) $injection .= $moduleScripts . "\n";
		if (!empty($scriptsLibs)) $injection .= $scriptsLibs . "\n";
		//Processing scripts
		$scripts = '';
		if (preg_match_all("'<script rel=\"inline\">(.*?)</script>'si", $html, $tmplArray)) {
			for($ix=0; $ix<count($tmplArray[0]);$ix++){
				$full_html = $tmplArray[0][$ix];
				$scripts .= "\r\n" . $tmplArray[1][$ix];
				$html = str_replace($full_html, "", $html);
			}
		}
		if (!empty($scripts)) {
			if (modules\base\config\system\Config::__()->system->encode_scripts == 'yes') {
				$scripts = "eval($.base64.decode('".base64_encode($scripts)."'));";
			}
			$injection .= "<script type=\"text/javascript\">
					<!-- 
					$().ready(function(){".$scripts."}); 
					//--> 
					</script>"; 
		}

		//Processing styles
		$styles = '';
		if (preg_match_all("'<style rel=\"inline\">(.*?)</style>'si", $html, $tmplArray)) {
			$cnt = count($tmplArray[0]);
			for($ix=0; $ix<$cnt;$ix++){
				$full_html = $tmplArray[0][$ix];
				$styles .= " " . $tmplArray[1][$ix];
				$html = str_replace($full_html, "", $html);
			}
			$styles = '<style type="text/css"> '.$styles.' </style>';
		}
		if (!empty($styles)) $injection .= "\n".$styles;
		

		//Inject code into head
		if (preg_match_all("'<head>(.*?)</head>'si", $html, $matches)) {
			$headFullHtml = $matches[0][0];
			$headHtml = $matches[1][0];
			$headHtml = '<head>'.$headHtml.$injection."\n".'</head>';
			$html = str_replace($headFullHtml, $headHtml, $html);

		}

		return $html;
	}




}