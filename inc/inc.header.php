<!-- Meta -->
<meta charset="utf-8">
<meta name="robots" content="noindex"/>
<link rel="icon" type="image/ico" href="favicon.ico" />

<!-- CSS -->
<link rel="stylesheet" href="ressources/bootstrap/css/bootstrap.min.css" media="screen">
<link rel="stylesheet" href="ressources/style.css" type="text/css" media="screen">
<link rel="stylesheet" href="ressources/reset.css" type="text/css" media="screen">
<link href='http://fonts.googleapis.com/css?family=Changa+One|Droid+Sans:400,700' rel='stylesheet' type='text/css'>

<!-- JS -->
<script type="text/javascript" src="ressources/jquery.min.js"></script>
<script type="text/javascript" src="ressources/jquery-ui-1.10.1.custom.min.js"></script>
<script type="text/javascript" src="ressources/jquery.leanModal.js"></script>
<script src="ressources/bootstrap/js/bootstrap.min.js"></script>


<script>
$(document).ready( function(){ 
	// Chargement du background configuré
	$('body').css('background-image', 'url(ressources/backgrounds/<?php echo $config->background; ?>)');

	// Fenêtres modales
	$('#about_us').modal({show:false});
	$('#config_panel').modal({show:false});
	$('#howto_update').modal({show:false});

	// Panel de configuration, tabs
	$('#myTab a[href=#general]').tab('show');
	$('#myTab a').click(function (e) {
  		e.preventDefault();
  		$(this).tab('show');
  	});

	// Gère l'unroll d'un dossier jamais ouvert auparavant (requête GETs)
  	$('body').on('click', '.toRoll', function(e)
  	{
		var dir = $(this).data('path');
		var my_div = this;
		$.get('ajax.php?dir_content='+dir, function(data) 
		{
	  		$(my_div).next('.dirInList').hide().html(data).show('slide',{ direction: "up" },400);
	  		$(my_div).removeClass('toRoll');
	  		$(my_div).addClass('isRolledShown');
		});
  	});

  	// Cache un dossier cliqué déroulé
  	$('body').on('click', '.isRolledShown', function(e)
  	{
  		$(this).next('.dirInList').show('slide',{ direction: "up" },400);
  		$(this).removeClass('isRolledShown');
	  	$(this).addClass('isRolledHidden');
  	});

  	// Affiche un dossier cliqué caché
  	$('body').on('click', '.isRolledHidden', function(e)
  	{
  		$(this).next('.dirInList').hide('slide',{ direction: "up" },400);
  		$(this).addClass('isRolledShown');
	  	$(this).removeClass('isRolledHidden');
  	});
});

// Gère le choix des images de fond (modal config)
function set_radio($inputid) {
    $("#background_"+$inputid).prop("checked", true);
    $("a.radio-picture").removeClass('selected_radio');
	$("a#linkbackground_" + $inputid).addClass('selected_radio');
}
</script>
