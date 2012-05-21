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
    
    <script type="text/javascript">
  $(document).ready(function() {
    $('#slickbox').hide();
    $('#slick-slidetoggle').click(function() {
      $('#slickbox').slideToggle(400);
      return false;
    });
});
    </script>
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
    <a id="slick-slidetoggle" href="#" class="help">Help ! I can't watch the video !</a>
    </p>

    <div id="slickbox">
                <h3 style="font-weight: bold;">I'm missing a codec | The player does not appear</h3>   
                    <p style="margin:20px;">
                    <img src="ressources/rsz_debian.png" /> You are using Ubuntu/Debian : <span class="terminal">apt-get install ffmpeg libavcodec-unstripped-52 libavdevice-unstripped-52 libavformat-unstripped-52 libavutil-unstripped-50 libpostproc-unstripped-51 libswscale-unstripped-0</span>
                    </p>
                    
                    <p style="margin:20px;">
                    <img src="ressources/rsz_fedora.png" /> You are using Fedora : <span class="terminal">yum install gstreamer-ffmpeg gstreamer-plugins-bad gstreamer-plugins-ugly</span></p>
                    
                    <p style="margin:20px;">
                    <img src="ressources/rsz_windows.png" /> You are using Windows or Mac : <a href="http://www.videolan.org/vlc/#download">download VLC 2.0</a> and install it with the Mozilla Plugin (you can choose during installation). 
                    Thanks to this plugin, VLC will be recognize by all the browsers.
                    </p>
                    
                    <h3 style="font-weight: bold;">The player does not play the video !</h3>
                    <p style="margin:20px;">Click on "Download the file" below the video, this should force your player to play the video.</p>
    </div>
    
    <center>
    <object classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616"
        width="600" height="400"
        codebase="http://go.divx.com/plugin/DivXBrowserPlugin.cab">
        <param name="src" value="<?php echo $file; ?>"/>
         
        <embed
      type="video/divx"
      src="<?php echo $file; ?>"
      width="600" height="400"
      pluginspage="http://go.divx.com/plugin/download/">
        </embed>
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
