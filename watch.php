<?php
require('inc/functions.inc.php');

// Fichier à lire et son extention
$file = htmlspecialchars($_GET['file']);
$ext = get_file_icon(basename($file),TRUE);

// On récupère la vidéo suivante dans l'arborescence, si on est sur un fichier vidéo
if($ext == "avi") $nextnprev = get_nextnprev($file);
$prev = $nextnprev['prev'];
$next = $nextnprev['next'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>CakeBox - Download or watch your file</title>
    <meta charset="utf-8">
    <link href='http://fonts.googleapis.com/css?family=Changa+One|Droid+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="ressources/style.css" type="text/css" media="screen">
    <link rel="stylesheet" href="ressources/reset.css" type="text/css" media="screen">
    <link rel="stylesheet" href="ressources/tooltips.css" type="text/css" media="screen" />
    <script src="ressources/oXHR.js"></script>
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
    
      <h2><?php echo ustr_replace(LOCAL_DL_PATH."/","",$file ); ?></h2>
            
            <?php  if($ext == "avi"){ // if it's a video file ?>
                <div id="popcorn" class="littleh2">
                <?php if(!file_exists("data/".basename($file))){ ?>
                    Have you finished watching this video ?
                    <span class="mark" onclick="markfile('<?php echo basename($file); ?>');">Click here to remind you next time</span>
                    <a href="#" class="update_info" style="text-decoration: underline;"> 
                    (What's that ?)
                  <span class="tooltip">
                          <span></span>
                          Like everyone else, you never know what episode you stopped in your series.<br/>
                          Click on the previous link to mark an episode and know quickly if you have already seen or not (it will be underlined in the main list)
                  </span>
                  </a>
              <?php } else echo  'Hey, <span class="unmark">you\'ve already seen this video</span>, do you remember ? <span class="update_info" style="text-decoration: underline;cursor:pointer;" onclick="unmarkfile(\''.basename($file).'\')">No, cancel please !</span>'; ?>
              </div>
          <?php } ?>
    <hr class="clear" />
          
      <?php if($ext == "avi"){ // if it's a video file  ?>
    <p style="text-align:center;margin-bottom:10px;">
    <a href="https://github.com/MardamBeyK/Cakebox/wiki/Je-n%27arrive-pas-%C3%A0-lire-les-vid%C3%A9os-en-streaming-!" target="_blank" class="help">Help ! I can't watch the video !</a>
    </p>

    <center>
      <object id='mediaPlayer' width="600" height="400" classid='CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95' codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701' standby='Loading Media Player components...' type='application/x-oleobject'>
        <param name='fileName' value="<?php echo $file; ?>">
        <param name='animationatStart' value='true'>
        <param name='transparentatStart' value='true'>
        <param name='autoStart' value="false">
        <param name='showControls' value="true">
        <param name="ShowStatusBar" value="true">
        <param name='loop' value="0">
        <embed type='application/x-mplayer2' pluginspage='http://microsoft.com/windows/mediaplayer/en/download/' src="<?php echo $file; ?>" width="600" height="400" autostart="0" displaysize='4' autosize='0' bgcolor='black' showcontrols="0" showtracker='0' ShowStatusBar='1' showdisplay='0' videoborder3d='0' designtimesp='5311' loop="0"></embed>    
        </object>

        <?php

          if($prev != NULL)
          {
            echo '<div style="margin:40px 0px 10px 0px;">';
            echo '<a href="watch.php?file='.$prev.'" class="next_episode">';
            echo "← Watch the previous episode";
            echo '</a></div>';
          }


          if($next != NULL)
          {
            echo '<div style="margin:10px 0px 40px 0px;padding-left:30px;">';
            echo '<a href="watch.php?file='.$next.'" class="next_episode">';
            echo "Watch the next episode →";
            echo '</a></div>';
          }
        ?>

    </center>
      <?php } ?>
         
      <div class="download_button">
    <a href="<?php echo $file; ?>"><img src="ressources/downloadfile.png" /></a><br/>
                Right click and "Save link as" to download it<br/>
                <strong>Size :</strong> <?php echo convert_size(filesize($file)); ?>
      </div>

<br />
<br />
    
        </section>
  <!--==============================footer=================================-->
    <footer>
      <div class="padding">
  
        </div>
    </footer>
</body>
</html>
