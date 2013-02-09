<meta charset="utf-8">
<meta name="robots" content="noindex"/>
<script type="text/javascript" src="ressources/jquery.min.js"></script>
<link rel="stylesheet" href="ressources/style.css" type="text/css" media="screen">
<link rel="stylesheet" href="ressources/reset.css" type="text/css" media="screen">
<link href='http://fonts.googleapis.com/css?family=Changa+One|Droid+Sans:400,700' rel='stylesheet' type='text/css'>
<link rel="icon" type="image/ico" href="favicon.ico" />
<script type="text/javascript" src="ressources/jquery.leanModal.js"></script>
<script type="text/javascript" src="ressources/jquery.sizzle.js"></script>

<script>
$(document).ready( function(){ 
	// Chargement du background configuré
	$('body').css('background-image', 'url(ressources/backgrounds/<?php echo $config->get('background'); ?>)');
	// Fenêtres modales
	$("#link_config_panel").leanModal({closeButton: ".modal_close"});
	$("#link_howto_update").leanModal({closeButton: ".modal_close"});
	$("#link_about_us").leanModal({closeButton: ".modal_close"});
});
</script>
