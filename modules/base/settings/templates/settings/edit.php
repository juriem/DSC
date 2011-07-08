<?php
/**
 * AJAX interface for editing settings 
 */
?>

<div class="gui-dialog parent">
	<input data-field="id" class="data id" type="hidden" value="<?php echo $setting->id; ?>">
	<input data-field="type" class="data type" type="hidden" value="<?php echo $setting->type; ?>">
	<input class="data setting_name" type="hidden" value="<?php echo $setting->setting_name.' ('.$setting->code.')'; ?>">

	<table>
		<tr>
			<td class="label"><?php echo Languages::getText('settings.labels.value');?></td>
			<td>
				<?php
				if (in_array($setting->type, array('text','string'))) {
					App::executeModule('languages', 'values/_default/settings/#setting_values/value:'.$setting->type.':value/'.$setting->id);
				}  else {
					switch ($setting->type) {
						case 'yesno':
							?>
							<select data-field="value" class="data value">
								<option value="yes"><?php echo Languages::getText('global.values.yes');?></option>
								<option value="no"><?php echo Languages::getText('global.values.no');?></option>
							</select>
							<?php 
							break; 
						case 'textvalue':
							?>
							<textarea data-field="value" class="data value" cols="50" rows="10"><?php echo $setting->single_value; ?></textarea>
							<?php 
							break;
						default:
							?>
							<input data-field="value" class="data value" type="text" size="50" value="<?php echo $setting->single_value;?>">
							<?php 
					}
				}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="buttons" style="float:right; clear:right;">
					<a data-action="update" class="action" href="#"><?php echo Languages::getText('global.actions.update');?></a>
					<a data-action="cancel" class="action" href="#"><?php echo Languages::getText('global.actions.cancel');?></a>
				</div>
			</td>
		</tr>
	</table>
</div>

