<?php
/**
 * System configuration 
 * 
 */
?>

<div id="id-system-default" class="gui-section parent">

	<div class="gui-tabs">
		<div class="gui-tab">
			<a href="#id-database"><?php echo Languages::getText('settings.actions.database_settings', 'Database settings', 'en');?></a>
		</div>
		<div class="gui-tab">
			<a href="#id-system"><?php echo Languages::getText('settings.actions.system_settings', 'System settings', 'en');?></a>
		</div>
		<div class="gui-tab">
			<a href="#id-security"><?php echo Languages::getText('settings.actions.security', 'Security settings', 'en');?></a>
		</div>
	</div>

	<div id="id-database" class="gui-subsection gui-tab-content parent">
		<table>
			<tr>
				<td colspan="2"><h3><?php echo Languages::getText('settings.titles.database_settings','Database settings','en')?></h3></td>
			</tr>
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.database_driver', 'Database driver');?></td>
				<td>
					<select class="data driver">
						<option value="mysql">MYSQL</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.database_server', 'Database server', 'en');?>:</td>
				<td><input type="text" class="data host" value="<?php echo Config::getInstance()->database->host;?>"></td>
			</tr>
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.database_name', 'Database name','en');?>:</td>
				<td><input type="text" class="data database" value="<?php echo Config::getInstance()->database->database; ?>"></td>
			</tr>
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.tables_prefix', 'Table\'s prefix', 'en');?>:</td>
				<td><input type="text" class="data table_prefix" value="<?php echo Config::getInstance()->database->table_prefix;?>"></td>
			</tr>
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.database_user','User for conneting database', 'en');?>:</td>
				<td><input type="text" class="data user" value="<?php echo Config::getInstance()->database->user; ?>"></td>
			</tr>
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.database_password', 'Password for connecting database', 'en');?>:</td>
				<td><input type="text" class="data password" value="<?php echo Config::getInstance()->database->password; ?>"></td>
			</tr>
			<tr>
				<td colspan="2" align="right">
					<div class="buttons" style="float:right; clear:right;">
						<a href="#" class="action update">Update</a>
					</div>
					
					<input class="data section" type="hidden" value="database">
					
				</td>
				
			</tr>
		</table>
	</div>
	
	<div id="id-system" class="gui-subsection parent gui-tab-content parent">
		<table>
			<tr>
				<td colspan="2"><h3><?php echo Languages::getText('settings.titles.system_settings', 'System settings', 'en');?></h3></td>
			</tr>
			
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.application_id', 'Application ID', 'en');?>:</td>
				<td><input size="50" class="data app_id" type="text" disabled value="<?php echo Config::getInstance()->system->app_id; ?>">&nbsp;<a href="#" class="action generate_guid">Generate</a></td>
			</tr>
			
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.use_database_session_handler', 'User database handler for sessions', 'en')?>:</td>
				<td>
					<select class="data use_dbsession">
						<option value="yes"><?php echo Languages::getText('global.select_values.yes'); ?></option>
						<option value="no"><?php echo Languages::getText('global.select_values.no');?></option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.debug_mode', 'Debug mode', 'en');?>:</td>
				<td>
					<select class="data debug_mode">
						<option value="yes"><?php echo Languages::getText('global.select_values.yes'); ?></option>
						<option value="no"><?php echo Languages::getText('global.select_values.no');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.auto_create_tokens', 'Auto create tokens')?>:</td>
				<td>
					<select class="data auto_create_tokens">
						<option value="yes"><?php echo Languages::getText('global.select_values.yes'); ?></option>
						<option value="no"><?php echo Languages::getText('global.select_values.no');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.encode_scripts', 'Use BASE64 for encoding inline scripts', 'en');?>:</td>
				<td>
					<select class="data encode_scripts">
						<option value="yes"><?php echo Languages::getText('global.select_values.yes'); ?></option>
						<option value="no"><?php echo Languages::getText('global.select_values.no');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="label"><?php echo Languages::getText('settings.labels.timezone', 'Time zone', 'en')?>:</td>
				<td><input type="text" class="data timezone" value="<?php echo Config::getInstance()->system->timezone;?>"></td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="buttons" style="float:right; clear:right;">
						<a href="#" class="action update"><?php echo Languages::getText('global.actions.update');?></a>
					</div>
					
					<input type="hidden" class="data section" value="system">
					
				</td>
			</tr>
		</table>
	</div>
	
	<div id="id-security" class="gui-subsection parent gui-tab-content parent">
		<table>
			<tr>
				<td colspan="3"><h3><?php echo Languages::getText('settings.titles.securiry_settings', 'Security settings', 'en');?></h3></td>
			</tr>
			<tr id="id-developer" class="parent">
				<td class="label"><?php echo Languages::getText('settings.labels.change_developer_password', 'Change developer password', 'en');?>:</td>
				<td><input type="text" class="data password" value=""></td>
				<td>
					<div class="buttons">
						<a href="#" class="action update"><?php echo Languages::getText('global.actions.update');?></a>
					</div>
					<input type="hidden" class="data section" value="security">
				</td>
			</tr>
		</table>
	</div>
	
