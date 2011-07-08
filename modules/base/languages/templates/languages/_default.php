<?php 
/**
 * Admin template for languages 
 */
?>
<?php if (isset($languages)):?>


<div id="id-default-template" class="section-container"> 
<input type="hidden" class="data id_name" value="item_id">
<input type="hidden" class="data module_name" value="<?php echo $this->_module->_moduleName; ?>">
<input type="hidden" class="data table_name" value="#language_values">
<input type="hidden" class="data columns" value="language_name:string:language_name">


<h1 class="section-title"><?php echo Languages::getText('languages.titles.list_of_languages', 'Языки системы', 'ru');?></h1>



<table align="center">
<tr class="actions-bar">
	<td colspan="6">
		<div class="buttons" style="float:right; clear:right;">
			<?php HTML::addAction('add', Languages::getText('languages.actions.add'), URL::build('languages/languages/add')); ?>
		</div>
	</td>
</tr>
<tr class="header">
	<th><?php echo Languages::getText('languages.columns.language_url_code'); ?></th>
	<th><?php echo Languages::getText('languages.columns.language_name', 'Название языка', 'ru');?></th>
	<th><?php echo Languages::getText('global.columns.use_by_default', 'По-умолчанию', 'ru'); ?></th>
	<th><?php echo Languages::getText('global.columns.is_admin_default', 'По-умолчанию (Admin)', 'ru'); ?></th>
	
	<th><?php echo Languages::getText('global.columns.is_enabled', 'Разрешен/Запрещен', 'ru');?></th>
	<th><?php echo Languages::getText('global.columns.actions', 'Действия', 'ru'); ?></th>
</tr>
<?php 
foreach($languages as $language) {
	?>
	<tr class="row parent">
		<td><strong><?php echo $language->url_code;?></strong></td>
		
		<td class="ui-value-holder" name="language_name"><?php echo $language->language_name; ?></td>
		
		<td>
			<div class="buttons">
				
				 <a class="action toggle-state" name="is_default" href="#"></a>
			</div>
		</td>
		
		<td>
			<div class="buttons">
				<a class="action toggle-state" name="is_admin_default" href="#"></a>
			</div>
		</td>
		
		
		<td>
			<?php if (!$language->is_default):?>
			<div class="buttons">
				<?php HTML::addChangeStateAction($language, 'is_enabled'); ?>
			</div>
			<?php endif;?>
		</td>
		
		<!-- Actions -->
		<td>
			<div class="buttons">
				<?php 
					HTML::addAction('names', Languages::getText('global.actions.names')); 
					HTML::addAction('edit', Languages::getText('global.actions.edit'), URL::build('languages/languages/edit/'.$language->id)); 
				?>
				<?php if (!$language->is_default): ?>
				<?php 
					HTML::addAction('delete', Languages::getText('global.actions.delete')); 
				?>
				<?php endif; ?>
			</div>
			
			
			<?php HTMLForm::addHiddenFields($language, 'id,is_default,is_admin_default');?>
		</td>
	</tr>
	<?php 
}
?>
</table>
<div id="id-form" title="<?php echo Languages::getText('languages.titles.edit_language_names', 'Названия языка'); ?>"></div>

</div>

<?php 
Scripts::addModuleLib('helper'); 
Scripts::addModuleLib('html');
Scripts::addModuleLib('languages'); 
?>


<script rel="inline">
var holder = $('#id-default-template');

$('#id-default-template').modHtml('buttons'); 

//Init change states 
$('#id-default-template a[data-action="change_state"]').modStates({
	tableName : '#languages',
	actions:{'is_enabled':['<?php echo Languages::getJText('global.actions.enable')?>','<?php echo Languages::getJText('global.actions.disable')?>']},
	classes:{'is_enabled':['disabled','enabled']}
}); 
//Init toggle states 
$('#id-default-template .action.toggle-state').modHelper('assign_state', {
	type : 'toggled',
	lang : '<?php echo Languages::getCurrentLanguage(); ?>',
	tableName : '#languages', 
	actions : '<?php echo Languages::getJText('global.actions.set_default')?>', 
	onComplete:function(){
		window.location.reload();
	}
});

$('#id-default-template tr.row').each(function(){
	var self = this; 
	$(this).find('[data-action="names"]').modLanguages({
		lang:'<?php echo Languages::getCurrentLanguage(); ?>', 
		moduleName:'languages',
		tableName:'#language_values',
		idHolderName:'id',
		columns:'language_name:string:language_name',
		valueHolder : $(self).find('td[name*="language_name"]'), 
		title:'<?php echo Languages::getJText('languages.titles.language_name')?>'
	}); 
}); 


</script>


<?php endif; ?>
