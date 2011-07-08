<?php
/**
 * Languages list 
 */ 
?>


<div id="id-languages-default" class="gui-section parent">
	
	<h1 class="gui-section-title"><?php echo Languages::getText('languages.title.list_of_languages'); ?></h1>
	
	<table>
	<tr>
		<td colspan="3">
			<div class="buttons" style="float:right; clear:right;">
				<a class="action add" href="__BASE_URL__/_developer/languages/add"><?php echo Languages::getText('languages.actions.add_new_language'); ?></a>
			</div>
		</td>
	</tr>
	<tr>
		<th>#</th>
		<th><?php echo Languages::getText('languages.columns.url_code'); ?></th>
		<th><?php echo Languages::getText('global.columns.actions'); ?></th>
	</tr>	
	<?php 
	foreach($languages as $language) {
	?>	
	<tr class="row parent">
		<td><?php echo $language->id; ?></td>
		<td><?php echo $language->url_code; ?></td>
		<td>
			<div class="buttons">
				<a class="action edit" href="__BASE_URL__/_developer/languages/edit/<?php echo $language->id; ?>">Edit</a>
				<a class="action delete" href="#">Delete</a>
			</div>
		</td>
	</tr>
	<?php 	
	}
	?>
	</table>
</div>

