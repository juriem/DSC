//@depends: ajax:js//
/**
 * Assign languages actions 
 * Displays dialog for edit values  
 */

(function($){
	
	/**
	 * Private part 
	 */
	
	/**
	 * Public part
	 * options 
	 * 		- lang - Current system language 
	 *		- moduleName - Name of module 
	 *		- tableName - Name of table for value 
	 *		- idHolderName - Name of class for id 
	 *		- columns - columns definition 
	 *		- itemName - name of column for item id (optional)
	 *		- title - Dialog title (optional)
	 *		- valueHolder - Value holder (optional)
	 */
	$.fn.modLanguages = function(options) {
		return this.each(function(){
			
			var self = this; 
			
			//Check sender 
			if (options == undefined) $.error('modLanguages: options is undefined!'); 
			if (options.lang == undefined) $.error('modLanguages: lang is undefined!'); 
			if (options.moduleName == undefined) $.error('modLanguages: moduleName is undefined!'); 
			if (options.tableName == undefined) $.error('modLanguages: tableName is undefined!'); 
			if (options.columns == undefined) $.error('modLanguages: columns is undefined!');
			
			if (options.idHolderName == undefined) options = $.extend(options, {idHolderName:'id'});
			if (options.itemName == undefined) options = $.extend(options, {itemName:'item_id'});
			if (options.title == undefined) options = $.extend(options, {title:''});
			
			//Processing columns
			var _columns = ''; 
			var _arr = options.columns.split(','); 
			for(var i=0; i < _arr.length;i++) {
				var _parts =  _arr[i].split(':');
				_columns += ((_columns=='')?'':',') + _parts[0];  
			}
			_columns += ',language_id'; 
		 
			//Prepare base data 
			var id = $($(this).parents('.parent').get(0)).find('.data[data-field='+options.idHolderName+']').val();
			var data = {
					'data[id]':id,
					'data[id_name]':options.itemName,
					'data[module_name]':options.moduleName,
					'data[table_name]':options.tableName,
					'data[columns]':options.columns
			};
			//Assign AJAX to link 
			$(this).modAjax('assign',{
				moduleName:'languages',
				moduleAction:'values/dialog',
				lang:options.lang,
				'data':data,
				getResponse : function(response){
					//Checking for holder 
					var container = $('#id-modlanguages-dialog'); 
					if ($(container).size() == 0) {
						container = $('<div id="id-modlanguages-dialog" class="parent"></div>'); 
						$('body').append(container); 
					}
					$(container)
						.html(response)
						.modHtml('buttons')
						.attr('title',options.title)
						.dialog({modal:true, width:'auto', resizable:false});
					
					$(container).find('.tabs').tabs();
					
					var valuesHolder = $(container).find('.language-value'); 
					
					$(container).find('.action[data-action="update"]').modAjax('assign',{
							moduleName:'languages',
							moduleAction:'values/update',
							lang:options.lang,
							'data':data,
							fetchPattern:[[valuesHolder,'data[values][%i][%]']],
							onSuccess : function(result,sender){
								//Checking if value holder exists
								
								if (options.valueHolder != undefined) {
									//var p = $(self).parents('.parent').get(0); 
									var valueHolder = options.valueHolder;// $(p).find('._value'); 
									
									if ($(valueHolder).size() != 0) {
										//get column_name 
										var columnName = $(valueHolder).attr('name'); 
										if (columnName != '') {
											var _data = $.extend(data, {'data[value_name]':columnName}); 
											
											$(valueHolder).modAjax('html',{
												moduleName:'languages',
												moduleAction:'values/get',
												lang:options.lang,
												'data':_data,
												hide : true
											});
											
										}
									}
								}
								$(container).dialog('destroy'); 
								$(container).remove();
							}
						}	
					); 
					
					
					//Cancel action
					$(container).find('.action.cancel').click(function(e){
						$(container).dialog('destroy'); 
						$(container).remove(); 
					});
				}
			}); 
		});
	};  
})(jQuery); 