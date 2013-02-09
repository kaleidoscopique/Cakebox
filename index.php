<?php

require_once('inc/lang.inc.php');
require_once('inc/functions.inc.php');

// Request : DELETE FILES
if(isset($_POST['delete']))
{
	foreach($_POST['Files'] as $file)
	{
		if(is_dir(LOCAL_DL_PATH.'/'.$file)) @rrmdir(LOCAL_DL_PATH.'/'.$file);
		else @unlink($file);
	}
}

// Request : MOVE FILES
if(isset($_POST['move']))
{
	foreach($_POST['Files'] as $file)
	{
		// On rajoute "/downloads" devant le nom des dossiers
		if(is_dir(LOCAL_DL_PATH.'/'.$file)) $file = LOCAL_DL_PATH.'/'.$file;
		@rename($file,$_POST['moveSelect']."/".basename($file));
	}
}

// Request : CREATE DIR
if(isset($_POST['mkdir']) && !empty($_POST['mkdir_name']))
	mkdir($_POST['mkdirSelect']."/".$_POST['mkdir_name'],0777);

// Request : DO UPDATE
if(isset($_GET['do_update']))
	$update->execute($force,isset($_GET['force_update']));

// Request : IGNORE UPDATE
if(isset($_GET['ignore_update'])) $update->ignore();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="robots" content="noindex"/>
    <title>CakeBox - <?php echo $lang[$config->get('lang')]['index_title']; ?></title>
    <meta charset="utf-8">

    <script type="text/javascript" src="ressources/jquery.min.js"></script>
    <link rel="stylesheet" href="ressources/style.css" type="text/css" media="screen">
    <link rel="stylesheet" href="ressources/reset.css" type="text/css" media="screen">
    <link href='http://fonts.googleapis.com/css?family=Changa+One|Droid+Sans' rel='stylesheet' type='text/css'>
    <link rel="icon" type="image/ico" href="favicon.ico" />
    <script type="text/javascript" src="ressources/jquery.leanModal.min.js"></script>

    <script>
    $(function() {
    	// Chargement du background configuré
    	$('body').css('background-image', 'url(ressources/backgrounds/<?php echo $config->get('background'); ?>)');
    	$("#link_config_panel").leanModal({closeButton: ".modal_close"});
    	$("#link_howto_update").leanModal({closeButton: ".modal_close"});
    });
    </script>
</head>
<body>
        <!-- HEADER -->
        <header>
		    <div id="logo">
				<a href="index.php">
					<span class="first">Cake</span>
					<span class="second">Box</span>
				</a>
		    </div>
		    <div id="menu">
		    	<a id="link_config_panel" rel="leanModal" href="#config_panel">Options</a>
		    </div>
        </header>
        <!-- / HEADER -->

        <?php
	        // Verifie les MàJ (+ affichage)
	       if($update->is_update_available()):
	    ?>

			<div id="update">
				<h3><?php echo $lang[$config->get('lang')]['new_version']." : v".$update->get_current_version()." !" ?></h3>
				<ol>
				    <?php foreach($update->get_changelog() as $change) echo "<li>$change;</li>"; ?>
				</ol>


				<div id="button_zone">
		            <div class="button">
		                <a id="link_howto_update" rel="leanModal" href="#howto_update">
		                    <img src="ressources/clouddownload.png" class="download_update_img"/><br />
		                    <?php echo $lang[$config->get('lang')]['click_here_update']; ?>
		                </a>
		            </div>
		            <span class="under_button">
		            	<a href="index.php?ignore_update&number=<?php echo $update->get_current_version(); ?>">
		                	<?php echo $lang[$config->get('lang')]['ignore_update']; ?>
		                </a>
            		</span>
		        </div>
		        <hr class="justclear" />
			</div>

		<?php endif; ?>
		<?php if(isset($_GET['update_done'])): ?>

      	<?php endif; ?>

        <!-- CONTENT -->
        <section id="content">
			<?php
				// TODO : faire un vrai affichage des erreurs, pourquoi pas une méthode de $config ?
				if($config->get_error_no_data_dir())
				{
					echo "Cakebox a besoin d'un dossier DATA et d'un dossier DOWNLOADS pour fonctionner.";
				}
				else
				{
					if($config->get_error_no_data_dir())
					{
						echo "Le chmod de DATA et DOWNLOADS doit être 777.";
					}
				}
			?>

			<h2><?php echo $lang[$config->get('lang')]['index_main_title']; ?></h2>	
			<hr class="clear" />			
			
			<!-- Local files -->
			<div id="local">
				<?php
					$treeStructure = new FileTree($config->get('download_dir'));
					$treeStructure->print_tree();
				?>
			</div>
			<!-- / Local files -->
			
        </section>
        <!-- / CONTENT -->

	<!-- FOOTER -->
    <footer>
    	<div class="padding">
        </div>
    </footer>
    <!-- / FOOTER -->

    <div id="config_panel">
    	<h1>Panneau de configuration</h1>
    	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum libero purus, convallis nec vestibulum eget, luctus vitae purus. Vestibulum non mauris et sem vulputate pellentesque ac a turpis. Ut vel lacus vitae justo vestibulum lobortis. Nunc ipsum ipsum, laoreet id dictum nec, fermentum vel purus. Maecenas nisl felis, faucibus non rutrum eu, sollicitudin sed ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent dignissim lacinia tempus. Nulla facilisi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nulla facilisi. Nulla accumsan pellentesque velit, a malesuada diam tristique a. Fusce eleifend magna erat, et imperdiet orci. Quisque sapien mauris, malesuada eu tristique pulvinar, placerat id ligula. Vivamus vitae viverra nulla. Donec eget turpis vel erat malesuada sodales.</p>
    	<a class="modal_close" href="#">
    		Fermer
    	</a>
    </div>
    <div id="howto_update">
    	<h1>Comment mettre à jour Cakebox ?</h1>
    	<p>Connectez-vous en SSH sur votre serveur et placez-vous à la racine de votre serveur web, là où se trouve le dossier de Cakebox : </p>
    	<div class="terminal">
    		<span class="prompt">$</span> cd /var/www
    	</div>
    	<p>Il ne vous reste qu'à taper cette commande pour lancer la mise à jour :</p>
    	<div class="terminal">
    		<span class="prompt">$</span> wget http://www.github.com/MardambeyK/cakebox/raw/scrips/update.sh && chmod +x update.sh && ./update.sh
    	</div>
    	<p>C'est fini ! Cakebox est à jour. Bon stream.</p>

    	<a class="modal_close" href="#">
    		Fermer
    	</a>
    </div>
</body>
</html>
