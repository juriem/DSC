<?php
/**
 * Cron jobs list
 */
?>

<div class="parent section" id="id-jobs-list">
	
	
	<div class="section-title"><?php echo Languages::getText('developer.titles.cronjobs_list');?></div>
	
	<table>
		<tr>
			<td colspan="5">
				<a data-action="add" href="<?php echo URL::build('cron/add')?>">Add new job</a>
			</td>
		</tr>
		
		<tr class="header">
			<th>Module name</th>
			<th>Interval (minutes)</th>
			<th>Enabled</th>
			<th>Last run</th>
			<th>Actions</th>
		</tr>
		
		<?php if ($jobs) {
		
			foreach($jobs as $_job) {
				?>
		<tr class="parent">
			<td><?php echo $_job->module;?></td>
			<td><?php echo $_job->interval;?></td>
			<td>
				<a href="#" data-action="change_state"></a>
			</td>
			<td>
				<?php echo date('d.m.Y H:i:s',$_job->last_run)?>
			</td>
			<td>
				<a data-action="edit" href="<?php echo URL::build('cron/edit/'.$_job->id); ?>">Edit</a>
				<a data-action="delete" href="#">Delete</a>
				
				<input type="hidden" data-field="id" value="<?php echo $_job->id?>">
				<input type="hidden" data-field="is_enabled" value="<?php echo $_job->is_enabled;?>">
			</td>
		</tr>		
				
				<?php 
			}
			
		}?>
		
	</table>

</div>


<script rel="inline">


$('[data-action="change_state"]').each(function(){

	var self = this;
	
	function updateState() {
		var parent = $(self).parents('.parent').get(0);
		var valueHolder = $(parent).find('[data-field="is_enabled"]');

		if ($(valueHolder).val() == 0) {
			$(self).html('Enable');
		} else {
			$(self).html('Disable'); 
		}
		
	}

	function changeState(){
		var parent = $(self).parents('.parent').get(0);
		var valueHolder = $(parent).find('[data-field="is_enabled"]');
		$(valueHolder).val(Math.abs($(valueHolder).val()-1));
	}
	
	updateState();

	$(this).modAjax({
		moduleName:'developer',
		moduleAction:'cron/change_state',
		fetchPattern:[[null,'data[%]']],
		onSuccess:function(){
			changeState();
			updateState();
			}
	});
	
}); 
 

</script>