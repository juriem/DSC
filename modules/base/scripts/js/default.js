<?php include('jquery'.DS.'default.js'); ?>
/**
 * Global settings 
 */
(function($){
	/**
	 * Global settings for system scripts 
	 */
	
	ScriptsSettings = function() {
		var scriptSettings = this, 
			_settings = {}
		/**
		 * Initialize setting
		 */
		this.init = function(settings){
			_settings = $.extend(_settings, settings); 
		}; 	
		/**
		 * Set setting 
		 */
		this.set = function(settingName, settingValue) {
			_settings.settingName = settingValue; 
		}; 
		/**
		 * Get setting 
		 */
		this.get = function(settingName) {
			var result; 
			$.each(_settings, function(name,value){
				if (name == settingName) {
					result = value;
				} 
			});
			return result; 
		}; 		
	}; 
	
	/**
	 * Global instance of settings for script 
	 */
	var settingsInstance;  
	
	$.modScripts = function(method, param1, param2) {
		
		if (settingsInstance == undefined) settingsInstance = new ScriptsSettings(); 
					
		if (typeof method == 'object') {
			var settings = method;
			settingsInstance.init(settings);
		} else {
			if (method != undefined) {
				switch(method) {
					case 'init':	
						if (typeof param1 == 'object') {
							var settings = param1;
							settingsInstance.init(settings);
						}
						break; 
					case 'set':
						if (param1 != undefined && param2 != undefined) {
							var settingName = param1, settingValue = param2; 
							settingsInstance.set(settingName, settingValue);  
						}
						break; 
					case 'get':
					default:
						if (param1 != undefined) {
							var settingName = param1; 
							return settingsInstance.get(settingName);
						}		
				}
			}
		}
	};
})(jQuery); 
 