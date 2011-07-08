<?php
/**
 * Template for script libs
 */
?>



<div id="id-libs" class="gui-section parent">
	<h1 class="gui-section-title"><?php echo Languages::getText('developer.title.script_libs'); ?></h1>
	
	
	<table>
	<tr class="actions">
		<td colspan="2" align="right">
			<div class="buttons" style="float:right; clear: right;">
				<a class="action add" href="/_developer/libs/add" ><?php echo Languages::getText('global.action.add'); ?></a>
			</div>
		</td>
	</tr>
	
	<tr class="header">
		<td><?php echo Languages::getText('global.columns.code')?></td>
		<td><?php echo Languages::getText('global.columns.actions'); ?></td>
	</tr>	
<?php 
foreach ($libs as $lib) {
?>
	<tr class="row parent">
		<td><?php echo $lib->code;?></td>
		<td>
			<div class="buttons">
				<a href="/_developer/libs/edit/<?php echo $lib->id; ?>" class="action edit"><?php echo Languages::getText('global.actions.edit'); ?></a>
				<a href="#" class="action delete"><?php echo Languages::getText('global.actions.delete'); ?></a>
			</div>
			<input type="hidden" class="data id" value="<?php echo $lib->id; ?>">
		</td>
	</tr>
<?php 
}
?>
	</table>	
</div>

<script rel="inline">

$('#id-libs .action.delete').modAjax('assign', {
	moduleName : 'developer',
	moduleAction : 'libs/delete',
	lang : '<?php echo Languages::getCurrentLanguage(); ?>',
	fetchPattern : [[null,'id','id']],
	onBeforeAction : function(){
		if (!confirm('<?php echo Languages::getText('developer.confirms.delete_script_lib'); ?>')) return false; 
		return true; 
	},
	onSuccess : function(result, self) {
		$($(self).parents('.parent').get(0)).remove(); 
	}
});
</script>