<?php
/**
 * List of modules 
 */
?> 
<div id="id-modules" class="parent gui-section">
	
	<h2 class="gui-section-title">List of modules</h2>
	
	<table>
		<tr class="actions">
			<td colspan="4">
				<div class="buttons" style="float:right;">
					<a href="__BASE_URL__/_developer/modules/add" class="action add"><?php echo Languages::getText('global.actions.add');?></a>
				</div>
			</td>
		</tr>
		<tr class="header">
			<th class="gui-code">Code</th>
			<th><?php echo Languages::getText('modules.columns.title', '', 'ru'); ?></th>
			<th><?php echo Languages::getText('modules.columns.system_state', '', 'ru'); ?></th>
			<th><?php echo Languages::getText('global.columns.actions'); ?></th>		
		</tr>
	<?php 
	foreach($modules as $module) {
	?>
	<tr class="row parent">
		<td class="gui-code"><?php echo $module->module_name;?></td>
		<td><?php echo $module->title; ?></td>
		<td><?php echo ($module->_isBroken)?'Broken':'Ok'; ?></td>
		<td>
			<a href="__BASE_URL__/_developer/modules/edit/<?php echo $module->id?>" class="action edit"><?php echo Languages::getText('global.actions.edit');?></a>
			<a href="#" class="action uninstall"><?php echo Languages::getText('modules.actions.uninstall','Uninstall','en');?></a>
			
			<input type="hidden" class="data id" value="<?php echo $module->id;?>">
		</td>
	</tr>
	<?php 	
	}
	?>
	</table>
</div>

<script rel="inline">
$('#id-modules .action.uninstall').modAjax('assign', {
	moduleName : 'developer',
	moduleAction : 'modules/delete',
	lang : '<?php echo Languages::getCurrentLanguage();?>',
	fetchPattern : [[null,'id','id']],
	onBeforeAction : function(){
		if (!confirm('<?php echo Languages::getJText('modules.confirms.delete_module')?>')) return false;
		return true; 
	},
	onSuccess : function(result,self) {
		$($(self).parents('.parent').get(0)).remove(); 
	}
});
</script>