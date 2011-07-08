<?php
/**
 * Template for values
 * Not use for AJAX
 */
 
?>

<div id="id-values-<?php echo $holder_id; ?>" class="tabs parent gui-dialog">
	<ul>
		<?php
		$_values = '';  
		foreach($languages as $language) {
			$_disabled = ''; 
			if (!$language->is_enabled) $_disabled = ' (*)'; 
			//Init empty values
			$_value = array();  
			foreach($columns as $column){
				$_value[$column->name] = ''; 
			}			
			if ($values) {
				foreach($values as $value){
					if ($value->language_id == $language->id) {
						$tmp = $value->getArray(); 
						foreach($tmp as $key=>$val){
							$_value[$key] = $val;	
						}
						break; 
					}
				}
			}
			?>
			<li><a href="#id-value-<?php echo $language->id;?>"><?php echo $language->language_name.$_disabled;?></a></li>
			<?php
			//Build interface for values  
			ob_start();
			?>
			<div class="value parent" id="id-value-<?php echo $language->id;?>">
				<table>
			<?php   
			foreach($columns as $column) {
				echo '<tr>'; 
				?>
				<td class="label"><?php echo Languages::getText($module_name.'.labels.'.$column->label); ?></td>
				
				<td>
					<?php if ($column->type == 'string'):?>
					
					<input data-field="<?php echo $column->name; ?>"  class="data <?php echo $column->name; ?>" type="text" size="50" value="<?php echo $_value[$column->name];?>">
					<?php else: ?>
					<textarea data-field="<?php echo $column->name; ?>" class="data <?php echo $column->name;?>" cols="50" rows="20"><?php echo $_value[$column->name];?></textarea>
					<?php endif;?>
				</td>
				<?php 
				echo '</tr>'; 
			}
			?>
				</table>
				<input data-field="language_id" class="data" type="hidden" value="<?php echo $language->id; ?>">
			</div>
			<?php 
			$_values .= ob_get_clean();
		}
		?>
	
	</ul>
	
	<?php echo $_values; ?>
</div>

<?php 
if ($use_editor) Scripts::addLib('ckeditor');
?>

<script rel="inline">
$('#id-values-<?php echo $holder_id;?>').tabs();
<?php if ($use_editor): ?>
$('#id-values-<?php echo $holder_id;?>').find('textarea').ckeditor(); 
<?php endif; ?>
</script>
