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
<script type="text/javascript" src="ressources/js/jquery.min.js"></script>
<script type="text/javascript" src="ressources/js/jquery-ui-1.10.1.custom.min.js"></script>
<script type="text/javascript" src="ressources/js/jquery.bootstrap.modal.js"></script>
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
			// Injecte les données
	  		$(my_div).next('.dirInList').html(data);
			$(my_div).next('.dirInList').hide();

	  		// Simule un dossier fermé
			$(my_div).removeClass('toRoll');
		  	$(my_div).addClass('isRolledHidden');

		  	// Actionne l'ouverture (trigger ci-dessous)
		  	$(my_div).click();
		});
  	});

  	// Cache un dossier cliqué déroulé
  	$('body').on('click', '.isRolledShown', function(e)
  	{
  		$(this).next('.dirInList').hide("slide", { direction: "up"});
  		$(this).parents().css('height','auto');
		$(this).removeClass('isRolledShown');
	  	$(this).addClass('isRolledHidden');
  	});

  	// Affiche un dossier cliqué caché
  	$('body').on('click', '.isRolledHidden', function(e)
  	{
  		$(this).next('.dirInList').show("slide", { direction: "up"});
  		$(this).addClass('isRolledShown');
	  	$(this).removeClass('isRolledHidden');
  	});

  	// Afficher les actions sur les dir et les files
  	$('.onefile, .onedir').hover(function () 
  	{
  	// Afficher les actions on hover
    $(this).children('.actions_list').html('<img src="ressources/mini_delete.png" alt="Supprimer" class="delete_file_button"/> \
    	<img src="ressources/mini_edit.png" alt="Editer" /> \
    	<img src="ressources/mini_info.png" />');
  },

  	// Cacher les options out hover
  	function () 
  	{
    	$(this).children('.actions_list').html('');
  	});

  	// On click sur le bouton de supression
  	$('body').on('click', 'img.delete_file_button', function(e)
  	{
  		var content = $(this).parent().parent().data('path');
  		$("#confirmDiv").confirmModal({heading: 'Confirmer la suppression',
  										body: 'Êtes-vous sûr de vouloir supprimer <strong>'+content+'</strong> ?',
		callback: function () {
			// Si "oui"
		}
	});
  	});



});

// Gère le choix des images de fond (modal config)
function set_radio($inputid) {
    $("#background_"+$inputid).prop("checked", true);
    $("a.radio-picture").removeClass('selected_radio');
	$("a#linkbackground_" + $inputid).addClass('selected_radio');
}
</script>
