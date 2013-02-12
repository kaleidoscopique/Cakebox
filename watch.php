<?php
// Includes
require_once('inc/lang.php');
require_once('inc/Configuration.class.php');
require_once('inc/FileTree.class.php');
require_once('inc/File.class.php');
require_once('inc/Update.class.php');

// File infos
$fullpath = $_GET['file'];

// Initilisation de l'objet
$file_type = File::get_type($fullpath);
if($file_type == "video") $file = new Video($fullpath);

// TODO : gestion des autres types de fichier (+ fichiers génériques)

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>CakeBox - <?php echo $lang[$config->get('lang')]['watch_title']; ?></title>
    <?php require_once('inc/header.php'); ?>
</head>

<body>
    <!-- TOPBAR -->
    <?php require_once('inc/topbar.php'); ?>
    <!-- / TOPBAR -->

    <section id="content">
        <h2><?php echo $file->get_name(); ?></h2>
        
        <div id="popcorn" class="littleh2">
            <?php
                // If file is not marked as "already seen"
                if (!file_exists("data/".$file->get_name())):
            ?>
            <?php echo $lang[$config->get('lang')]['have_you_finished']; ?>
            <span class="mark" onclick="markfile('<?php echo addslashes($pathInfo['basename']); ?>');"><?php echo $lang[$config->get('lang')]['click_remind']; ?></span>
                <a href="#" class="tooltip" style="text-decoration: underline;">
                    <?php echo $lang[$config->get('lang')]['what_zat']; ?>
                    <span><?php echo $lang[$config->get('lang')]['popcorn_details']; ?></span>
                </a>

            <?php else: ?>
                Hey, <span class="unmark"><?php echo $lang[$config->get('lang')]['do_you_remember']; ?></span>
                <span class="update_info" style="text-decoration: underline;cursor:pointer;" onclick="unmarkfile('<?php echo addslashes($pathInfo['basename']); ?>')"><?php echo $lang[$config->get('lang')]['cancel_please']; ?></span>

            <?php endif; ?>
        </div>
        <hr class="underh2" />

        <?php 
        // Gestion des vidéos
        if(File::isVideo($file->get_name())): 
        ?>
            <div id="web-player">
            <?php if ($config->get('video_player') == "divxwebplayer"): ?>
                <object classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616" width="640" height="480" codebase="http://go.divx.com/plugin/DivXBrowserPlugin.cab">
                    <param name="custommode" value="none" />
                    <param name="autoPlay" value="false" />
                    <param name="src" value="<?php echo $file->get_url(); ?>" />
                    <embed type="video/divx" src="<?php echo $file->get_url(); ?>" custommode="none" width="640" height="480" autoPlay="false" pluginspage="http://go.divx.com/plugin/download/"></embed>
                </object>
            <?php elseif ($config->get('video_player') == "vlc"): ?>
                <embed type="application/x-vlc-plugin" name="VLC" autoplay="yes" loop="no" volume="100" width="640" height="480" target="<?php echo $file->get_url(); ?>">
            <?php endif; ?>

            <?php
            // Affiche Précédent et Suivant si possible
            if ($file->get_next()):
                echo '<div class="next_file">';
                echo '<a href="watch.php?file='.$prev.'">';
                echo "← ".$lang[$config->get('lang')]['watch_previous'];
                echo '</a></div>';
            endif;

            if ($file->get_prev()):
                echo '<div class="previous_file">';
                echo '<a href="watch.php?file='.$next.'">';
                echo $lang[$config->get('lang')]['watch_next']." →";
                echo '</a></div>';
            endif;
            ?>
            </div>
        <?php endif; ?>

        <div id="button_zone">

            <div class="button">
                <a href="<?php echo $file->get_url(); ?>" download="<?php echo $file->get_url(); ?>">
                    <img src="ressources/clouddownload.png" /><br />
                    <?php echo $lang[$config->get('lang')]['download']; ?>
                </a>
            </div>
            <span class="under_button">
                <?php echo $lang[$config->get('lang')]['right_click']; ?><br/>
                <strong><?php echo $lang[$config->get('lang')]['size']; ?></strong> : <?php echo File::get_file_size($file->get_fullname()); ?>
            </span>
        </div>
        <br />
        <br />
    </section>

    <!-- MODAL PAGES -->
    <?php require_once('inc/modal_pages.php'); ?>
    <!-- / MODAL -->
</body>
</html>
