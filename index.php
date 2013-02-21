<?php
// Includes
require_once('inc/lang.php');
require_once('inc/Configuration.class.php');
require_once('inc/FileTree.class.php');
require_once('inc/File.class.php');
require_once('inc/Update.class.php');

// Request : IGNORE UPDATE
if(isset($_GET['ignore_update'])) $update->ignore();

// Request : UPDATE CONFIG
if(isset($_POST['submit'])) $config->update($_POST);
?>

<!DOCTYPE html>
<html lang="fr">
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
	            <a href="#howto_update" data-toggle="modal">
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
	<?php endif; // Update Available ?>

	<!-- CONTENT -->
	<section id="content">
		<?php
			// Les dossiers data et downloads n'existent pas
			if($config->get_error_no_data_dir()):
				echo'<div class="alert alert-error">
						<strong>Attention !</strong> Cakebox a besoin d\'un dossier "data" et d\'un dossier "downloads" à la racine. Merci de les créer.<br />
						Voici la commande à utiliser depuis votre accès SSH :<br /><br />
						<div class="terminal">
							<span class="prompt">$</span> mkdir '.$config->get('cakebox_absolute_path').'downloads ; mkdir '.$config->get('cakebox_absolute_path').'data
						</div>
					</div>';
			else:
				// Le chmod des dossiers downloads et data n'est pas bon
				if($config->get_error_chmod_data_dir()):
					echo'<div class="alert alert-error"> 
					<strong>Attention !</strong> Le chmod du dossier "data" et celui du dossier "downloads" doivent être 777. Merci de les modifier.<br />
						Voici la commande à utiliser depuis votre accès SSH :<br /><br />
						<div class="terminal">
							<span class="prompt">$</span> chmod -R 777 '.$config->get('cakebox_absolute_path').'downloads ; chmod -R 777 '.$config->get('cakebox_absolute_path').'data
						</div>
					</div>';
				endif;
			endif;
		?>

		<h2><?php echo $lang[$config->get('lang')]['index_main_title']; ?></h2>	
		<hr class="underh2" />			
			
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
	<!-- / MODAL -->
</body>
</html>
