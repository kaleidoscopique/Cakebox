<?php

require_once('inc/lang.php');
require_once('inc/functions.php');

// Request : IGNORE UPDATE
if(isset($_GET['ignore_update'])) $update->ignore();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>CakeBox - <?php echo $lang[$config->get('lang')]['index_title']; ?></title>
    <?php require_once('inc/header.php'); ?>
</head>
<body>
	<!-- TOPBAR -->
	<?php require_once('inc/topbar.php'); ?>
	<!-- / TOPBAR -->

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

	<!-- MODAL PAGES -->
	<?php require_once('inc/modal_pages.php'); ?>
	<div id="howto_update" class="modal_window">
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
	<!-- / MODAL -->
</body>
</html>
