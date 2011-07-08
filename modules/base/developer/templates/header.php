<?php
/**
 * Template for header of page 
 */
Scripts::addModuleLib('ajax'); 
Scripts::addModuleLib('scripts.jquery-ui'); 
Scripts::addModuleLib('html'); 
Scripts::addModuleLib('helper');

echo '<!DOCTYPE html>'; //PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'; 
?>

<html>
<head>
	<title>Developer access</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="description" content=""> 
	<meta name="keywords" content=""> 
	<meta name="author" content="Gizlab">
	
	<link rel="stylesheet" type="text/css" href="__BASE_URL__/files/developer/css/default.css">

	
</head>
<body>
<!-- Main wrapper -->
<div id="id-wrapper">

<script rel="inline">
$.modScripts({lang:'<?php echo Languages::getCurrentLanguage();?>'})
</script>