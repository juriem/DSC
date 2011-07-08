//@depends: scripts:js;ajax:css//
/**
 * Ajax module
 *   
 */
(function($){
	 
	/**
	 * Params processor  
	 */	
	ParamsProcessor = function(actionButton, pattern, baseHolder) {
		var paramsProcessor = this,
			_baseHolder = $(actionButton).parents('.parent').get(0); 
			data = {}, 
			/**
			 * Create parameter
			 */
			create = function(name, value) {
				value = String(value); 
				value = value.replace(/\n/g, "\\n");
				value = value.replace(/"/g, '\\"');
				value = value.replace(/'/g, "\\'");
				
				var _eval = 'var tmp = {"' + name + '":"' + value + '"};';
				try {
					eval(_eval);
					if (data == undefined) {
						data = tmp;
					} else {
						data = $.extend(data, tmp);
					}
				} catch (e) {
					$.error('modAjax: error add parameters!'); 
				}
			},
			/**
			 * Fetch parameters
			 */
			fetch = function(holder, paramBase, paramsList, baseClass) {
				var params = paramsList.split(',');
				for (ix = 0; ix < params.length; ix++) {
					$(holder).find('.'+baseClass+'.' + params[ix]).each(function(){
						var self = this; 
						//Check for parents 
						if ($(this).parents('.parent').get(0) === $(holder).get(0)) {
							var value = $(self).val();
							var paramName = paramBase.replace('%', params[ix]);
							create(paramName, value);
						}
					});
				}
			}; 
			
		/**
		 * Fetch data from html 
		 * @returns Array of fetched parameters  
		 */
		this.fetch = function(){
			
			if (pattern) {
				// Processing pattern 
				$.each(pattern, function(index, value){
					var holder = value[0];
					if (holder == null) holder = _baseHolder;
				
					// Get template for parameter name 
					var paramName = value[1]; 
					//var baseClass = ''; 
					//if (value[2] != undefined) baseClass = value[2];
					 
					//if (baseClass != '') baseClass = '.'+baseClass; 
					//baseClass += '[data-field]'; 
					
					$(holder).each(function(i){
						var _holder = this; 
						var _paramName = String(paramName).replace('%i', i);
						
						// Start fetching all data holder
						$(_holder).find('[data-field]').each(function(){
							
							// Checking if belongs to parent  
							if ($($(this).parents('.parent').get(0)).get(0) == $(_holder).get(0)) {
								// Processing 
								var name = $(this).attr('data-field'); 	
								var value = $(this).val();
								if ($(this).attr('data-value')) {
									value = $(this).attr('data-value'); 
								}
								name = _paramName.replace('%', $(this).attr('data-field'));
								create(name, value); 
							}
						});
						
					});
				});
			}
			return data; 
		}; 
	}; 
	 
	
	/**
	 * AjaxRequest class 
	 * options:
	 * - onSuccess : Function on success
	 * - getResponse : Function for return response data
	 * - onComplete : Function on complete   
	 */
	AjaxRequest = function(options){
		var ajaxRequest = this, overlay = null,
				show = function(){					
					zIndex = 0; 
					$('body *').each(function(){
						var _zIndex = parseInt($(this).css('z-index')); 
						zIndex = (_zIndex > zIndex)?_zIndex:zIndex; 
					}); 
			
					//Increase zIndex
					zIndex++; 
					
					/*<?php if (file_exists(ROOT.DS.str_replace('/',DS,'images/ajax/loading.gif'))): ?>*/
					overlay = $('<div id="id-busy-overlay"><img src="<?php echo URL::getBaseUrl(); ?>/images/ajax/loading.gif"></div>');
					/*<?php else: ?>*/
					overlay = $('<div id="id-busy-overlay"><img src="<?php echo URL::getBaseUrl(); ?>/files/ajax/assets/loading.gif"></div>');
					/*<?php endif; ?>*/
					$('body').append($(overlay)); 
					$(overlay).css({
						'z-index':zIndex, 
						'width':$(document).width()+'px', 
						'height':$(document).height()+'px'
					});
					
					//Set image position
					var img = $(overlay).find('img'); 
					var imgTop = $(document).scrollTop() + ($(window).height() - $(img).height())/2;
					var imgLeft = ($(window).width() - $(img).width())/2;
					$(img).css({
						'position':'absolute',
						'z-index':(zIndex+1), 
						'top':imgTop+'px', 
						'left':imgLeft+'px'
					});
				}, 
				hide = function(){
					$(overlay).remove(); 
				}; 
				
		/**
		 * Execute ajax request 
		 */
		this.execute = function(){
			
			if (options.hideBusy == undefined) show();
			
			$.ajax({
				async:true,
				url : '<?php echo URL::getBaseUrl(); ?>/',
				type:'post',
				'data':options.data,
				success: function(response){
					
					var result;
					try{
						eval('result = '+response+';');
						
						//Checking for redirect after session is expired 
						if (result.redirect != undefined) {
							if (result.reason != undefined) alert(result.reason);
							window.location.href=result.redirect;
							return; 
						}
						//If don't need response 
						if (options.getResponse == undefined) {
							//Processing result
							if (typeof result == 'object') {
								if (result.result == true) {
									if (typeof options.onSuccess == 'function') {
										options.onSuccess(result); 
									}
								} else {
									if (result.error_msg != undefined) {
										alert(result.error_msg);										
									} else {
										alert('modAjax: Unspecified reponse\'s error!'); 
									} 
								}
							} else {
								alert('modAjax: Bad response format!');
							}
						}
					} catch(e){
						//$('#id-debug').html(response); 
						//Skip and nothing to do
						if (options.getResponse == undefined) {
							//alert('modAjax: can\'t evaluate response!'); 
						}
					}
					//Return response 
					if (typeof options.getResponse == 'function'){
						options.getResponse(response); 
					}
					
				},
				error: function(){
					//alert('modAjax: AJAX system error!');
				},
				complete: function(){
					if (options.hideBusy == undefined) hide();
					//Do something
					if (options.onComplete == 'function') options.onComplete();
				}
			});
		}; 
	}; 
	
	var processor; 
	
	var methods = {
			
			/**
			 * Assign action to anchor button
			 * @param options
			 * 			- lang - Current system language
			 * 			- moduleName - Name of module 
			 * 			- moduleAction - Actions for module 
			 * 			 
			 * 		 	- fetchPattern - pattern for fetching data from page 
			 * 			- appendFetchPattern(self) - Function for append fetch pattern
			 * 			- appendData[self] - Optional. Append data after initialization
			 * 			- data (optional) - Additional data for posting  
			 * 			- onBeforeAction[self] (optional) - Additional checking before making request
			 * 			- getResponse[response,self] (optional) : Function for get response data
			 * 			- onSuccess[result,self] (optional) - function on success result 
			 */
			assign : function(options) {
				
				
				return this.each(function(){
					
					
					//Check for node type
					if (String($(this).get(0).nodeName).toUpperCase() != 'A') 
							$.error('modAjax: Method \'assign\' can be applied only to anchor'); 
					
					
					//Assign event handler
					$(this).click(function(e){
						e.preventDefault();
						var self = this; 
						
						
						
						var makeRequest = true; 
						if (typeof options.onBeforeAction == 'function') {
							makeRequest = options.onBeforeAction(self); 
						}
						//Define fetchPattern
						
						if (makeRequest) {
							//Processing request
							var data = {
									'module':options.moduleName,
									'module_action' : options.moduleAction,
									'lang' : options.lang,
									'base_url':options.baseURL
									};
							
							//Append data 
							if (typeof options.data == 'object') data = $.extend(data, options.data); 
							if (typeof options.data == 'function') data = $.extend(data, options.data(self)); 
							
							if (typeof options.fetchPattern == 'object') {
								data = $.extend(data, (new ParamsProcessor(self, options.fetchPattern, $(self).parents('.parent').get(0))).fetch());
							}
							
							if (typeof options.fetchPattern == 'function') {
								data = $.extend(data, (new ParamsProcessor(self, options.fetchPattern(self), $(self).parents('.parent').get(0))).fetch());
							}
							//appendFetchPattern is depricated 
							if (typeof options.appendFetchPattern == 'function') {
								data = $.extend(data, (new ParamsProcessor(self, options.appendFetchPattern(self), $(self).parents('.parent').get(0))).fetch());
							}
							//appendData is depricated 
							if (typeof options.appendData == 'function') data = $.extend(data, options.appendData()); 
							//Prepare ajax options 
							var ajaxOptions = {'data':data}; 
							if (options.hide != undefined) ajaxOptions = $.extend(ajaxOptions, {hideBusy:true}); 
							if (typeof options.onSuccess == 'function') {
								ajaxOptions = $.extend(ajaxOptions, {onSuccess : function(result) {options.onSuccess(result,self);}}); 
							}
							if (typeof options.onComplete == 'function') {
								ajaxOptions = $.extend(ajaxOptions, {onComplete : function() {options.onComplete(self); }}); 
							}
							if (typeof options.getResponse == 'function') {
								ajaxOptions = $.extend(ajaxOptions, {getResponse : function(response){options.getResponse(response,self);}}); 
							}
							(new AjaxRequest(ajaxOptions)).execute();
						}
					});
				}); 
			},
			/**
			 * Get html content into container
			 * @param options
			 * 	- moduleName : Name of module
			 * 	- moduleAction : Module action 
			 *  - lang : Current language
			 *  - data (optional)
			 *  - insertAfter (optional) : Flag if insert data after container
			 *  - hideBusy (optional) : Hide busy box  
			 * 	Event:
			 * 		onComplete(container) - area where response wrote 
			 * 			 
			 */
			html : function(options) {
				return this.each(function(){
					//Save base object
					var self = this, 
						settings = $.extend({
							data : {}, 
							insertAfter : false, 
							hideBusy : false, 
							onComplete : function() {}
						}, options); 
					 
					var data = {
						'module':options.moduleName,
						'module_action': options.moduleAction,
						'lang':options.lang, 
						'base_url':options.baseURL
					};
					
					if (options.data != undefined) data = $.extend(data, options.data); 
					if (options.fetchPattern != undefined) {
						
						data = $.extend(data, 
								(new ParamsProcessor(self, options.fetchPattern, $(self).parents('.parent').get(0))).fetch());
					} 
					var ajaxOptions = {'data':data, 
							getResponse : function(response){
								var container = null; 
								if (options.insertAfter != undefined) {
									container = $(response);
									
									$(response).insertAfter(self); 
									
								} else {
									$(self).html(response); 
									container = self; 
								}
								
								if (typeof options.onComplete == 'function') {
									options.onComplete(container); 
								}
								
							}
					};
					if (options.hide != undefined) ajaxOptions = $.extend(ajaxOptions, {hideBusy:true});
					(new AjaxRequest(ajaxOptions)).execute();
				}); 
			}
			
	}; 
	
	/**
	 * Public part 
	 */
	$.fn.modAjax = function(method, options){
		
		var _options; 
		if (typeof method == 'object') {
			_options = method;  
		} else {
			if (options != undefined) _options = options; 
		}
		
		// Check options  
		
		if (_options == undefined) $.error('modAjax: options is undefined!'); 
		if (_options.moduleName == undefined) $.error('modAjax: moduleName is undefined!'); 
		if (_options.moduleAction == undefined) $.error('modAjax: moduleAction is undefined!');
		if (_options.lang == undefined) {
			var _lang = $.modScripts('get','lang');
			if (_lang != undefined) {
				_options.lang = _lang; 
			} else {
				$.error('modAjax: lang is undefined!');
			}
		}
		if (_options.baseURL == undefined) _options.baseURL = '';
		
		if (methods[method]) {
			return methods[method].apply(this, new Array(_options));
		} else if (typeof method == 'object') {
			return methods['assign'].apply(this, new Array(_options));
		} else {
			$.error('modAjax: unknown method "'+method+'"!'); 
		}
	};
	
	/**
	 * Global action
	 */
	$.modAjax = function(options) {
		if (options == undefined) $.error('modAjax: options is undefined!'); 
		if (options.moduleName == undefined) $.error('modAjax: moduleName is undefined!'); 
		if (options.moduleAction == undefined) $.error('modAjax: moduleAction is undefined!'); 
		if (options.lang == undefined) $.error('modAjax: lang is undefined!');
		if (options.baseURL == undefined) options.baseURL = ''; 
		var data = {
				'module':options.moduleName,
				'module_action': options.moduleAction,
				'lang':options.lang, 
				'base_url':options.baseURL
		};
		//Extending data
		if (options.data != undefined) data = $.extend(data, options.data);
		
		var ajaxOptions = {'data':data}; 
		if (typeof options.onComplete == 'function') {
			ajaxOptions = $.extend(ajaxOptions, {getResponse : function(response){options.onComplete(response);}}); 
		}
		if (options.hide != undefined) ajaxOptions = $.extend(ajaxOptions, {hideBusy:true});
		(new AjaxRequest(ajaxOptions)).execute();
	}; 
})(jQuery);

