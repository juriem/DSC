<?php
/**
 * System and modules settings 
 */
?>

<div id="id-system-settings" class="parent gui-section">

<h2><?php echo Languages::getText('settings.titles.main_title', 'Настройки системы', 'ru')?></h2>

<table>
	<tr class="actions">
		<td colspan="5">
			<div class="buttons" style="float:right; clear:right;">
				<a href="__BASE_URL__/_developer/settings/add"><?php echo Languages::getText('settings.actions.add_new'); ?></a></td>
			</div>
	</tr>
	<tr>
		<td class="label"<?php echo Languages::getText('settings.labels.module_name', 'Module code', 'en'); ?>></td>
		<td colspan="4" align="left">
		<select class="filter module_id">
			<option value=''><?php echo Languages::getText('global.select_values.all');?></option>
			<option value='global'>Global</option>
			<?php 
			foreach($modules as $module) {
				echo '<option value="'.$module->id.'">'.$module->module_name.'</option>';
			}
			?>
		</select>
		</td>
	</tr>
	<tr class="header">
		<th>#</th>
		<th><?php echo Languages::getText('settings.columns.module_name');?></th>
		<th><?php echo Languages::getText('settings.columns.code'); ?></th>
		<th><?php echo Languages::getText('settings.columns.type');?></th>
		<th><?php echo Languages::getText('global.columns.actions'); ?></th>
	</tr>	
</table>
</div>

<script rel="inline">
var holder = $('#id-system-settings'); 


$('#id-system-settings select.filter').change(function(){
	$(holder).find('.gui-table-row').remove(); 
	
	$('#id-system-settings tr.header').modAjax('html',{
		moduleName : 'developer',
		moduleAction : 'settings/get_list',
		fetchPattern : [[null,'data[module_id]','module_id','filter']],
		lang : '<?php echo Languages::getCurrentLanguage(); ?>',
		insertAfter : true, 
		onComplete : function(c){
			
			$('#id-system-settings .gui-table-row').each(function(){
				$(this).find('.action.delete').modAjax('assign',{
					moduleName : 'developer',
					moduleAction : 'settings/delete',
					lang : '<?php echo Languages::getCurrentLanguage(); ?>',
					fetchPattern : [[null,'id','id']],
					onBeforeAction : function(){
						if (!confirm('<?php echo Languages::getJText('settings.confirms.delete_settings!', 'Delete settings?', 'en');?>')) return false;
						return true; 
					},
					onSuccess : function(result, sender){
						$($(sender).parents('.parent').get(0)).remove(); 
					}
				});
			}); 
			 			
		}
	});
}).change(); 


</script>
