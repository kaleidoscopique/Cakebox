<?php

require('inc/lang.inc.php');
require('inc/functions.inc.php');

// File infos
$fullpath = $_GET['file'];

// Initilisation de l'objet
$file_type = File::get_type($fullpath);
if($file_type == "video") $file = new Video($fullpath);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="robots" content="noindex" />
    <title>CakeBox - <?php echo $lang[$config->get('lang')]['watch_title']; ?></title>
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
    <header>
        <div id="logo">
            <a href="index.php">
                <span class="first">Cake</span>
                <span class="second">Box</span>
            </a>
        </div>
    </header>

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

        <hr class="clear" />


        <?php 
        // Gestion des vidéos
        if(File::isVideo($file->get_name())): 
        ?>
            <center>
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
            if ($file->get_next())
            {
                echo '<div style="margin:40px 0px 10px 0px;">';
                echo '<a href="watch.php?file='.$prev.'" class="next_episode">';
                echo "← ".$lang[$config->get('lang')]['watch_previous'];
                echo '</a></div>';
            }
            if ($file->get_prev())
            {
                echo '<div style="margin:10px 0px 40px 0px;padding-left:30px;">';
                echo '<a href="watch.php?file='.$next.'" class="next_episode">';
                echo $lang[$config->get('lang')]['watch_next']." →";
                echo '</a></div>';
            }
            ?>
            </center>
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
                <strong><?php echo $lang[$config->get('lang')]['size']; ?></strong> <?php echo File::get_file_size($file->get_fullname()); ?>
            </span>
        </div>
        <br />
        <br />
    </section>

    <footer>
        <div class="padding"></div>
    </footer>
</body>
</html>
