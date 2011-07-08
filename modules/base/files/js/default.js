//@depends: scripts:js;scripts.swfobject:js;scripts.uploadify:css//
/**
 * Wrapper for uploadify library 
 */
(function($){
	
	/**
	 * Flash object for uploadify 
	 */
	UploadifyUploader = function(settings, object) {
		
		var uploadifyUploader = this, 
			elementId = $(object).attr('id'), 
			self = null, 
			encodeScriptData = function(vars) {
				if (vars != undefined) {
					var params = '';
					$.each(vars, function(name,value){
						params += ((params=='')?'':'&')+name+'='+value; 
					});
					$('#id-debug').append(params + '<br>'); 
					return escape(params); 
				} else {
					return ''; 
				}
			}; 
		
		//Initialize data and interface 
		var pagePath = location.pathname;
		pagePath = pagePath.split('/');
		pagePath.pop();
		pagePath = pagePath.join('/') + '/';
		var data = {};
		data.uploadifyID = settings.id;
		data.pagepath = pagePath;
		if (settings.buttonImg) data.buttonImg = escape(settings.buttonImg);
		if (settings.buttonText) data.buttonText = escape(settings.buttonText);
		if (settings.rollover) data.rollover = true;
		data.script = settings.script;
		data.folder = escape(settings.folder);
		data.scriptData = encodeScriptData(settings.scriptData); 
		data.width          = settings.width;
		data.height         = settings.height;
		data.wmode          = settings.wmode;
		data.method         = settings.method;
		data.queueSizeLimit = settings.queueSizeLimit;
		data.simUploadLimit = settings.simUploadLimit;
		if (settings.hideButton)   data.hideButton   = true;
		if (settings.fileDesc)     data.fileDesc     = settings.fileDesc;
		if (settings.fileExt)      data.fileExt      = settings.fileExt;
		if (settings.multi)        data.multi        = true;
		if (settings.auto)         data.auto         = true;
		if (settings.sizeLimit)    data.sizeLimit    = settings.sizeLimit;
		if (settings.checkScript)  data.checkScript  = settings.checkScript;
		if (settings.fileDataName) data.fileDataName = settings.fileDataName;
		if (settings.queueID)      data.queueID      = settings.queueID;
		if (settings.onInit() !== false) {
			$(object).css('display','none');
			$(object).after('<div id="' + settings.id + 'Uploader"></div>');
			swfobject.embedSWF(settings.uploader, settings.id + 'Uploader', settings.width, settings.height, '9.0.24', settings.expressInstall, data, {'quality':'high','wmode':settings.wmode,'allowScriptAccess':settings.scriptAccess},{},
			function(event) {
				
				self = $('#'+settings.id+'Uploader'); 
				if (typeof(settings.onSWFReady) == 'function' && event.success) settings.onSWFReady();
			});
			if (settings.queueID == false) {
				$("#" + settings.id + "Uploader").after('<div id="' + settings.id + 'Queue" class="uploadifyQueue"></div>');
			} else {
				$("#" + settings.queueID).addClass('uploadifyQueue');
			}
		}
		
		/**
		 * Change data for script 
		 * @param scriptData
		 */
		this.changeScriptData = function(data,reset){
			
			if (reset == undefined) reset = false; 
			if (reset) {
				settings.scriptData = data;
			} else {
				settings.scriptData = $.extend(settings.scriptData, data); 
			}
			var settingValue = encodeScriptData(settings.scriptData);
			var result = $(self).get(0).updateSettings('scriptData', settingValue); 
		}; 
		
		/**
		 * Start uploading file  
		 * @param fileId
		 * @param check
		 */
		this.upload = function(fileId, check) {
			if (check == undefined) check = false; 
			$(self).get(0).startFileUpload(fileId, check); 
		};
		
		/**
		 * Cancel file upload 
		 * @param fileId
		 */
		this.cancel = function(fileId) {
			$(self).get(0).cancelFileUpload(fileId, true, true, false);
		}; 
		
		/**
		 * Clear files queue 
		 */
		this.clear = function() {
			$(self).get(0).clearFileUploadQueue(false);
		}; 
	}; 
	
	/**
	 * Wrapper for input element 
	 */
	UploadifyWrapper = function(options,object){
		
		var uploadifyWrapper = this,
		/**
		 * Instance of object 
		 */
		self = object, 
		settings = $.extend({
			id              : $(object).attr('id'), // The ID of the object being Uploadified
			uploader        : '<?php echo URL::getBaseUrl();?>/files/files/assets/uploadify.swf', // The path to the uploadify swf file
			script          : '<?php echo URL::getBaseUrl();?>/', // The path to the uploadify backend upload script
			expressInstall  : null, // The path to the express install swf file
			folder          : '', // The path to the upload folder
			height          : 64, // The height of the flash button
			width           : 64, // The width of the flash button
			cancelImg       : '<?php echo URL::getBaseUrl();?>/files/files/assets/cancel.png', // The path to the cancel image for the default file queue item container
			buttonImg : '<?php echo URL::getBaseUrl(); ?>/files/files/assets/add_image.png',
			wmode           : 'opaque', // The wmode of the flash file
			scriptAccess    : 'sameDomain', // Set to "always" to allow script access across domains
			fileDataName    : 'Filedata', // The name of the file collection object in the backend upload script
			method          : 'POST', // The method for sending variables to the backend upload script
			queueSizeLimit  : 1, // The maximum size of the file queue
			simUploadLimit  : 1, // The number of simultaneous uploads allowed
			queueID         : false, // The optional ID of the queue container
			scriptData		: {
				module : options.moduleName,
				module_action : options.moduleAction,
				language : options.lang,
				PHPSESSION:'<?php echo session_id();?>'
			},
			multi : options.multiple, 
			auto : options.auto, 
			// Set to "speed" to show the upload speed in the default queue item
			displayData     : 'percentage',
			// Set to true if you want the queue items to be removed when a file is done uploading
			removeCompleted : true, 
			// Function to run when uploadify is initialized
			onInit          : function() {}, 
			// Function to run when a file is selected
			onSelect        : function() {}, 
			// Function to run once when files are added to the queue
			onSelectOnce    : function() {}, 
			// Function to run when the queue reaches capacity
			onQueueFull     : function() {return false;},
			// Function to run when script checks for duplicate files on the server
			onCheck         : function() {},
			// Open 
			//onOpen : function(){$('#id-debug').append('Opened <br>');},
			// Function to run when an item is cleared from the queue
			onCancel        : function() {}, 
			// Function to run when the queue is manually cleared
			onClearQueue    : function() {}, 
			// Function to run when an upload item returns an error
			onError         : function() {}, 
			// Function to run each time the upload progress is updated
			onProgress      : function() {}, 
			onComplete      : function(event, ID, fileObj, response, data) { 
				eval('var result='+response+';');
				if (result != undefined) {
					if (result.redirect != undefined) {
						// Check for expired session 
						window.location.href = result.redirect;
					} else if (result.result != undefined) {
						if (result.error_msg != undefined) alert(result.error_msg);
					}
				}
			}, 
			fileDesc : 'Files', 
			// Function to run when an upload is completed
			onAllComplete   : function() { if (typeof options.onSuccess == 'function') options.onSuccess(); }
		}, options),
		_instanceId = (new Date()).getTime();  
		
		
		//Additional extendings  
		//Check filter for extension 
		if (options.fileExt != undefined) settings = $.extend(settings, {fileExt : options.fileExt});
		//Check for queue limits 
		if (options.queueSize != undefined) settings = $.extend(settings, {queueSizeLimit : options.queueSize});
		//Checking custom image	
		if (options.image != undefined) {
			if (options.image.src != undefined && options.image.width != undefined && options.image.height != undefined) {
				//Extend uploadify settings 
				settings.buttonImg = options.image.src; 
				settings.width = options.image.width; 
				settings.height = options.image.height;
			}
		} 
		//Add script data 
		if (options.data != undefined) {
			settings.scriptData = $.extend(settings.scriptData, options.data); 
		}
		
		//Init flash uploader 
		var _uploader = new UploadifyUploader(settings, object); 
		
		
		
		// =============================================================== //
		
		// Open 
		if (typeof(settings.onOpen) == 'function') {
			$(self).bind("uploadifyOpen", settings.onOpen);
		}
		// Select 
		$(self).bind("uploadifySelect", {'action': settings.onSelect, 'queueID': settings.queueID}, function(event, ID, fileObj) {
			if (event.data.action(event, ID, fileObj) !== false) {
				var byteSize = Math.round(fileObj.size / 1024 * 100) * .01;
				var suffix = 'KB';
				if (byteSize > 1000) {
					byteSize = Math.round(byteSize *.001 * 100) * .01;
					suffix = 'MB';
				}
				var sizeParts = byteSize.toString().split('.');
				if (sizeParts.length > 1) {
					byteSize = sizeParts[0] + '.' + sizeParts[1].substr(0,2);
				} else {
					byteSize = sizeParts[0];
				}
				if (fileObj.name.length > 20) {
					fileName = fileObj.name.substr(0,20) + '...';
				} else {
					fileName = fileObj.name;
				}
				queue = '#' + settings.id + 'Queue';
				if (event.data.queueID) {
					queue = '#' + event.data.queueID;
				}
				$(queue).append('<div id="' + settings.id + ID + '" class="uploadifyQueueItem"> ' 
						+ '<div class="cancel">'
						+	'<a href="#"><img src="' + settings.cancelImg + '" border="0" /></a>'
						+'</div>'
						+'<span class="fileName">' + fileName + ' (' + byteSize + suffix + ')</span><span class="percentage"></span>'
						+'<div class="uploadifyProgress">'
						+'<div id="' + settings.id + ID + 'ProgressBar" class="uploadifyProgressBar"><!--Progress Bar--></div>'
						+'</div>'
					+'</div>');
				//Cancel file upload 
				$('#'+settings.id+ID+' div.cancel a').click(function(e){
					e.preventDefault(); 
					_uploader.cancel(ID); 
				}); 
			}
		});
		// SelectOnce
		$(self).bind("uploadifySelectOnce", {'action': settings.onSelectOnce}, function(event, data) {
			event.data.action(event, data);
			if (settings.auto) {
				if (settings.checkScript) {
					_uploader.upload(null, false); 
				} else {
					_uploader.upload(null, true); 
				}
			}
		});
		// QueueFull
		$(self).bind("uploadifyQueueFull", {'action': settings.onQueueFull}, function(event, queueSizeLimit) {
			if (event.data.action(event, queueSizeLimit) !== false) {
				alert('The queue is full.  The max size is ' + queueSizeLimit + '.');
			}
		});
		// CheckExist
		$(self).bind("uploadifyCheckExist", {'action': settings.onCheck}, function(event, checkScript, fileQueueObj, folder, single) {
			var postData = new Object();
			postData = fileQueueObj;
			postData.folder = (folder.substr(0,1) == '/') ? folder : pagePath + folder;
			if (single) {
				for (var ID in fileQueueObj) {
					var singleFileID = ID;
				}
			}
			$.post(checkScript, postData, function(data) {
				for(var key in data) {
					if (event.data.action(event, data, key) !== false) {
						var replaceFile = confirm("Do you want to replace the file " + data[key] + "?");
						if (!replaceFile) {
							_uploader.cancel(key);
						}
					}
				}
				if (single) {
					_uploader.upload(singleFileID, true);
				} else {
					_uploader.upload(null, true);
				}
			}, "json");
		});
		
		// Cancel 
		$(self).bind("uploadifyCancel", {'action': settings.onCancel}, function(event, ID, fileObj, data, remove, clearFast) {
			if (event.data.action(event, ID, fileObj, data, clearFast) !== false) {
				if (remove) { 
					var fadeSpeed = (clearFast == true) ? 0 : 250;
					$("#" + settings.id + ID).fadeOut(fadeSpeed, function() { $(this).remove(); });
				}
			}
		});
		// Clear queue 
		$(self).bind("uploadifyClearQueue", {'action': settings.onClearQueue}, function(event, clearFast) {
			var queueID = (settings.queueID) ? settings.queueID : settings.id + 'Queue';
			if (clearFast) {
				$("#" + queueID).find('.uploadifyQueueItem').remove();
			}
			if (event.data.action(event, clearFast) !== false) {
				$("#" + queueID).find('.uploadifyQueueItem').each(function() {
					var index = $('.uploadifyQueueItem').index(this);
					$(this).delay(index * 100).fadeOut(250, function() { $(this).remove(); });
				});
			}
		});
		
		// Errors 
		var errorArray = [];
		$(self).bind("uploadifyError", {'action': settings.onError}, function(event, ID, fileObj, errorObj) {
			if (event.data.action(event, ID, fileObj, errorObj) !== false) {
				var fileArray = new Array(ID, fileObj, errorObj);
				errorArray.push(fileArray);
				$("#" + settings.id + ID).find('.percentage').text(" - " + errorObj.type + " Error");
				$("#" + settings.id + ID).find('.uploadifyProgress').hide();
				$("#" + settings.id + ID).addClass('uploadifyError');
			}
		});
		
		// onUpload event 
		if (typeof(settings.onUpload) == 'function') {
			$(self).bind("uploadifyUpload", settings.onUpload);
		}
		// Progress 
		$(self).bind("uploadifyProgress", {'action': settings.onProgress, 'toDisplay': settings.displayData}, function(event, ID, fileObj, data) {
			
			
			if (event.data.action(event, ID, fileObj, data) !== false) {
				$("#" + settings.id + ID + "ProgressBar").animate({'width': data.percentage + '%'},250,function() {
					if (data.percentage == 100) {
						$(this).closest('.uploadifyProgress').fadeOut(250,function() {$(this).remove();});
					}
				});
				if (event.data.toDisplay == 'percentage') displayData = ' - ' + data.percentage + '%';
				if (event.data.toDisplay == 'speed') displayData = ' - ' + data.speed + 'KB/s';
				if (event.data.toDisplay == null) displayData = ' ';
				$("#" + settings.id + ID).find('.percentage').text(displayData);
			}
		});
		// Complete
		$(self).bind("uploadifyComplete", {'action': settings.onComplete}, function(event, ID, fileObj, response, data) {
			if (event.data.action(event, ID, fileObj, unescape(response), data) !== false) {
				$("#" + settings.id + ID).find('.percentage').text(' - Completed');
				if (settings.removeCompleted) {
					$("#" + $(event.target).attr('id') + ID).fadeOut(250,function() {$(this).remove();});
				}
				$("#" + jQuery(event.target).attr('id') + ID).addClass('completed');
			}
		});
		// All complete
		//if (typeof(settings.onAllComplete) == 'function') {
		$(self).bind("uploadifyAllComplete", {'action': settings.onAllComplete}, function(event, data) {
			
			if (typeof _uploadersQueueCallback == 'function') _uploadersQueueCallback(); 
			if (event.data.action(event, data) !== false) {
				errorArray = [];
			}
		});
		//}
		
		// =============================================================== //
		
		// Public methods 
		
		/**
		 * Checking object 
		 * @param _object 
		 * @returns {Boolean}
		 */
		this.checkObject = function(_object) {
			if ($(_object).attr('id') == $(object).attr('id')) {
				return true; 
			}
			return false; 
		};
		
		/**
		 * Upload file. Used for non auto uploads 
		 * @param options
		 * 		callback - function for queue object. It calls when upload fully completed 
		 * 		data - additional data for script 
		 */
		this.upload = function(callback, options, reset){
			if (reset == undefined) reset = false;
			
			if (typeof options.onSuccess == 'function') settings.onSuccess = options.onSuccess; 
			
			//Set callback 
			_uploadersQueueCallback = callback; 
			
			//$(self).bind('uploadifyAllComplete', {'action':callback}); 
			
			//Checking queue 
			if ($('#'+settings.id+'Queue').html() != '') {
				_uploader.changeScriptData(options.data, reset);
				_uploader.upload(null); //Start upload  
			} else {
				// Instantly call callback function 
				callback(); 
			}
		};
		
		/**
		 * Raise success callback from external 
		 */
		this.raiseSuccess = function(){
			if (typeof settings.onSuccess == 'function') settings.onSuccess(); 
		};
		
	}; 
	
	/**
	 * Queue for uploaders 
	 */
	UploadersQueue = function(){
		var counter = 0, 
			instances = new Array(),
			externalCallback = function(){}, 
			onInstanceCompleted = function(instance){
				counter--;
				if (counter <= 0) {
					onAllComplete(instance);
				}
			},
			onAllComplete = function(instance){
				// Call external callback
				externalCallback(); 
				if (instances.length == 1) {
					instance.raiseSuccess(); 
				}
			}; 
		
		/**
		 * Add uploader to queue 
		 * @param uploader
		 */
		this.add = function(uploader) {
			instances.push(uploader);
			counter++; 
		}; 
		
		/**
		 * Initiate upload for object
		 * Used only for non auto mode  
		 * @param object
		 */
		this.start = function(object, options){
			$.each(instances, function(index, instance){
				if(instance.checkObject(object)) {
					//Add callback on complete 
					instance.upload(function(){onInstanceCompleted(instance);}, options); 
				}
			}); 
		};
		
		/**
		 * Assign event handler for event on complete all instances 
		 * @param handler - function 
		 */
		this.assignHandler = function(handler){
			if (typeof handler == 'function') externalCallback = handler; 
		}; 
		
	}; 
	
	var uploadersQueue = new UploadersQueue(); 
	
	var methods = {
			
			/**
			 * Init uploader instance for element 
			 * @param options
			 * 		lang - Current user language 
			 * 		moduleName - Name of module 
			 * 		moduleAction - Action for module 
			 * 		multiple - Multiple
			 * @returns
			 */
			init : function(options) {
				
				return this.each(function(){
					
					var self = this;
					if (options == undefined) $.error('modFiles: options is undefined!');
					
					if (options.lang == undefined) {
						var _lang = $.modScripts('get','lang'); 
						if (_lang == undefined) {
							$.error('modFiles: lang is undefined');
						} else {
							options.lang = _lang; 
						}
					} 
						
					if (options.moduleName == undefined) $.error('modFiles: moduleName is undefined'); 
					if (options.moduleAction == undefined) $.error('moduleFiles: moduleAction is undefined');
					
					if (options.multiple == undefined) options.multiple = false; 
					if (options.auto == undefined) options.auto = true;
					
					//Add id attribute 
					var _name = $(this).attr('name'); 
					$(this).attr('id', 'id-'+_name); 
					
					//Create instance for uploader 
					var instance = new UploadifyWrapper(options, this); 
					
					//If not auto put in array of instances 
					if (!options.auto) {
						if (uploadersQueue == undefined) {
							uploadersQueue = new UploadersQueue(options); 
						}
						uploadersQueue.add(instance);
					}
				});
			},
			
			/**
			 * Start upload 
			 * @param options
			 * 		data - additional script data 
			 * 		onComplete - handler on complete 
			 * 		onSuccess - handler on success upload of file(s)
			 * 		
			 * @returns
			 */
			upload : function(options) {
				var _options = $.extend({
						data : {},
						onComplete : function(){},
						onSuccess : function(){}
				}, options); 
				return this.each(function(){
					var self = this; 
					uploadersQueue.start(this, options);
				});
			}
			
	}; 
	
	// Public access
	$.fn.modFiles = function(method,options){
		if (methods[method]){
			methods[method].apply(this,Array.prototype.slice.call(arguments,1)); 
		} else if(typeof method == 'object') {
			methods['init'].apply(this, Array.prototype.slice.call(arguments,0)); 
		} else {
			$.error('modFile: method \''+method+'\' not found!'); 
		}
	}; 
	
	
	// Global settings for files module
	/**
	 * options 
	 * 		onComplete - handler for complete all instances 
	 */
	$.modFiles = function(options) {
		if (options != undefined) {
			if (typeof options.onComplete == 'function') {
				uploadersQueue.assignHandler(options.onComplete); 
			}
		}
	}; 
	
})(jQuery); 