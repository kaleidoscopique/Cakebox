<?php

require('inc/config.inc.php');
require('inc/functions.inc.php');

// check the editmode
$editmode = (isset($_GET['editmode'])) ? TRUE:FALSE;

// delete files
if(isset($_POST['delete']))
{
	foreach($_POST['Files'] as $file)
	{
		if(is_dir($file)) @rrmdir($file);
		else @unlink($file);
	}
}

// move files
if(isset($_POST['move']))
{
	foreach($_POST['Files'] as $file)
	{
		if(is_dir($file)) @rename($file,$_POST['moveSelect']."/".basename($file));
		else if(file_exists($file)) @rename($file,$_POST['moveSelect']."/".basename($file));
	}
}

// mkdir
if(isset($_POST['mkdir']) && !empty($_POST['mkdir_name']))
{
	mkdir($_POST['mkdirSelect']."/".$_POST['mkdir_name'],0777);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>CakeBox - Select a file to download or stream it</title>
    <meta charset="utf-8">
    <script type="text/javascript" src="ressources/oXHR.js"></script>
    <link rel="stylesheet" href="ressources/style.css" type="text/css" media="screen">
    <link rel="stylesheet" href="ressources/reset.css" type="text/css" media="screen">
    <link rel="stylesheet" href="ressources/tooltips.css" type="text/css" media="screen" />
    <link href='http://fonts.googleapis.com/css?family=Changa+One|Droid+Sans' rel='stylesheet' type='text/css'>
    <link rel="icon" type="image/ico" href="favicon.ico" />
</head>
<body>
        <!-- ==============================header================================= -->
        <header>
	    <div id="logo">
		<a href="index.php"><span class="first">Cake</span><span class="second">Box</span></a>
	    </div>
        </header>
        <!--==============================content================================-->
        <section id="content">
	
			<?php check_dir(); // Test les chmod des dossiers importants ?>
			
			<h2>Click the icon to download or stream your files</h2>	    
			<hr class="clear" />
			<!--<p id="loader" style="display: none;text-align: center;"><img src="ressources/ajax-loader.gif" alt="loading" /></p>-->
			
			<!-- ========== MANAGEMENT MENU =========== -->		
			<p>
				<?php
					/* Check if user is in editmode or not (for javascript request) */		
					$suffix = ($editmode) ? "-edit":"" ?>
					<select id="filterSelect" onchange="filesfilter(this);">
						<option value="all<?php echo $suffix; ?>" default>Show all files</option>
						<option value="videos<?php echo $suffix; ?>">Show video files only</option>
					</select>
					
				<?php
					// Display a short sentence about the editmode (on/off)
					if(!$editmode) echo '<a class="goeditmode" href="?editmode">Go to edit mode (create, move and rename content)</a>';
					else echo '<a class="goeditmode" href="index.php">Leave edit mode now</a>';
				?>
			</p>
			
			<?php
			/* ======== CREATE FORM FOR EDITMODE ========== */		
			if($editmode): ?> <form name="editform" action="index.php?editmode" method="post" > <?php endif; ?>
			
			
			<!-- ========== LOCAL FILES =========== -->
			<div id="local">
				<?php
				$listof_dir = array(); // global var filled by recursive_directory_tree()
				$tree_structure = recursive_directory_tree(LOCAL_DL_PATH);
				print_tree_structure($tree_structure,"all",$editmode);
				?>
			</div>
			
			<?php
			/* ======== THE EDITBOX ======= */		
			if($editmode): ?>
			<div class="editbox">
			
				<!-- ========== CREATE NEW DIR =========== -->
				<p>
				Create a new dir in...
				<select name="mkdirSelect">
					<option value="<?php echo LOCAL_DL_PATH; ?>">/</option>
					<?php foreach($listof_dir as $dir) { echo '<option value="'.$dir.'">'.ustr_replace(LOCAL_DL_PATH,"",$dir).'</option>'; } ?>
				</select>
				<input type="text" value="name of the new dir" onblur="if(this.value=='') this.value='name of the new dir'" onclick="if(this.value=='name of the new dir') this.value='';" name="mkdir_name"/>
				<input type="submit" value="Create !" name="mkdir"/>
				</p>
				
				<!-- ========== MOVE FILES =========== -->
				<p>
				Move selected file(s) to...
				<select name="moveSelect">
					<option value="<?php echo LOCAL_DL_PATH; ?>">/</option>
					<?php foreach($listof_dir as $dir) { echo '<option value="'.$dir.'">'.ustr_replace(LOCAL_DL_PATH,"",$dir).'</option>'; } ?>
				</select>
				<input type="submit" value="Let's move !" name="move"/>
				</p>
				
				<!-- ========== DELETE FILES =========== -->
				<p>
				Delete selected file(s) ?
				<input type="submit" value="Yes, delete it/them !" name="delete"/>
				</p>
			</div>
			</form>
			<?php endif; ?>
			<!-- /endof -->
        </section>
	<!--==============================footer=================================-->
    <footer>
    	<div class="padding">
	
        </div>
    </footer>
</body>
</html>
