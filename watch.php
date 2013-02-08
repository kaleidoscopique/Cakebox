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
    <link rel="icon" type="image/ico" href="favicon.ico" />

    <!-- Style & ergo -->
    <link href='http://fonts.googleapis.com/css?family=Changa+One|Droid+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="ressources/style.css" type="text/css" media="screen">
    <link rel="stylesheet" href="ressources/reset.css" type="text/css" media="screen">
    <script lang="javascript">
        var lang_ok_unmark = "<?php echo $lang[$config->get('lang')]['ok_unmark']; ?>";
        var lang_ok_mark = "<?php echo $lang[$config->get('lang')]['ok_mark']; ?>";
    </script>
    <script src="ressources/oXHR.js"></script>
    <!-- / Style & ergo -->

    <?php if ($config->get('video_player') == "vlc" && File::isVideo($file->get_name())): ?>
    <!-- VLC Controls -->
    <link rel="stylesheet" type="text/css" href="ressources/vlc-styles.css" />
    <script language="javascript" src="ressources/jquery.min.js"></script>
    <script language="javascript" src="ressources/jquery-vlc.js"></script>
    <script language="javascript">
        function play(instance, uri) {
            VLCobject.getInstance(instance).play(uri);
        }
        var player = null;
        $(document).ready(function() {
            player = VLCobject.embedPlayer('vlc1', 600, 400, true);
        });
    </script>
    <!-- / VLC Controls -->
    <?php endif; ?>
</head>

<body <?php if ($config->get('video_player') == "vlc" && File::isVideo($file->get_name())): ?> onload="play('vlc1', '<?php echo $config->get('download_link').addslashes($filePath); ?>')" <?php endif; ?>>
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
                <!-- Embed DivX Player -->
                <object classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616" width="640" height="480" codebase="http://go.divx.com/plugin/DivXBrowserPlugin.cab">
                    <param name="custommode" value="none" />
                    <param name="autoPlay" value="false" />
                    <param name="src" value="<?php echo $file->get_url(); ?>" />
                    <embed type="video/divx" src="<?php echo $file->get_url(); ?>" custommode="none" width="640" height="480" autoPlay="false" pluginspage="http://go.divx.com/plugin/download/"></embed>
                </object>
                <!-- / DivX -->
            <?php elseif ($config->get('video_player') == "vlc"): ?>
                <!-- Embed VLC -->
                <div id="vlc1" style="margin-bottom:50px;">player 1</div>
                <!-- / VLC -->
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

        <div class="download_button">
            <a href="<?php echo $file->get_url(); ?>" download="<?php echo $file->get_url(); ?>">
              <img src="ressources/<?php echo $lang[$config->get('lang')]['file_img_download']; ?>" />
            </a><br/>
            <?php echo $lang[$config->get('lang')]['right_click']; ?><br/>
            <strong><?php echo $lang[$config->get('lang')]['size']; ?></strong> <?php echo File::get_file_size($file->get_fullname()); ?>
        </div>
        <br />
        <br />
    </section>

    <footer>
        <div class="padding"></div>
    </footer>
</body>
</html>
