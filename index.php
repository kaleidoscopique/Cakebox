<?php

require_once('inc/lang.inc.php');
require_once('inc/functions.inc.php');

// Get the editmode status
$editmode = (EDITMODE_ENABLE && isset($_GET['editmode'])) ? TRUE:FALSE;

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

// Request : UPDATE
if(isset($_GET['do_update']))
{
	// Force the MAJ with ?do_update&force_update
	if(isset($_GET['force_update'])) $force = true;
	else $force = false;

	// Execute update
	do_update($force);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="robots" content="noindex"/>
    <title>CakeBox - <?php echo $lang[LOCAL_LANG]['index_title']; ?></title>
    <meta charset="utf-8">
    <script type="text/javascript" src="ressources/oXHR.js"></script>
    <link rel="stylesheet" href="ressources/style.css" type="text/css" media="screen">
    <link rel="stylesheet" href="ressources/reset.css" type="text/css" media="screen">
    <link rel="stylesheet" href="ressources/tooltips.css" type="text/css" media="screen" />
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
	        if(($update_info = check_update())) show_update($update_info);

	        // Show a message after an update
	        if(isset($_GET['update_done'])) show_update_done();
      	?>


        <!-- CONTENT -->
        <section id="content">

			<?php
				// Test chmod of main directories
				check_dir();
			?>

			<h2><?php echo $lang[LOCAL_LANG]['index_main_title']; ?></h2>	
			<hr class="clear" />

			<!-- EDITMODE MENU -->
			<p>
				<?php
					// Display a short sentence about the editmode (on/off)
					if(EDITMODE_ENABLE && !$editmode)
					  echo '<a class="goeditmode" href="?editmode">'.$lang[LOCAL_LANG]['enter_edit_mode'].'</a>';
					else if ($editmode)
					  echo '<a class="goeditmode" href="index.php">'.$lang[LOCAL_LANG]['leave_edit_mode'].'</a>';
				?>
			</p>
			
			<?php
				// Open form for editmode
				if($editmode):
			?>
				<form name="editform" action="index.php?editmode" method="post">
			<?php endif; ?>
			
			
			<!-- Local files -->
			<div id="local">
				<?php
					$listof_dir 	=	 array(); // Filled by recursive_directory_tree as a global var (for list of dir in editmode)
					$tree_structure =	 recursive_directory_tree(LOCAL_DL_PATH);
					print_tree_structure($tree_structure, $editmode);
				?>
			</div>
			<!-- / Local files -->
			
			<?php
				// Show the editbox
				if($editmode):
			?>
			<div class="editbox">
			
				<!-- Create dir form-->
				<p>
					<?php echo $lang[LOCAL_LANG]['create_new_dir']; ?>
					<select name="mkdirSelect">
						<option value="<?php echo LOCAL_DL_PATH; ?>">/</option>
						<?php foreach($listof_dir as $dir) { echo '<option value="'.$dir.'">'.ustr_replace(LOCAL_DL_PATH,"",$dir).'</option>'; } ?>
					</select>
					<input type="text" value="<?php echo $lang[LOCAL_LANG]['name_new_dir'];?>" onblur="if(this.value=='') this.value='<?php echo $lang[LOCAL_LANG]['name_new_dir'];?>'" onclick="if(this.value=='<?php echo $lang[LOCAL_LANG]['name_new_dir'];?>') this.value='';" name="mkdir_name"/>
					<input type="submit" value="<?php echo $lang[LOCAL_LANG]['create_new_dir_button']; ?>" name="mkdir"/>
				</p>
				<!-- / Create dir form-->
				
				<!-- Move dir&file form-->
				<p>
					<?php echo $lang[LOCAL_LANG]['move_dir']; ?>
					<select name="moveSelect">
						<option value="<?php echo LOCAL_DL_PATH; ?>">/</option>
						<?php foreach($listof_dir as $dir) { echo '<option value="'.$dir.'">'.ustr_replace(LOCAL_DL_PATH,"",$dir).'</option>'; } ?>
					</select>
					<input type="submit" value="<?php echo $lang[LOCAL_LANG]['move_dir_button']; ?>" name="move"/>
				</p>
				<!-- / Move dir&file form-->
				
				<!-- Delete dir&file form-->
				<p>
					<?php echo $lang[LOCAL_LANG]['delete_content']; ?>
					<input type="submit" value="<?php echo $lang[LOCAL_LANG]['delete_content_button']; ?>" name="delete"/>
				</p>
				<!-- / Delete dir&file form-->

			</div>
			</form>
			<?php endif;  ?>
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
