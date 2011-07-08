<?php
/**
 * Edit module template 
 */
?>



<div id="id-modules-edit" class="gui-section parent">

<input class="data id" type="hidden" value="<?php echo $data->id;?>">
<input class="data is_system" type="hidden" value="<?php echo $data->is_system;?>">
<input class="data is_add" type="hidden" value="<?php echo $data->is_add;?>">

<h2 class="gui-section-title"><?php echo (isset($_newMode)?Languages::getText('global.titles.add'):Languages::getText('global.titles.edit')); ?></h2>
	
	<table>
		<tr>
			<td class="gui-label"><?php echo Languages::getText('modules.labels.module_name', 'Системное имя модуля', 'ru')?>:</td>
			<td><input class="data module_name" type="text" value="<?php echo $data->module_name; ?>"></td>
		</tr>
		
		<tr>
			<td class="gui-label"><?php echo Languages::getText('modules.labels.names', 'Название и описание', 'ru')?></td>
			<td id="id-language-values">
				<?php App::executeModule('languages', 'values/_default/modules/#module_values/title:string:title,description:text:description/'.$data->id.'/yes'); ?>
			</td>
		</tr>
		
		
		<tr>
			<td class="label"><?php echo Languages::getText('modules.labels.is_system');?></td>
			<td><input type="checkbox" name="is_system"></td>
		</tr>
		<tr>
			<td class="label"><?php echo Languages::getText('modules.labels.is_add');?></td>
			<td><input type="checkbox" name="is_add"></td>
		</tr>
		
		
		<tr>
			<td colspan="2">
				<div class="buttons" style="float:right;">
					<a class="action update" href="#"><?php echo Languages::getText('global.actions.update'); ?></a>
				</div>
			</td>
		</tr>
		
	</table>
	
	
</div>

<?php 
Scripts::addModuleLib('html'); 
?>

<script rel="inline">
var holder = $('#id-modules-edit');
$(holder).modHtml('checkboxes'); 
$(holder).find('.action.update').modAjax('assign', {
	moduleName : 'developer',
	moduleAction : 'modules/update', 
	lang : '<?php echo Languages::getCurrentLanguage(); ?>',
	fetchPattern : [
	            	[null,'data[%]','id,module_name'],
	            	[$(holder).find('#id-language-values .value'), 'data[values][%i][%]', 'language_id,title,description']],
	onSuccess : function(result, sender) {
		alert('<?php echo Languages::getJText('global.messages.success_updated')?>'); 
		window.location.href = '__BASE_URL__/_developer/modules'; 
	}	
}); 
</script>