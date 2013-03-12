<?php
// Includes
require_once('inc/inc.lang.php');
require_once('inc/Configuration.class.php');
require_once('inc/FileTree.class.php');
require_once('inc/File.class.php');
require_once('inc/Update.class.php');

// File infos
$fullpath = $_GET['file'];

// Initilisation de l'objet
$file = new File($fullpath);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>CakeBox - <?php echo $lang[$config->lang]['watch_title']; ?></title>
    <?php require_once('inc/inc.header.php'); ?>
    <script type="text/javascript" src="ressources/jquery.tinyscrollbar.min.js"></script>
    <script>
    $(document).ready( function(){
        $('#tree_zone').tinyscrollbar();
    });
    </script>
</head>

<body>
    <!-- TOPBAR -->
    <?php require_once('inc/inc.topbar.php'); ?>
    <!-- / TOPBAR -->

    <section id="content">
        <h2><?php echo $file->name; ?></h2>
        
        <div id="popcorn" class="littleh2">
            <?php if (!$file->sticky): ?>
            <?php echo $lang[$config->lang]['have_you_finished']; ?>
            <span class="mark" onclick="markfile('<?php echo addslashes($pathInfo['basename']); ?>');"><?php echo $lang[$config->lang]['click_remind']; ?></span>
                <a href="#" class="tooltip" style="text-decoration: underline;">
                    <?php echo $lang[$config->lang]['what_zat']; ?>
                    <span><?php echo $lang[$config->lang]['popcorn_details']; ?></span>
                </a>

            <?php else: ?>
                Hey, <span class="unmark"><?php echo $lang[$config->lang]['do_you_remember']; ?></span>
                <span class="update_info" style="text-decoration: underline;cursor:pointer;" onclick="unmarkfile('<?php echo addslashes($file->name); ?>')"><?php echo $lang[$config->lang]['cancel_please']; ?></span>

            <?php endif; ?>
        </div>
        <hr class="underh2" />

        <?php include('watch/inc.'.$file->watch_include.'.php'); ?>

        <div class="watch_content_bloc">
            <div class="title">
                Informations sur le fichier
            </div>
            <div id="download_zone">
                <strong><?php echo $lang[$config->lang]['size']; ?></strong> : <?php echo $file->get_size(); ?><br />
                <a href="<?php echo $file->url; ?>" download="<?php echo $file->url; ?>" class="download_link">Cliquez ici pour le télécharger</a>
            </div>

        </div>

        <div class="watch_content_bloc">
            <div class="title">
                Dans le même répertoire que ce fichier
            </div>
            <div id="tree_zone">
                <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
                    <div class="viewport">
                        <div class="overview">
                <?php
                    $treeStructure = new FileTree($file->dirname);
                    $treeStructure->print_tree();
                ?>
                        </div>
                    </div>
            </div>
        </div>

        <br />
        <br />
    </section>

    <!-- MODAL PAGES -->
    <?php require_once('inc/inc.modal_pages.php'); ?>
    <!-- / MODAL -->
</body>
</html>
