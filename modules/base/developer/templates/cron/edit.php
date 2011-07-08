<?php
/**
 * Edit or Add cron job 
 */
?>


<div class="parent form" id="id-cronjob-data">
	
	<input type="hidden" data-field="id" value="<?php echo $data->id?>">
	
	<div class="section-title">
		<?php echo (isset($this->_newMode))?'Add new job':'Edit job'?>
	</div>
	
	<table>
		<tr>
			<td class="label">Module:</td>
			<td>
				<input data-field="module" type="text" value="<?php echo $data->module;?>">
			</td>
		</tr>
		
		<tr>
			<td class="label">Interval (minutes):</td>
			<td>
				<input data-field="interval" type="text" value="<?php echo $data->interval;?>">
			</td>
		</tr>
		
		<tr>
			<td class="label">Description (optional):</td>
			<td>
				<textarea data-field="description"><?php echo $data->description;?></textarea>
			</td>
		</tr>
		
		<tr>
			<td class="label">Enabled:</td>
			<td>
				<input type="checkbox" data-field="is_enabled" data-value="<?php echo $data->is_enabled;?>">
			</td>
		</tr>
		
		<tr>
			<td class="label">Last run:</td>
			<td>
				<?php 
				if ((int)$data->last_run == 0) {
					echo 'Never';
 				} else {
 					echo date('d.m.Y H:i:s', $data->last_run); 
 				}
				?>
			</td>
		</tr>
		
		<tr>
			<td colspan="2">
				<a data-action="update" href="#">Update</a>
				<a data-action="cancel" href="<?php echo URL::build('cron')?>">Cancel</a>
			</td>
		</tr>
	</table>
</div>


<script rel="inline">
$('#id-cronjob-data').modHtml('checkboxes'); 
$('#id-cronjob-data [data-action="update"]').modAjax({
	moduleName:'developer',
	moduleAction:'cron/update',
	lang:'<?php echo Languages::getCurrentLanguage();?>',
	fetchPattern:[[null,'data[%]']],
	onSuccess:function(){
		window.location.href='<?php echo URL::build('cron')?>'
	}
}); 
</script>
