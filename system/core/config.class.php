<?php
namespace system\core;

class Config {

	private $_configFile;

	public function __construct($configFile) {
		$this->_configFile = $configFile;
	}

	/**
	 *
	 * Read configuration
	 */
	private function _readConfiguration(){
		$result = parse_ini_file($this->_configFile);
		$out = array();
		if ($result) {
			foreach($result as $key=>$value){
				// Processing key
				$keys = explode('.', $key);
				$out[$keys[0]][$keys[1]] = $value;
			}
		}
		return $out;
	}

	/**
	 *
	 * Write configuration
	 * @param array $config
	 */
	private function _writeConfiguration($config){


		$configContent = "";

		foreach($config as $section=>$value){
			$configContent .= "[$section]\r\n";
			foreach($value as $configName=>$configValue) {
				$_configName = $section . '.' . $configName . ' = ';
				if (!is_numeric($configValue)) {
					$configContent .= $_configName . '"' . $configValue . '"' . "\r\n";
				} else {
					$configContent .= $_configName . $configValue . "\r\n";
				}
			}
			$configContent .= "\r\n"; // End of section
		}
		file_put_contents($this->_configFile, $configContent); // Rewrite config
	}

	public function getAll(){
		return $this->_readConfiguration();
	}
	

	/**
	 * 
	 * Get configuration value 
	 * @param string $config - Configuration name in next format section__configName
	 */
	public function __get($config) {
		$parts = explode('__', $config);
		if (is_array($parts) && count($parts) == 2) {
			$section = $parts[0];
			$configName = $parts[1];
				
			$config = $this->_readConfiguration();
			if (key_exists($section, $config)){
				if (key_exists($configName, $config[$section])) {
					return $config[$section][$configName];
				}
			}
		}
		return null;
	}
	
	/**
	 * 
	 * Set configuration 
	 * @param string $config - Configuration name in next format section__configName
	 * @param mixed $value - Value for configuration
	 */
	public function __set($config, $value){
		$parts = explode('__',$config);
		if (is_array($parts) && count($parts) == 2) {
			$section = $parts[0]; 
			$configName = $parts[1];
			// Write configuration 
			$config = $this->_readConfiguration();
			$config[$section][$configName] = $value;
			$this->_writeConfiguration($config);
		} 
	}

}