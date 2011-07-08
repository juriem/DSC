<?php
/**
 * Tokens list 
 */
 
?>



<div id="id-tokens" class="section-container gui-cestion parent" >


<h1 class="gui-section"><?php echo Languages::getText('languages.titles.tokens', 'Токены'); ?></h1>




<table align="center">
	<tr class="header parent">
		<th>
			<?php echo Languages::getText('languages.columns.token_code', 'Системный код'); ?>&nbsp;
			<select data-field="module_id" class="filter">
				<option value=""><?php echo Languages::getText('global.select_values.all');?></option>
				<?php 
				if ($modules) {
					foreach($modules as $module) {
						$_module_name = $module->module_name; 
						$_module_id = $module->module_id; 
						if ($module->module_id == null) {
							$_module_name = 'global';
							$_module_id = 0; 
						} 	
						echo '<option value="'.$_module_id.'">'.$_module_name.'</option>'; 
					}
				}
				?>
			</select>
		</th>
		<th width="400"><?php echo Languages::getText('languages.columns.token_value', 'Значение'); ?></th>
		<th><?php echo Languages::getText('global.columns.actions', 'Действия'); ?></th>
	</tr>
	<!-- Place for list -->
</table>
</div>

<div id="id-form"></div>


<?php 
Scripts::addModuleLib('html'); 
Scripts::addModuleLib('languages');
?>

<script rel="inline">
//Handler for change filter 
$('#id-tokens .filter[data-field="module_id"]').change(function(){
	$('#id-tokens tr.row').remove();
	var header = $('#id-tokens tr.header'); 
	$('#id-tokens tr.header').modAjax('html', {
		moduleName : 'languages',
		moduleAction : 'tokens/load_list',
		lang : '<?php echo Languages::getCurrentLanguage(); ?>',
		fetchPattern : [ [header, 'filters[%]', 'filter']],
		insertAfter : true, 
		onComplete : function(container) {
			
			$('#id-tokens').modHtml('buttons'); 
			$('#id-tokens tr.row').each(function(){
				var self = this; 
				$(this).find('.action[data-action="edit"]').modLanguages({
					lang:'<?php echo Languages::getCurrentLanguage(); ?>', 
					moduleName:'languages',
					tableName:'#token_values',
					columns:'value:text:value',
					valueHolder : $(self).find('td[name*="value"]'),
					title:$(self).find('td.ui-code').html(),
					idHolderName:'id'
				});
			}); 	
		}
	});
}).change();

 
</script>