/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

/*
 * Remaded version of uploadify 
*/

(function($){
	
	
	/**
	 * Uploader class 
	 */
	Uploader = function(options, self){
		
		var uploader = this, 
			/**
			 * init settings 
			 */
			settings = $.extend({
				id              : $(self).attr('id'), // The ID of the object being Uploadified
				uploader        : '<?php echo URL::getBaseUrl();?>/files/scripts/assets/uploadify/uploadify.swf', // The path to the uploadify swf file
				script          : '<?php echo URL::getBaseUrl();?>/', // The path to the uploadify backend upload script
				expressInstall  : null, // The path to the express install swf file
				folder          : '', // The path to the upload folder
				height          : 64, // The height of the flash button
				width           : 64, // The width of the flash button
				cancelImg       : '<?php echo URL::getBaseUrl();?>/files/scripts/assets/uploadify/cancel.png', // The path to the cancel image for the default file queue item container
				wmode           : 'opaque', // The wmode of the flash file
				scriptAccess    : 'sameDomain', // Set to "always" to allow script access across domains
				fileDataName    : 'Filedata', // The name of the file collection object in the backend upload script
				method          : 'POST', // The method for sending variables to the backend upload script
				queueSizeLimit  : 999, // The maximum size of the file queue
				simUploadLimit  : 1, // The number of simultaneous uploads allowed
				queueID         : false, // The optional ID of the queue container
				scriptData		: {},
				displayData     : 'percentage', // Set to "speed" to show the upload speed in the default queue item
				removeCompleted : true, // Set to true if you want the queue items to be removed when a file is done uploading
				onInit          : function() {}, // Function to run when uploadify is initialized
				onSelect        : function() {}, // Function to run when a file is selected
				onSelectOnce    : function() {}, // Function to run once when files are added to the queue
				onQueueFull     : function() {}, // Function to run when the queue reaches capacity
				onCheck         : function() {}, // Function to run when script checks for duplicate files on the server
				onCancel        : function() {}, // Function to run when an item is cleared from the queue
				onClearQueue    : function() {}, // Function to run when the queue is manually cleared
				onError         : function() {}, // Function to run when an upload item returns an error
				onProgress      : function() {}, // Function to run each time the upload progress is updated
				onComplete      : function() {}, // Function to run when an upload is completed
				onAllComplete   : function() {}  // Function to run when all uploads are completed
			}, options),
			/**
			 * Get element id 
			 */
			_elementId = $(self).attr('id'),
			_uploaderId = _elementId + 'Uploader',
			_upload = function(ID, checkComplete) {
				if (!checkComplete) checkComplete = false;
				$('#'+_elementId+'Uploader').get(0).startFileUpload(ID, checkComplete);
			},
			_cancel = function(ID){
				$('#'+_elementId+'Uploader').get(0).cancelFileUpload(ID, true, true, false);
			},
			_clear = function() {
				$('#'+_elementId+'Uploader').get(0).clearFileUploadQueue(false);
			};
			
			//Init data and interface 
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
			if (settings.scriptData) {
				var scriptDataString = '';
				for (var name in settings.scriptData) {
					scriptDataString += '&' + name + '=' + settings.scriptData[name];
				}
				data.scriptData = escape(scriptDataString.substr(1));
			}
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
				$(self).css('display','none');
				$(self).after('<div id="' + _elementId + 'Uploader"></div>');
				swfobject.embedSWF(settings.uploader, settings.id + 'Uploader', settings.width, settings.height, '9.0.24', settings.expressInstall, data, {'quality':'high','wmode':settings.wmode,'allowScriptAccess':settings.scriptAccess},{},function(event) {
					if (typeof(settings.onSWFReady) == 'function' && event.success) settings.onSWFReady();
				});
				if (settings.queueID == false) {
					$("#" + _elementId + "Uploader").after('<div id="' + _elementId + 'Queue" class="uploadifyQueue"></div>');
				} else {
					$("#" + settings.queueID).addClass('uploadifyQueue');
				}
			}
			
			//Get uploader 
			var _uploader = $('#'+_elementId+'Uploader'); 
			
			//Start bindings
			if (typeof(settings.onOpen) == 'function') {
				$(self).bind("uploadifyOpen", settings.onOpen);
			}
			if (typeof(settings.onUpload) == 'function') {
				$(self).bind("uploadifyUpload", settings.onUpload);
			}
			
			//Select
			$(self).bind("uploadifySelect", {'action': settings.onSelect, 'queueID': settings.queueID}, function(event, ID, fileObj) {
				if (event.data.action(event, ID, fileObj) !== false) {
					var byteSize = Math.round(fileObj.size / 1024 * 100) * .01;
					var suffix = 'KB';
					if (byteSize > 1000) {
						byteSize = Math.round(byteSize *.001 * 100) * .01;
						suffix = 'MB';
					}
					var sizeParts = byteSize.toString().split('.');
					byteSize = sizeParts[0]; 
					if (sizeParts.length > 1) {
						byteSize += '.' + sizeParts[1].substr(0,2);
					} 
					if (fileObj.name.length > 20) {
						fileName = fileObj.name.substr(0,20) + '...';
					} else {
						fileName = fileObj.name;
					}
					queue = '#' + _elementId + 'Queue';
					if (event.data.queueID) {
						queue = '#' + event.data.queueID;
					}
					$(queue).append('<div id="' + _elementId + ID + '" class="uploadifyQueueItem"> ' 
							+ '<div class="cancel">'
							+	'<a href="#"><img src="' + settings.cancelImg + '" border="0" /></a>'
							+'</div>'
							+'<span class="fileName">' + fileName + ' (' + byteSize + suffix + ')</span><span class="percentage"></span>'
							+'<div class="uploadifyProgress">'
							+'<div id="' + _elementId + ID + 'ProgressBar" class="uploadifyProgressBar"><!--Progress Bar--></div>'
							+'</div>'
						+'</div>');
					//Assign cancel event 
					$(queue).find('div.cancel a').click(function(e){
						_cancel(ID); 
					}); 
				}
			});
			
			//Checking if exists ???
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
								$('#'+_uploaderId).get(0).cancelFileUpload(key, true, true);
							}
						}
					}
					if (single) {
						_upload(singleFileID, true);
					} else {
						_upload(null, true); 
					}
				}, "json");
			});
			
			//Cancel action 
			$(self).bind("uploadifyCancel", {'action': settings.onCancel}, function(event, ID, fileObj, data, remove, clearFast) {
				if (event.data.action(event, ID, fileObj, data, clearFast) !== false) {
					if (remove) { 
						var fadeSpeed = (clearFast == true) ? 0 : 250;
						$("#" + _elementId + ID).fadeOut(fadeSpeed, function() { $(this).remove(); });
					}
				}
			});
			
			// Select once 
			$(self).bind("uploadifySelectOnce", {'action': settings.onSelectOnce}, function(event, data) {
				event.data.action(event, data);
				if (settings.auto) {
					if (settings.checkScript) {
						_upload(null,false);
					} else {
						_upload(null,true);
					}
				}
			});
			// Queue full 
			$(self).bind("uploadifyQueueFull", {'action': settings.onQueueFull}, function(event, queueSizeLimit) {
				if (event.data.action(event, queueSizeLimit) !== false) {
					alert('The queue is full.  The max size is ' + queueSizeLimit + '.');
				}
			});
			//Clear queue 
			$(self).bind("uploadifyClearQueue", {'action': settings.onClearQueue}, function(event, clearFast) {
				var queueID = (settings.queueID) ? settings.queueID : _elementId + 'Queue';
				if (clearFast) {
					$("#" + queueID).find('.uploadifyQueueItem').remove();
				}
				if (event.data.action(event, clearFast) !== false) {
					$("#" + queueID).find('.uploadifyQueueItem').each(function() {
						var index = jQuery('.uploadifyQueueItem').index(this);
						$(this).delay(index * 100).fadeOut(250, function() { $(this).remove(); });
					});
				}
			});
			
			//Progress 
			$(self).bind("uploadifyProgress", {'action': settings.onProgress, 'toDisplay': settings.displayData}, function(event, ID, fileObj, data) {
				if (event.data.action(event, ID, fileObj, data) !== false) {
					$("#" + _elementId + ID + "ProgressBar").animate({'width': data.percentage + '%'},250,function() {
						if (data.percentage == 100) {
							$(this).closest('.uploadifyProgress').fadeOut(250,function() {$(this).remove();});
						}
					});
					if (event.data.toDisplay == 'percentage') displayData = ' - ' + data.percentage + '%';
					if (event.data.toDisplay == 'speed') displayData = ' - ' + data.speed + 'KB/s';
					if (event.data.toDisplay == null) displayData = ' ';
					$("#" + _elementId + ID).find('.percentage').text(displayData);
				}
			});
			
			
			//Complete 
			$(self).bind("uploadifyComplete", {'action': settings.onComplete}, function(event, ID, fileObj, response, data) {
				if (event.data.action(event, ID, fileObj, unescape(response), data) !== false) {
					$("#" + _elementId + ID).find('.percentage').text(' - Completed');
					if (settings.removeCompleted) {
						$("#" + _elementId + ID).fadeOut(250,function() {jQuery(this).remove();});
					}
					$("#" + _elementId + ID).addClass('completed');
				}
			});
			if (typeof(settings.onAllComplete) == 'function') {
				$(self).bind("uploadifyAllComplete", {'action': settings.onAllComplete}, function(event, data) {
					if (event.data.action(event, data) !== false) {
						errorArray = [];
					}
				});
			}
			
			//Error processing 
			var errorArray = [];
			$(self).bind("uploadifyError", {'action': settings.onError}, function(event, ID, fileObj, errorObj) {
				if (event.data.action(event, ID, fileObj, errorObj) !== false) {
					var fileArray = new Array(ID, fileObj, errorObj);
					errorArray.push(fileArray);
					$("#" + _elementId + ID).find('.percentage').text(" - " + errorObj.type + " Error");
					$("#" + _elementId + ID).find('.uploadifyProgress').hide();
					$("#" + _elementId + ID).addClass('uploadifyError');
				}
			});
			
			
			//Public methods 
			/**
			 * 
			 */
			this.checkObject = function(object){
				if ($(self).get(0) === $(object).get(0)) return true; 
				return false; 
			}; 
			
			/**
			 * Start uploading 
			 * @param options - additional Scriptdata 
			 */
			this.upload = function(options) {
				var scriptData = $.extend(_settings.scriptData, (!options)?{}:options);
				var scriptDataString = '';
				$.each(scriptData, function(name,value){
					scriptDataString += '&' + name + '=' + value;
				});
				settingValue = escape(scriptDataString.substr(1));
				$('#'+_elementId+'Uploader').get(0).updateSettings('scriptData', settingValue);
			}; 
			
	}; 
	
	
	$.fn.uploadify = function(method, options) {
		
		var _instances = new Array(); 
		
		if (typeof method == 'object' || method == 'init') {
			if (typeof method == 'object') options = method; 
			
			return this.each(function(){
				var _instance = new Uploader(options, this);
				_instances.push(_instance); 
			});
		} else if (method == 'upload') {
			return this.each(function(){
				$.each(_instances, function(index, _instance){
					if (_instance.checkObject(self)) {
						_instance.upload(options); 
					}
				});
			});
		}
	};
})(jQuery);