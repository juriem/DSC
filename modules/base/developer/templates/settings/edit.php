<?php
/**
 * Edit or Add setting
 */
?>


<div id="id-system-edit-setting" class="gui-section parent">
	<input type="hidden" class="data id" value="<?php echo $data->id; ?>">
	
	<h1 class="gui-section-title"><?php echo Languages::getText('settings.title.')?></h1>
	
	<table>
		<!-- Code -->
		<tr>
			<td class="label">Code:</td>
			<td><input size="30" type="text" class="data code" value="<?php echo $data->code; ?>"></td>
		</tr>
		<!-- Module -->
		<tr>
			<td class="label">Module name:</td>
			<td>
				<select class="data module_id">
					<option value="">Global</option>
					<?php 
					foreach($modules as $module) {
						echo '<option value="'.$module->id.'">'.$module->module_name.(($module->is_system)?'*':'').'</option>'; 
					}
					?>
				</select>
			</td>
		</tr>
		<!-- Names -->
		<tr>
			<td class="label"><?php echo Languages::getText('settings.labels.setting_name'); ?></td>
			<td id="id-setting-names">
				<?php 
				App::executeModule('languages', 'values/_default/settings/#setting_names/setting_name:string:setting_name,description:text:description/'.$data->id); 
				?>
			</td>
		</tr>
		
		<!-- Type -->
		<tr>
			<td class="label"><?php echo Languages::getText('settings.label.value_type');?>:</td>
			<td>
				<select class="data type">
					<option value="value"><?php echo Languages::getText('settings.select_values.single_value'); ?></option>
					<option value="yesno"><?php echo Languages::getText('settings.select_values.yesno');?></option>
					<option value="textvalue"><?php echo Languages::getText('settings.select_values.text_value');?></option>
					<option value="string"><?php echo Languages::getText('settings.select_values.string'); ?></option>
					<option value="text"><?php echo Languages::getText('settings.select_values.text'); ?></option>
				</select>
			</td>
		</tr>
		
		<!-- Values -->
		<tr>
			<td class="label">Value(s):</td>
			<td>
				<div id="id-setting-values"></div>
			</td>
		</tr>
		
		
		
		<!-- Button -->
		<tr>
			<td colspan="2" align="right">
				<a href="#" class="action update">Update</a>
			</td>
		</tr>
		
		
	</table>
	
	
</div>


<script rel="inline">
var holder = $('#id-system-edit-setting'); 
//Set active module 
var module_id = '<?php echo $data->module_id; ?>'; 
$(holder).find('.data.module_id').val(module_id);
var type = '<?php echo $data->type; ?>';
if (type == '') type='value'; 
$(holder).find('.data.type').val(type); 

//Tabs 
$('#id-setting-names').tabs();
//Init buttons 
$(holder).find('a.action').button();
 
$(holder).find('.data.type').change(function(){ 
	$('#id-setting-values').modAjax('html', {
		moduleName : 'developer',
		moduleAction : 'settings/get_value',
		lang : '<?php echo Languages::getCurrentLanguage(); ?>',
		fetchPattern : [[null,'data[%]','id,type']],
		onComplete : function(c) {
			
			$(c).find('.data.single_value').val('<?php echo $data->single_value;?>'); 	
		}
	}); 
}).change(); 


$(holder).find('.action.update').modAjax('assign', {
	moduleName : 'developer',
	moduleAction : 'settings/update',
	lang : '<?php echo Languages::getCurrentLanguage(); ?>',
	fetchPattern : [[null,'data[%]','id,code,module_id,type'],
	            	[$('#id-setting-names .value'),'data[names][%i][%]','language_id,setting_name,description']],
	appendFetchPattern : function(self){
		var p = $(self).parents('.parent').get(0); 
		var type = $(p).find('.data.type').val(); 
		if (type == 'text' || type == 'string') {
			return [[$('#id-setting-values .value'),'data[values][%i][%]','language_id,value']]; 
		} else {
			return [[null,'data[%]','single_value']]; 
		}
	},
	onSuccess : function() { window.location.href = '__BASE_URL__/_developer/settings'; }	
});

</script>