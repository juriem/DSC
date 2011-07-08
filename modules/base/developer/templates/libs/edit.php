<?php
/**
 * Edit or Add libarary
 */ 
?>


<div id="id-holder" class="gui-section parent">
	<input type="hidden" class="data id" value="<?php echo $data->id; ?>">

	<table>
	<tr>	
		<td class="label"><?php echo Languages::getText('scripts.labels.code')?>:</td>
		<td><input type="text" class="data code" value="<?php echo $data->code; ?>"></td>
	</tr>
	<tr>
		<td class="label"><?php echo Languages::getText('scripts.labels.folder_name'); ?>:</td>
		<td><input type="text" class="data folder_name" value="<?php echo $data->folder_name;?>"></td>
	</tr>
	<tr>
		<td class="label"><?php echo Languages::getText('script.labels.additional_script'); ?>:</td>
		<td>
			<textarea class="data additional_script"><?php echo $data->additional_script; ?></textarea>
		</td>
	</tr>	
	
	<?php 
	if ($items) {
		foreach($items as $item) {
			$itemTypeCSS = ''; 
			$itemTypeJS = ''; 
			if ($item->item_type == 'css') $itemTypeCss = ' selected=""'; 
			if ($item->item_type == 'js') $itemTypeJS = ' selected=""';
	?>
	<tr class="item parent">
		<td class="label"><?php echo Languages::getText('scripts.labels.library_item'); ?></td>
		<td>
			<table>
				<tr>
					<th><?php echo Languages::getText('scripts.columns.item_type'); ?></th>
					<th><?php echo Languages::getText('scripts.columns.item_name');?></th>
					<th><?php echo Languages::getText('global.columns.actions'); ?></th>
				</tr>
				<tr>
					<td>
						<select class="data item_type">
							<option value="css"<?php echo $itemTypeCSS;?>><?php echo Languages::getText('scripts.select_values.item_type_css');?></option>
							<option value="js"<?php echo $itemTypeJS;?>><?php echo Languages::getText('scripts.select_values.item_type_js');?></option>
						</select>
					</td>
					<td>
						<input type="text" class="data item_name" value="<?php echo $item->item_name;?>">
					</td>
					<td>
						<div class="buttons">
							<a href="#" class="action delete"><?php echo Languages::getText('global.actions.delete'); ?></a>
						</div>
						<input type="hidden" class="data id" value="<?php echo $item->id; ?>">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php 		
		}
	}
	?>
	
	<tr class="new-item item parent">
		<td class="label"><?php echo Languages::getText('scripts.labels.new_item');?>:</td>
		<td>
			<table>
				<tr>
					<th><?php echo Languages::getText('scripts.columns.item_type'); ?></th>
					<th><?php echo Languages::getText('scripts.columns.item_name');?></th>
					<th><?php echo Languages::getText('global.columns.actions'); ?></th>
				</tr>
				<tr>
					<td>
						<select class="data item_type">
							<option value="css"<?php echo $itemTypeCSS;?>><?php echo Languages::getText('scripts.select_values.item_type_css');?></option>
							<option value="js"<?php echo $itemTypeJS;?>><?php echo Languages::getText('scripts.select_values.item_type_js');?></option>
						</select>
					</td>
					<td>
						<input type="text" class="data item_name" value="">
					</td>
					<td>
						<div class="buttons">
							<a href="#" class="action update"><?php echo Languages::getText('global.action.save');?></a>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
		
		
	<tr>
		<td colspan="2" align="right">
			<div class="buttons" style="float:right; clear:right;">
				<a href="#" class="action update_all"><?php echo Languages::getText('global.actions.update'); ?></a>
			</div>
		</td>
	</tr>	
	</table>
</div>

<script rel="inline">
var holder = $('#id-holder');
var counter = 0; 


//Update item 
$(holder).find('.action.update_all').click(function(e){
	e.preventDefault(); 
	 
	var id = $(holder).find('.data.id').val();
	var data = $.ajaxPost.createData({}, 'data[id]',id);  
	var code = $(holder).find('.data.code').val();
	data = $.ajaxPost.createData(data, 'data[code]',code);
	var folder_name = $(holder).find('.data.folder_name').val();
	data = $.ajaxPost.createData(data, 'data[folder_name]',folder_name);  
	var additional_script = $(holder).find('.data.additional_script').val(); 
	data = $.ajaxPost.createData(data, 'data[additional_script]', additional_script); 

	//Processing item
	$(holder).find('tr.item').each(function(i){
		var paramName = 'data[items]['+i+']'; 
		var item_type = $(this).find('.data.item_type').val(); 
		data = $.ajaxPost.createData(data, paramName+'[item_type]', item_type); 
		var item_name = $(this).find('.data.item_name').val(); 
		data = $.ajaxPost.createData(data, paramName+'[item_name]', item_name); 
		var id = $(this).find('.data.id').val(); 
		data = $.ajaxPost.createData(data, paramName+'[id]', id); 
	}); 

	$.ajaxPost('developer','libs/update',''); 
	$.ajaxPost.execute(data, function(){window.location.href='/_developer/libs';}, function(){alert('System error!');});
	
}); 

//Delete item 
$(holder).find('tr.item .action.delete').click(function(e){
	e.preventDefault(); 

	if (!confirm('Delete item?')) return false; 
	var parent  = $(this).parents('tr.item'); 
	var id = $(parent).find('.data.id').val(); 

	$.ajaxPost('developer','libs/delete_item',''); 
	$.ajaxPost.execute({'id':id}, function(result){$(parent).remove();}, function(){}); 
	
}); 


//Add new item 
$(holder).find('tr.new-item .action.update').click(function(e){
	e.preventDefault(); 
	var parent = $(this).parents('tr.new-item'); 

	var item_type = $(parent).find('.data.item_type').val(); 
	var item_name = $(parent).find('.data.item_name').val(); 

	if (item_name == '') {
		alert('Please enter value for item!');  
		return false; 
	}
	
	var itemTypeCSS = (item_type == 'css')?' selected':''; 
	var itemTypeJS = (item_type=='js')?' selected':''; 
	var code = '<tr class="item item-'+counter+'">'; 
	code += '<td class="label">Item</td><td><table>'+ 
			'<tr>' + 
			'	<th>Type</th>' + 
			'	<th>Name</th>' + 
			'	<th>Actions</th>' +
			'</tr>' + 
			'<tr>' + 
			'	<td>' + 
			'		<select class="data item_type">' + 
			'			<option'+itemTypeCSS+' value="css">StyleSheet</option>' + 
			'			<option'+itemTypeJS+' value="js">JavaScript</option>' + 
			'		</select>' + 
			'	</td>' + 
			'	<td>' + 
			'		<input type="text" class="data item_name" value="'+item_name+'">' + 
			'	</td>' + 
			'	<td><a href="#" class="action delete">Delete</a><input type="hidden" class="data id" value="0"></td>' + 
			'</tr>' + 
		'</table>' + 
	'</td>' + 
	'</tr>';

	$(code).insertBefore(parent);

	$(holder).find('tr.item-'+counter+' .action.delete').click(function(e){
		e.preventDefault();
		if (!confirm('Delete item?')) return false; 
		var _parent = $(this).parents('tr.item'); 
		var id = $(_parent).find('data id').val(); 
		if (id != 0) {
			//Processing delete via ajax 
		}
		$(_parent).remove();
	}); 
	counter++; 

	$(parent).find('.data.item_name').val(''); 
	
}); 

</script>

