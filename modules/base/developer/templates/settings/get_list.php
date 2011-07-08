<?php
/**
 * Settings list 
 */ 
?>

<?php foreach($settings as $setting): ?> 
<tr class="gui-table-row parent">
	<td><?php echo $setting->id; ?></td>
	<td><?php echo ($setting->module_id === null)?'_GLOBAL_':$setting->module_name; ?></td>
	<td><?php echo $setting->code; ?></td>
	<td><?php echo $setting->type; ?></td>
	
	<td>
		<a href="__BASE_URL__/_developer/settings/edit/<?php echo $setting->id; ?>"><?php echo Languages::getText('global.actions.edit');?></a>
		<a href="#" class="action delete">Delete</a>
		<input type="hidden" class="data id" value="<?php echo $setting->id; ?>">
	</td>
</tr><?php endforeach; ?>







