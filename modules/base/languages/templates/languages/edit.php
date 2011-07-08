<?php
/**
 * Edit language 
 */
?>

<div id="id-languages-edit" class="gui-section parent">
	<h1 class="gui-section-title"><?php echo (isset($_newMode))?Languages::getText('global.titles.new'):Languages::getText('global.titles.edit'); ?></h1>
	
	<?php HTMLForm::addHiddenFields($data, 'id'); ?>
	
	<table>
		<tr>
			<td class="label"><?php echo Languages::getText('global.labels.code'); ?></td>
			<td><?php HTMLForm::addInputBox($data, 'url_code', 10); ?></td>
		</tr>	
		
		<tr>
			<td class="label"><?php echo Languages::getText('languages.labels.display_name');?>:</td>
			<td><?php HTMLForm::addInputBox($data, 'display_name');?></td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('languages.labels.display_short_name', 'Сокращенное название', 'ru')?></td>
			<td><?php HTMLForm::addInputBox($data, 'display_short_name', 10); ?></td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('global.labels.names'); ?></td>
			<td id="id-language-values">
				<?php App::executeModule('languages', 'values/_default/languages/#language_values/language_name:string:name/'.$data->id); ?>
			</td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('global.labels.is_enabled'); ?></td>
			<td><?php HTMLForm::addCheckbox($data, 'is_enabled'); ?></td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('global.lables.is_admin_default'); ?></td>
			<td><?php HTMLForm::addCheckbox($data, 'is_admin_default');?></td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('global.labels.is_default'); ?></td>
			<td><?php HTMLForm::addCheckbox($data, 'is_default');?></td>
		</tr>
		
		<!-- Actions -->
		<tr>
			<td colspan="2">
				<div class="buttons" style="float:right; clear: right;">
					<a data-action="update" class="action update" href="#"><?php echo Languages::getText('global.actions.update'); ?></a>
				</div>
			</td>
		</tr>
	
	</table>
</div>

<?php 
Scripts::addModuleLib('helper'); 
?>

<script rel="inline">
//Init checkboxes
$('#id-languages-edit').modHtml('checkboxes').modHtml('buttons');
//Init actions 
$('#id-languages-edit .action[data-action="update"]').modAjax('assign', {
	moduleName : 'languages',
	moduleAction : 'languages/update',
	lang : '<?php echo Languages::getCurrentLanguage();?>',
	fetchPattern : [[null,'data[%]'],
	            	[$('#id-language-values div.value'),'data[values][%i][%]']],
	onSuccess : function(){ window.location.href = '<?php echo URL::build('languages/languages'); ?>'; }
});
</script>

