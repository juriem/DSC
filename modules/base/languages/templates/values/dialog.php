<?php
/**
 * AJAX form 
 * Edit values for several languages 
 */
?>

<div id="id-edit-values" class="parent">

	<table>
	<tr>
		<td>
	<!-- Languages -->
		<div class="tabs gui-tabs">
		<ul>
			<?php 
			foreach($languages as $language) {
				$disabled = ''; 
				if (!$language->is_enabled) $disabled=' (!)';  
				echo '<li><a href="#value-'.$language->id.'">'.$language->language_name.$disabled.'</a></li>';
			}
			?>
		</ul>
	
		<!-- Load values -->
		<?php
		foreach($languages as $language) {
			$_values = array(); 
			foreach($columns as $column) {
				$_values[$column->column_name] = '';
			}
			if ($values) {
				foreach($values as $value) {
					if ($value->language_id == $language->id) {
						foreach($columns as $column) {
							$key = $column->column_name; 
							$_values[$key] = $value->$key;
						}
						break; 
					}
				}
			}
			?>
			<div class="language-value parent" id="value-<?php echo $language->id; ?>">
				<table>
					<?php 
					foreach($columns as $column) {
						$key = $column->column_name; 
						$labelCode = $module_name.'.labels.'.$column->label_code;
						?>
						<tr>
							<td class="label"><?php echo Languages::getText($labelCode); ?></td>
							<td>
								<?php 
								if ($column->value_type == 'string') {
									//Create input box
									echo '<input class="data" data-field="'.$column->column_name.'" type="text" size="50" value="'.$_values[$key].'">'; 
								} else {
									//Create textarea	
									echo '<textarea class="data" data-field="'.$column->column_name.'" cols="50" rows="10">'.$_values[$key].'</textarea>'; 
								}
								?>
							</td>
						</tr>
						<?php 
					}
					?>
				</table>
				<input type="hidden" data-field="language_id" class="data language_id" value="<?php echo $language->id; ?>">
			</div>
			<?php
		}
		?>
		</div>
	</td>
	</tr>
	<tr>
		<td align="right">
		<!-- Actions -->
		<div class="buttons" style="float:right; clear:right;">
			<a data-action="update" href="#" class="action update"><?php echo Languages::getText('global.actions.update'); ?></a>
			<a data-action="cancel" href="#" class="action cancel"><?php echo Languages::getText('global.actions.cancel');?></a>
		</div>
		</td>
	</tr>
	</table>
</div>