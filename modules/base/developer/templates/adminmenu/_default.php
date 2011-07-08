<?php
/**
 * 
 */

$base_url = '/_'.$this->_module->_moduleName.'/'.$this->_controllerName;

function render($p_id, $level, $menus, $base_url) {
	$index = 0;
	//Counting records
	$cnt = 0;  
	foreach($menus as $menu) {
		if ($menu->p_id == $p_id) $cnt++; 
	}
	
	foreach($menus as $menu) {
		$padding=''; 
		if ($menu->p_id == $p_id) {
			$padding = ' style="padding-left:'.($level*10).'px;"';
			 
		?>
		<tr class="row parent">
			<td<?php echo $padding; ?>><?php echo $menu->id; ?></td>
			<td><?php echo $menu->url_code; ?></td>
			<td><?php echo $menu->module_name; ?></td>
			<td><?php echo $menu->default_controller; ?></td>
			<td><?php echo $menu->default_task; ?></td>
			<td>
				<div class="buttons">
				<a class="action edit" href="__BASE_URL__/_developer/adminmenu/edit/<?php echo $menu->id;?>"><?php echo Languages::getText('global.actions.edit'); ?></a>
				<a class="action delete" href="#"><?php echo Languages::getText('global.actions.delete'); ?></a>
				<a class="action add-sub" href="__BASE_URL__/_developer/adminmenu/add/<?php echo $menu->id; ?>"><?php echo Languages::getText('global.actions.add_sub'); ?></a>
				<?php if (($index+1) < $cnt && $cnt > 0):?>
				<a class="action sort_index-down" href="#">Down</a>
				<?php endif; ?>
				<?php if ($index > 0 && $cnt > 0 ):?>
				<a class="action sort_index-up" href="#">Up</a>
				<?php endif; ?>
				</div>
				
				<input type="hidden" class="data id" value="<?php echo $menu->id;?>">
				<input type="hidden" class="data group_name" value="p_id">
				<input type="hidden" class="data group_id" value="<?php echo $menu->p_id;?>">
			</td>
		</tr>
		<?php
			render($menu->id, $level+1, $menus, $base_url);
			$index++;
		}		
	}
}

?>




<div id="id-adminmenu-list" class="gui-section parent">

<h1 class="gui-section-title"><?php echo Languages::getText('developer.title.admin_mennus'); ?></h1>

	<table>
		<tr class="actions">
			<td colspan="4">
				<div class="buttons">
					<a class="action add" href="__BASE_URL__/_developer/adminmenu/add"><?php echo Languages::getText('global.actions.add'); ?></a>
				</div>
			</td>
				
		</tr>
		<tr class="header">
			<th>#</th>
			<th><?php echo Languages::getText('menu.columns.url_code'); ?></th>
			<th><?php echo Languages::getText('menu.columns.module','Module','en'); ?></th>
			<th><?php echo Languages::getText('menu.columns.controller', 'Controller', 'en'); ?></th>
			<th><?php echo Languages::getText('menu.columns.task', 'Task', 'en'); ?></th>
			<th><?php echo Languages::getText('global.columns.actions'); ?></th>
		</tr>
		
		<?php 
		render(null, 0, $menus, $base_url); 
		?>
	</table>
</div>

<?php 
Scripts::addModuleLib('helper');
?>

<script rel="inline">
var holder = $('#id-adminmenu-list'); 

$(holder).find('tr.row .action.delete').modAjax('assign', {
	moduleName : 'developer',
	moduleAction : 'adminmenu/delete',
	lang : '<?php echo Languages::getCurrentLanguage(); ?>', 
	fetchPattern : [[null,'data[%]','id']],
	onBeforeAction : function(){
		if (!confirm('<?php echo Languages::getJText('global.confirms.delete_data')?>')) return false;
		return true; 
	}, 
	onSuccess : function(result,self){
		var p = $(self).parents('.parent').get(0);
		$(p).remove(); 
	}
}); 
//Sort index 
$('#id-adminmenu-list .action.sort_index-down').modHelper('change_sortindex', {
	tableName : '#menus',
	lang : '<?php echo Languages::getCurrentLanguage();?>',
	offset : 1,
	adWhere : 'is_admin = 1', 
	onComplete : function(){
		window.location.reload(); 
	}
}); 
$('#id-adminmenu-list .action.sort_index-up').modHelper('change_sortindex', {
	tableName : '#menus',
	lang : '<?php echo Languages::getCurrentLanguage();?>',
	offset : -1,
	adWhere : 'is_admin = 1',
	onComplete : function(){
		window.location.reload(); 
	}
});

</script>