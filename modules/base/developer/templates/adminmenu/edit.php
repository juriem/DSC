<?php
/**
 * Edit or Add menu 
 */
?>

<div id="id-adminmenu-edit" class="parent gui-section">

<h1 class="gui-section-title">
<?php 
if (isset($_newMode)) {
	echo Languages::getText('global.titles.new'); 
} else {
	echo Languages::getText('global.titles.edit'); 
}
?>
</h1>

<?php HTMLForm::addHiddenFields($data, 'id,p_id'); ?>


<table>
	<tr>
		<td class="label"><?php echo Languages::getText('menu.labels.is_group', 'Group menu');?></td>
		<td><input type="checkbox" data-field="is_group" data-value="<?php echo $data->is_group;?>"></td>
	</tr>

	<!-- Module -->
	<tr class="menu">
		<td class="label"><?php echo Languages::getText('menu.labels.module_id', 'Module', 'en'); ?></td>
		<td>
			<select data-field="module_id" class="data module_id">
			<?php 
			foreach($modules as $module) {
				echo '<option value="'.$module->id.'">'.$module->module_name.'</option>'; 
			}
			?>
			</select>
		</td>
	</tr>
	
	<tr class="group">
		<td class="label"><?php echo Languages::getText('menu.labels.url_code'); ?></td>
		<td><input data-field="url_code" class="data url_code" type="text"></td>
	</tr>
	
	<!-- Controller -->
	<tr class="menu">
		<td class="label"><?php echo Languages::getText('menu.labels.controller', 'Controller', 'en'); ?>:</td>
		<td><input data-field="default_controller" class="data default_controller" type="text" size="30" value="<?php echo $data->default_controller; ?>"></td>
	</tr>	
	<!-- Task -->
	<tr class="menu">
		<td class="label"><?php echo Languages::getText('menu.labels.task', 'Task', 'en'); ?>:</td>
		<td><input data-field="default_task" class="data default_task" type="text" size="30" value="<?php echo $data->default_task; ?>"></td>
	</tr>	
	<tr>
		<td class="label">Title and description:</td>
		<td class="language-value">
			<?php App::executeModule('languages','values/_default/menu/#menu_values/title:string:title,description:text:description/'.$data->id); ?>
		</td>
	</tr>
	
	<tr>
		<td colspan="2">
			<div class="buttons" style="float:right; clear:right;">
				<a data-action="update" class="action update" href="#"><?php echo Languages::getText('global.actions.update'); ?></a>
			</div>
		</td>
	</tr>
</table>

</div>

<?php 
Scripts::addModuleLib('html'); 
?>
<script rel="inline">

var holder = $('#id-adminmenu-edit');

function updateGroupMenu() { 
	
	var value=$(holder).find('[data-field="is_group"]').attr('checked'); 
	if (value) {
		$(holder).find('tr.menu').hide();
		$(holder).find('tr.group').show();		
	} else {
		$(holder).find('tr.menu').show();
		$(holder).find('tr.group').hide();
	}
}; 
updateGroupMenu(); 
$(holder)
	.modHtml('buttons')
	.modHtml('checkboxes', {onClick:function(){updateGroupMenu();}}); 


$(holder).find('.tabs').tabs();
$(holder).find('[data-field="module_id"]').val(<?php echo $data->module_id; ?>);

$(holder).find('[data-action="update"]').modAjax('assign', {
	moduleName : 'developer',
	moduleAction : 'adminmenu/update',
	lang : '<?php echo Languages::getCurrentLanguage(); ?>',
	fetchPattern : [[null, 'data[%]'], [$(holder).find('div.value'),'data[values][%i][%]']],
	onSuccess : function() { window.location.href = '__BASE_URL__/_developer/adminmenu'; }
});
</script>


