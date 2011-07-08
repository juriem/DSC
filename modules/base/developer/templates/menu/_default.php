<?php
/**
 * Default template
 */


function render($menus, $p_id, $padding) {
	
	foreach($menus as $menu) {
		if ($menu->p_id == $p_id) {
			?>
			<tr class="row parent">
				<td style="padding-left:<?php echo $padding;?>px;"><?php echo $menu->id; ?></td>
				<td><?php echo $menu->url_code; ?></td>
				<td align="center"><?php echo ($menu->is_group)?'Yes':'No'; ?></td>
				<td align="center"><?php echo ($menu->is_hidden)?'Yes':'No'; ?></td>
				<td align="center"><?php echo ($menu->is_locked)?'Yes':'No'; ?></td>
				<td align="center"><?php echo ($menu->is_extendable)?'Yes':'No'; ?></a></td>
				<td align="center"><?php echo ($menu->is_unlimited)?'Yes':'No'; ?></a></td>
				<td>
					<a class="action edit" href="__BASE_URL__/_developer/menu/edit/<?php echo $menu->id; ?>"><?php echo Languages::getText('global.actions.edit');?></a>
					<a class="action add_sub" href="__BASE_URL__/_developer/menu/add/<?php echo $menu->id?>"><?php echo Languages::getText('global.actions.add_sublevel');?></a>
					<a class="action delete" href="#">Delete</a>
				</td>
			</tr>
			<?php
			render($menus, $menu->id, $padding+10); 
		}
	}
	
}

?>


<div id="id-menu-default" class="parent">

	<h1>Site menus</h1>
	
	<table>
	<tr>
		<td class="actions" colspan="8" align="right">
			<a class="action add"href="__BASE_URL__/_developer/menu/add"><?php echo Languages::getText('menu.actions.add_top_level');?></a>
		</td>
	</tr>
	<tr class="header">
		<th>#</th>
		<th><?php echo Languages::getText('menu.columns.code');?></th>
		<th><?php echo Languages::getText('menu.columns.is_group', 'Group menu');?></th>
		<th><?php echo Languages::getText('menu.columns.is_hidden', 'Hidden menu');?></th>
		<th><?php echo Languages::getText('menu.columns.is_locked', 'Locked by system');?></th>
		<th><?php echo Languages::getText('menu.columns.is_extendable', 'Allow add sublevels');?></th>
		<th><?php echo Languages::getText('menu.columns.is_unlimited', 'Unlimited sublevels number');?></th>
		<th>Actions</th>
	</tr>
	<?php render($menus, null, 10); ?>
	
	</table>	
</div>


<script rel="inline">

</script>