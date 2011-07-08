<?php
/**
 * Template for developer 
 */
?>

<style rel="inline">
#id-menubar .menu-item {
	float:left; 
}
</style>

<h1 class="gui-title">Developer module</h1>

<?php if (SessionHandler::__()->dev_loggedin !== null): ?>
<!-- Menus -->
<div id="id-menubar">
	<div class="menu-item">
		<a href="<?php echo URL::build('system');?>">System configuration</a>
	</div>
	
	<div class="menu-item">
		<a href="<?php echo URL::build('settings');?>">System settings</a>
	</div>

	<div class="menu-item">
		<a href="<?php echo URL::build('libs')?>">Script libraries</a>
	</div>
	
	<div class="menu-item">
		<a href="<?php URL::build('adminmenu');?>">Admin menus</a>
	</div>
	
	<div class="menu-item">
		<a href="<?php echo URL::build('modules')?>">Modules</a>
	</div>
	
	<div class="menu-item">
		<a href="<?php echo URL::build('menu');?>">Menus</a>
	</div>
	
	<div class="menu-item">
		<a href="<?php echo URL::build('languages');?>">Languages</a>
	</div>
	
	<div class="menu-item">
		<a href="<?php echo URL::build('tokens');?>">Tokens</a>
	</div>
	
	<div class="menu-item">
		<a href="<?php echo URL::build('cron')?>">Cron jobs</a>
	</div>
	<div class="menu-item">
		<a data-action="logout" href="#">Close</a>
	</div>
	<div class="clr"></div>
</div>

<script rel="inline">
$('[data-action="logout"]').modAjax({
	moduleName:'developer',
	moduleAction:'login/logout',
	lang:'<?php echo Languages::getCurrentLanguage();?>',
	onSuccess:function(){ 
		window.location.href='<?php echo URL::build(''); ?>'; 
	}
}); 

$('#id-menubar a').button();
</script>
<?php endif; ?>
<!-- Content -->
<?php 
if (isset($content)) echo $content; 
?>
