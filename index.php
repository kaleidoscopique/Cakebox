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
{
	mkdir($_POST['mkdirSelect']."/".$_POST['mkdir_name'],0777);
}

// Request : DO UPDATE
if(isset($_GET['do_update']))
{
	// Force the MAJ with ?do_update&force_update
	if(isset($_GET['force_update'])) $force = true;
	else $force = false;

	// Execute update
	do_update($force);
}

// Request : IGNORE UPDATE
if(isset($_GET['ignore_update']))
{
	ignore_update($_GET['number']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="robots" content="noindex"/>
    <title>CakeBox - <?php echo $lang[$config->get('lang')]['index_title']; ?></title>
    <meta charset="utf-8">
    <script type="text/javascript" src="ressources/oXHR.js"></script>
    <link rel="stylesheet" href="ressources/style.css" type="text/css" media="screen">
    <link rel="stylesheet" href="ressources/reset.css" type="text/css" media="screen">
    <link href='http://fonts.googleapis.com/css?family=Changa+One|Droid+Sans' rel='stylesheet' type='text/css'>
    <link rel="icon" type="image/ico" href="favicon.ico" />
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
	        // Verify if Cakebox is up to date
	        //if(($update_info = check_update())) show_update($update_info);

	        // Show a message after an update
	        //if(isset($_GET['update_done'])) show_update_done();
      	?>


        <!-- CONTENT -->
        <section id="content">

			<?php
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
					// $listof_dir 	=	 array(); // Filled by recursive_directory_tree as a global var (for list of dir in editmode)
					// $tree_structure =	 recursive_directory_tree(LOCAL_DL_PATH);
					// print_r($tree_structure);
					// echo "<br /><br /><br />";
				?>

				<?php
					$treeStructure = new FileTree("downloads");
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