</div>

<?php 
Scripts::addModuleLib('html'); 
?>

<script rel="inline">
$('.data.debug_mode').val('<?php echo Config::getInstance()->system->debug_mode;?>'); 
$('.data.auto_create_tokens').val('<?php echo Config::getInstance()->system->auto_create_tokens;?>'); 
$('.data.encode_scripts').val('<?php echo Config::getInstance()->system->encode_scripts;?>');
$('.data.use_dbsession').val('<?php echo Config::getInstance()->system->use_dbsession; ?>'); 
$('.data.driver').val('<?php echo Config::getInstance()->database->driver?>'); 
$('#id-system-default').modHtml('tabs').modHtml('buttons'); 

//Init buttons 
//Handlers
//Database settings 
$('#id-database .action.update').modAjax('assign', {
	moduleName : 'developer',
	moduleAction : 'system/update_configuration',
	lang : '<?php echo Languages::getCurrentLanguage(); ?>',
	fetchPattern : [[null,'data[%]','section'],
	            	[null,'data[values][%]','host,user,database,password,table_prefix,driver']],
	onSuccess : function(){
		alert('<?php echo Languages::getJText('global.messages.success_updated');?>'); 
	}
}); 

//System settings
$('#id-system .action.update').modAjax('assign',{
	moduleName : 'developer',
	moduleAction : 'system/update_configuration',
	lang : '<?php echo Languages::getCurrentLanguage(); ?>',
	fetchPattern : [[null,'data[%]', 'section'],
	            	[null,'data[values][%]','use_dbsession,app_id,debug_mode,auto_create_tokens,encode_scripts,timezone']],
	onSuccess : function(){
		alert('<?php echo Languages::getJText('global.messages.success_updated');?>');
	}
});
//Security
$('#id-developer .action.update').modAjax('assign', {
	moduleName : 'developer',
	moduleAction : 'system/update_configuration',
	lang : '<?php echo Languages::getCurrentLanguage(); ?>',
	fetchPattern : [[null,'data[%]','section'],
	            	[null,'data[values][%]','password']],
	onSuccess : function(){
		alert('<?php echo Languages::getJText('global.messages.success_updated');?>');
		window.location.reload(); 	
	}
	
});
//Genearte GUID
$('#id-system-default .action.generate_guid').modAjax('assign',{
	moduleName : 'developer',
	moduleAction : 'system/generate_guid',
	lang : '<?php echo Languages::getCurrentLanguage();?>',
	onSuccess : function(result,self) {
		alert('<?php echo Languages::getJText('global.messages.success_updated');?>');
		$($(self).parents('.parent').get(0)).find('.data.app_id').val(result.app_id); 	
	}
}); 

</script>
