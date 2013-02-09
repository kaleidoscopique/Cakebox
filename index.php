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

    <script>
    $(function() {
    	// Chargement du background configuré
    	$('body').css('background-image', 'url(ressources/backgrounds/<?php echo $config->get('background'); ?>)');
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
        </header>
        <!-- / HEADER -->

        <?php
	        // Verifie les MàJ (+ affichage)
	       if($update->is_update_available()):
	    ?>

			<div id="update">
				<h3><?php echo $lang[$config->get('lang')]['new_version']." : v".$update->get_current_version()." !" ?></h3>
				<ul>
				    <?php foreach($update->get_changelog() as $change) echo "<li>$change;</li>"; ?>
				</ul>


				<div id="button_zone">
		            <div class="button">
		                <a href="index.php?do_update">
		                    <img src="ressources/clouddownload.png" /> <br />
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
</body>
</html>
