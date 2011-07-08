<?php
/**
 * Template for yesno values 
 */
?><div id="id-setting-value">
	<?php

	if (in_array($type, array('text','string'))) {
		App::executeModule('languages', 'values/_default/settings/#setting_values/value:'.$type.':value/'.$id); 
	} else {
		switch($type) {
			case 'yesno':
				?>
				<select class="data single_value">
					<option value="yes"><?php echo Languages::getText('global.select_values.yes'); ?></option>
					<option value="no"><?php echo Languages::getText('global.select_values.no'); ?></option>
				</select>
				<?php 
				break;
			case 'textvalue':
				?>
				<textarea class="data single_value" cols="50" rows="20"></textarea>
				<?php
				break;
			default:
				?>
				<input class="data single_value" type="text" size="30" value="">
				<?php 
		}
	}
	?>
	</div>
</div>