<?php
require('inc/config.inc.php');
require('inc/functions.inc.php');

$file = htmlspecialchars($_GET['file']);
$ext = get_file_icon(basename($file),TRUE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>CakeBox - Download or watch your file</title>
    <meta charset="utf-8">
    <script src="ressources/jquery.min.js"></script>
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
                    <span class="mark" onclick="markfile('<?php echo basename($file); ?>');">Click here to remember it next time</span>
                    <img src="ressources/popcorn.png" />
                    <a href="#" class="update_info" style="text-decoration: underline;"> 
                    (WTF ?)
                  <span class="tooltip">
                          <span></span>
                          Like everyone else, you never know what episode you stopped in your series.<br/>
                          Click on the previous link to mark an episode and know quickly if you have already seen or not.
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
    </center>
      <?php } ?>
      
      <div class="file_info">
    <?php
        echo '
        <strong>Size :</strong> '.convert_size(filesize($file)).'<br/>
        Last update : '.date("d F Y, H:i",filemtime($file)).'<br/>
        Last access : '.date("d F Y, H:i",fileatime($file)).'<br/>';
    ?>
      </div>      
      <div class="download_button">
    <a href="<?php echo $file; ?>"><img src="ressources/downloadfile.png" /></a><br/>
                Right click and "Save link as" to download it
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
