<?php
/**
 * Edit menu UI
 */


?>

<div class="parent" id="id-menu-edit">
	
	<h2><?php echo (isset($_newMode))?Languages::getText('global.titles.edit'):Languages::getText('global.titles.new'); ?></h2>
	
	<?php HTMLForm::addHiddenFields($data, 'id,p_id,is_group,is_hidden,is_locked,is_disabled,is_extendable,is_unlimited,sort_index'); ?>
	
	
	<table>
		<tr>
			<td class="label"><?php echo Languages::getText('menu.labels.url_code', 'URL code')?>:</td>
			<td><input class="data url_code" type="text" value="<?php echo $data->url_code; ?>"></td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('menu.labels.is_group','Group menu')?>:</td>
			<td><input type="checkbox" name="is_group"></td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('menu.labels.is_hidden', 'Hidden menu');?>:</td>
			<td><input type="checkbox" name="is_hidden"></td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('menu.labels.is_locked', 'Locked by system');?>:</td>
			<td><input type="checkbox" name="is_locked"></td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('menu.labels.is_disabled', 'Disabled');?>:</td>
			<td><input type="checkbox" name="is_disabled"></td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('menu.labels.is_extendable', 'Extendable');?>:</td>
			<td><input type="checkbox" name="is_extendable"></td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('menu.labels.is_unlimited', 'Unlimited');?>:</td>
			<td><input type="checkbox" name="is_unlimited"></td>
		</tr>
		
		<tr>
			<td class="label"><?php echo Languages::getText('menu.labels.menu_title', 'Title')?>:</td>
			<td id="id-languages-module">
				<?php App::executeModule('languages', 'values/_default/menu/#menu_values/title:string:title/'.$data->id); ?>
			</td>
		</tr>
		
		<!-- Access levels  -->
		<tr>
			<td class="label">Access level</td>
			<td>
				<select class="data access_level">
					<option value="all">All</option>
					<option value="user">Users</option>
					<option value="administrator">Administrators</option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td class="label">Access level checking</td>
			<td>
				<select class="data access_check_type">
					<option value="default">Default</option>
					<option value="exact">Exact</option>
				</select>
			</td>
		</tr>
		
		
		<!-- List of modules -->
		
		
		<!-- Actions -->
		<tr>
			<td colspan="2" align="right">
				<div class="buttons position-right">
					<a class="action update" href="#">Update</a>
				</div>
			</td>
		</tr>
		
	</table>
</div>


<script rel="inline">
var holder = $('#id-menu-edit'); 
$(holder).modHtml('checkboxes');  
$(holder).find('.action').button();

$(holder).find('.data.access_level').val('<?php echo $data->access_level; ?>');
$(holder).find('.data.access_check_type').val('<?php echo $data->access_check_type?>'); 

$(holder).find('.action.update').modAjax('assign',{
	moduleName : 'developer',
	moduleAction : 'menu/update',
	lang : '<?php echo Languages::getCurrentLanguage(); ?>', 
	fetchPattern : [[null,'data[%]', 'id,url_code,p_id,is_group,is_locked,is_hidden,is_disabled,is_extendable,is_unlimited,sort_index,access_level,access_check_type'],
	            	[$('#id-languages-module .value'),'data[values][%i][%]','language_id,title']],
	onSuccess : function(result,sender) {
		window.location.href = "__BASE_URL__/_developer/menu"; 
	}
});

</script>
