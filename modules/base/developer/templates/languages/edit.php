<?php
/**
 * Template for edit languages
 */
?>

<div id="id-languages-edit" class="gui-section parent">

<input class="data id" type="hidden" value="<?php echo $data->id; ?>">

<h1 class="gui-section-title">
<?php echo (isset($_newMode))?Languages::getText('languages.titles.new_language'):Languages::getText('languages.titles.edit_language');?>
</h1>





<table>
	
	<tr>
		<td class="label"><?php echo Languages::getText('languages.labels.url_code');?>:*</td>
		<td><input class="data url_code require" type="text" size="3" value="<?php echo $data->url_code; ?>"></td>
	</tr>
	
	
	<tr>
		<td class="label"><?php echo Languages::getText('languages.labels.language_name');?>:*</td>
		<td><?php App::executeModule('languages', 'value/_default/languages/#language_values/language_name:string:language_name/'.$data->id);?></td>
	</tr>
	
	
	<tr>
		<td colspan="2">
			<div class="buttons" style="float: right; clear: right;">
				<a class="action update" href="#" ><?php echo Languages::getText('global.actions.update');?></a>
			</div>
		</td>
	</tr>

</table>
</div>