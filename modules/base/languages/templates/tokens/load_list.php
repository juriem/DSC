<?php
/**
 * Tokens 
 */

if ($tokens) {
	foreach($tokens as $token) {
	?>
	<tr class="row parent">
		<td class="ui-code"><?php 
		echo (($token->module_name == null)?'global':$token->module_name).'.'.$token->group_name.'.'.$token->value_name; ?></td>
		<td class="ui-value-holder" name="value"><?php echo $token->value; ?></td>
		<td>
			<div class="buttons">
				<a data-action="edit" class="action" href="#"><?php echo Languages::getText('global.actions.edit', 'Изменить'); ?></a>
			</div>
			<?php HTMLForm::addHiddenFields($token, 'id'); ?>
		</td>
	</tr>	
	<?php 	
	}
}
?>


