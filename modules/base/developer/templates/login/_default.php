<?php
/*
 * Login into developer area
 */
?>

<style rel="inline">
#id-login-box {
	width:300px; 
	margin-top: 100px; 
	margin-left:auto; 
	margin-right: auto; 
}
</style>

<div id="id-login-box" class="parent ui-parent">
	<h1><?php echo Languages::getText('developer.titles.login_into_developerarea')?></h1>
	<table>
		<tr>
			<td class="label"><?php echo Languages::getText('users.labels.password_label', 'Пароль'); ?></td>
			<td><input data-field="password" type="password" value=""></td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="buttons" style="float:right; clear:right;">
					<a data-action="login" href="#"><?php echo Languages::getText('users.actions.login', 'Логин');?></a>
				</div>
			</td>
		</tr>
	</table>
</div>

<script rel="inline">
//Bind enter key
$('#id-login-box input[data-field="password"]').keyup(function(e){
	if (e.keyCode == 13) {
		$('#id-login-box [data-action="login"]').click();
	} 
}); 
//Assign AJAX module 
$('#id-login-box [data-action="login"]').modAjax('assign', {
	moduleName : 'developer',
	moduleAction : 'login/login',
	lang : '<?php echo Languages::getCurrentLanguage(); ?>',
	fetchPattern : [[null,'data[%]']],
	onSuccess : function() { window.location.reload(); }
	
});
</script>

