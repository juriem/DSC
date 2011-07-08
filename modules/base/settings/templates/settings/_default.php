<?php
/**
 * Settings 
 */
Scripts::addModuleLib('html'); 
?>

<div id="id-news-admin-settings" class="ui-section parent">
<div class="section-title">
	<h1><?php echo Languages::getText('settings.title.main_title', 'Настройки'); ?></h1>
</div>
<?php 
if ($items) {
?>
<table>
	<tr class="header">
		<th><?php echo Languages::getText('settings.columns.code', 'Системный код');?></th>
		<th class="column"><?php echo Languages::getText('settings.columns.setting_name', 'Название'); ?></th>
		<th class="column"><?php echo Languages::getText('settings.columns.value', 'Значение');?></th>
		<th class="column"><?php echo Languages::getText('global.labels.actions', 'Действия'); ?></th>
	</tr>
<?php 
foreach($items as $item) {
?>
	<tr class="row parent">
		<td><strong><?php echo $item->code; ?></strong></td>
		<td><?php echo $item->setting_name; ?></td>
		<td class="value-holder">
			<?php 
			if ($item->type == 'value' || $item->type == 'yesno' || $item->type == 'textvalue') {
				echo $item->single_value;
			} else {
				if (mb_strlen($item->value) > 100) {
					$item->value = mb_substr($item->value, 0, 200) . ' ...';  	
				}
				echo $item->value; 
			}
			?>
		</td>
		<td>
			<div class="buttons">
				<a data-action="edit" class="action" href="#"><?php echo Languages::getText('global.actions.edit');?></a>
			</div>
			<?php HTMLForm::addHiddenFields($item, 'id');?>
		</td>
		
	</tr>
<?php 		
}
?>	
	
</table>
<?php 	
}
?>

<script rel="inline">
//Holder 
var holder = $('#id-news-admin-settings');
//Init buttons
$(holder).modHtml('buttons'); 

$(holder).find('.action[data-action="edit"]').modAjax({
	moduleName : 'settings', 
	moduleAction : 'settings/edit',
	fetchPattern : [[null,'%']],
	getResponse : function(response, sender) {

		var rowParent = $(sender).parents('.parent').get(0); 
		
		//Checking for dialog 
		var dialogBody = $('#id-dialog-body');
		if ($(dialogBody).size() == 0) {
			dialogBody = $('<div id="id-dialog-body"/>');
			$('body').append();
		}
		$(dialogBody).html(response); 
		$(dialogBody).attr('title',$(dialogBody).find('.data.setting_name').val()); 	
		$(dialogBody).find('.tabs').tabs();
		$(dialogBody).modHtml('buttons'); 

		

		//Update action
		$(dialogBody).find('.action[data-action="update"]').modAjax({
			moduleName: 'settings',
			moduleAction : 'settings/update',
			fetchPattern:[[null,'data[%]'],[$(dialogBody).find('div.value'),'data[values][%i][%]']],
			onSuccess : function(result) {
				$(rowParent).find('td.value-holder').modAjax('html',{
					moduleName : 'settings', moduleAction : 'settings/get_value', 
					fetchPattern : [[null,'%']],
					hideBusy:true
				}); 
				
				$(dialogBody).dialog('close');
				$(dialogBody).remove(); 
			}
		}); 

		//Cancel action
		$(dialogBody).find('.action[data-action="cancel"]').click(function(e){
			e.preventDefault();
			$(dialogBody).dialog('close');
			$(dialogBody).remove();
		});

		$(dialogBody).dialog({modal:true,width:'auto',resizable:false});
		 
	}
}); 
</script>


</div>