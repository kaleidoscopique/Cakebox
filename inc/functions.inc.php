<?php

// Lancement de la session PHP du client
session_start();

/********************************/
/*      AUTHENTIFICATION        */
/********************************/

// Récupération des informations de connexion via le .htaccess s'il est utilisé
if(isset($_SERVER['REMOTE_USER']) && isset($_SERVER['PHP_AUTH_PW']))
{
  $_SESSION['ht_user']   = $_SERVER['REMOTE_USER'];
  $_SESSION['ht_pwd']    = $_SERVER['PHP_AUTH_PW'];
}

// Si l'utilisateur utilise bien un .htaccess pour s'authentifier, on update le link des fichiers
// Un lien vers un fichier protégé ressemble à http://login:password@domaine/cakebox/downloads/fichier.ext
// Un lien vers un fichier non-protégé ressemble à http://domaine/cakebox/downloads/fichier.ext
if(isset($_SESSION['ht_user']) && isset($_SESSION['ht_pwd']))
  $identity_inLink = $_SESSION['ht_user'].":".$_SESSION['ht_pwd']."@".$_SERVER['HTTP_HOST'];
else 
  $identity_inLink = $_SERVER['HTTP_HOST'];

/********************************/
/*        CONFIGURATION         */
/********************************/

// Configuration par défaut
if (!file_exists("config.php"))
{
  define('LOCAL_LANG', 'fr');                  // Modification de la langue (EN ou FR)
  define('TIME_CHECK_UPDATE', 12);             // Temps entre chaque vérification de mise à jour (0 = force la MàJ; -1 = désactive)
  define('EDITMODE_ENABLE', TRUE);             // Active ou désactive la fonction d'EDITMODE
  define('SEEN_MODE_ENABLE', TRUE);            // Active ou désactive la fonction de marquage des episodes comme vu
  define('DISPLAY_HIDDEN_FILESDIRS', FALSE);   // Affiche ou ignore les fichiers cachés
  define('IGNORE_CHMOD', FALSE);               // Active ou ignore la vérification des CHMOD sur /data et /downloads
  define('LOCAL_DL_PATH', 'downloads');        // Modifie le dossier que surveille Cakebox
  define('DOWNLOAD_LINK', "http://".$identity_inLink."/cakebox/");  // Modifie l'URL de stream des fichiers
  $excludeFiles = array(".", "..", ".htaccess", "");  // Liste des fichiers ignorés dans le listing de Cakebox
}
// Surcharge la configuration
else
	require_once("config.php");

/********************************/
/*          FONCTIONS           */
/********************************/

function isVideoFile($path)
{
    $pathInfo = pathinfo($path);
    $mime = explode("/", mime_content_type($path));
    if ($mime[0] == "video" || $pathInfo['extension'] == "mkv") // Les films en HD ne passent pas forcement...
        return TRUE;
    return FALSE;
}

/**
 * Retourne le chemin vers l'icone associé
 * @filename Le nom du fichier à considérer
 */
function get_file_icon($filename)
{
  $extension = pathinfo($filename, PATHINFO_EXTENSION);

  if($extension == "avi" || $extension == "mpeg" || $extension == "mp4" || $extension == "mkv") $extension = "avi";
  else if($extension == "mp3" || $extension == "midi" || $extension == "m4a" || $extension == "ogg" || $extension == "flac") $extension = "mp3";
  else if($extension == "iso" || $extension == "rar" || $extension == "zip") $extension = "iso";
  else $extension = "other";

  return "ressources/ext/".$extension.".png";
}

/**
 * Convertit la taille en Xo
 * @param $filePath Le fichier a traiter
 */
function getFileSize($filePath)
{
     $fs = filesize($filePath);

     if ($fs >= 1073741824)
      $fs = round($fs / 1073741824 * 100) / 100 . " Go";
     elseif ($fs >= 1048576)
      $fs = round($fs / 1048576 * 100) / 100 . " Mo";
     elseif ($fs >= 1024)
      $fs = round($fs / 1024 * 100) / 100 . " Ko";
     else
      $fs = $fs . " o";
     return $fs;
}

/**
 * Récupère récursivement le contenu d'un répertoire
 * et le retourne sous forme d'array
 * @param $directory Le répertoire à traiter
 **/
function recursive_directory_tree($directory = null)
{
    global $listof_dir;
    global $excludeFiles;

    //If $directory is null, set $directory to the current working directory.
    if ($directory == null) {
        $directory = getcwd();
    }

    //declare the array to return
    $return = array();

    //Check if the argument specified is an array
    if (is_dir($directory)) {

        array_push($listof_dir,$directory);
        //Scan the directory and loop through the results
        foreach(scandir($directory) as $file) {

            //. = current directory, .. = up one level. We want to ignore both.
            if ($file[0] == "." && !DISPLAY_HIDDEN_FILESDIRS) {
                continue;
            }

            //Exclude some specified files
            if (in_array($file, $excludeFiles)) {
                continue;
            }

            //Check if the current $file is a directory itself.
            //The appending of $directory is necessary here.
            if (is_dir($directory."/".$file))
            {
                //Create a new array with an index of the folder name.
                $return[$directory."/".$file] = recursive_directory_tree($directory."/".$file);
            }
            else
            {
                //If $file is not a directory, just add it to th return array.
                $return[] = $directory."/".$file;
            }
        }
    }
    else
    {
        $return[] = $directory;
    }

    unset($listof_dir[0]);
    return $return;
}

/**
 * Affiche la liste des fichiers sur index.php
 * @param $treestructure L'array contenant la hiérarchie de fichiers
 * @param $filter Le filtre à utiliser (all ou video)
 * @param $editmode Prendre en compte l'editmode dans l'affichage
 * @param $father Un paramètre récursif qui permet de connaître le(s) parent(s) d'un dossier
 */
function print_tree_structure($treestructure, $editmode = FALSE, $father = "")
{
  global $lang;

  if (empty($treestructure))
  {
	  echo '<div style="margin-bottom:5px;" class="onefile" id="div-'.htmlspecialchars($file).'">';
	  echo $lang[LOCAL_LANG]['empty_dir'];
	  echo '</div>';
	  return;
  }

  foreach($treestructure as $key => $file)
  {
    // Si on n'est sur un dossier
    if(is_array($file))
    {
      $key = addslashes(basename($key));
      echo '<div class="onedir">';
      if($editmode) echo '<input name="Files[]" id="Files" type="checkbox" value="'.$father.htmlspecialchars($key).'" onclick="CheckLikes(this);"/>';
      echo '<img src="ressources/folder.png" onclick="showhidedir(\''.$key.'\');return false;" class="pointerLink imgfolder" />
      <span class="pointerLink" onclick="showhidedir(\''.$key.'\');return false;">'.$key.'</span></div>';
      echo '<div id="'.$key.'" class="dirInList" style="display:none;">';
      print_tree_structure($file, $editmode, $father.htmlspecialchars($key)."/");
      echo '</div>';
    }
    else
    {
      echo '<div style="margin-bottom:5px;" class="onefile" id="div-'.htmlspecialchars($file).'">';

      // La checkbox de l'editmode
      if($editmode) echo '<input name="Files[]" id="Files" type="checkbox" value="'.htmlspecialchars($file).'"/>';

      // Affichage des images à gauche du titre (Direct Download + Watch)
      $current =$file;
      echo '<a href="'.DOWNLOAD_LINK.$file.'">';
        echo '<img src="ressources/download.png" title="Download this file" /> &nbsp;';
      echo '</a>';

      echo '<a href="watch.php?file='.$current.'">';
        echo '<img src="'.get_file_icon($file).'" title="Stream or download this file" /> &nbsp;';
      echo '</a>';

      if (SEEN_MODE_ENABLE && file_exists("data/".basename($file)))
      {
	      // Affichage du titre (soulignement si marqué comme vu)
	      echo '<span style="border-bottom:2px dotted #76D6B7;">';
	      echo basename(htmlspecialchars($file));
	      echo '</span>';
      }
      else
          echo basename(htmlspecialchars($file));

      // Création de l'infobulle
      echo '<a href="#" class="tooltip">&nbsp;(?)
      		<span>
              '.$lang[LOCAL_LANG]['size'].' : '.getFilesize($file).'<br/>
              '.$lang[LOCAL_LANG]['last_update'].' : '.date("d F Y, H:i",filemtime($file)).'<br/>
              '.$lang[LOCAL_LANG]['last_access'].' : '.date("d F Y, H:i",fileatime($file)).'<br/>
            </span>
            </a>';

      echo '</div>';
    }
  }
}


/**
 * Supprime un dossier qui n'est pas vide
 * @param $dir Le dossier à supprimer avec son contenu
 */
 function rrmdir($dir)
 {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 }

/*
*  Fonction str_replace() qui ne remplace qu'une occurence
*  @param voir str_replace
*/
function ustr_replace($needle , $replace , $haystack)
{
    // Looks for the first occurence of $needle in $haystack
    // and replaces it with $replace.
    $pos = strpos($haystack, $needle);
    if ($pos === false) {
        // Nothing found
    return $haystack;
    }
    return substr_replace($haystack, $replace, $pos, strlen($needle));
}

/*
 * Vérifie la permission des dossiers importants (downloads et data)
 * et affiche une erreur en cas de besoin
 */
function check_dir()
{
  global $lang;
  $isdir_data = is_dir("data");
  $isdir_downloads = is_dir("downloads");
  if(!$isdir_data || !$isdir_downloads)
  {
    echo '<p style="background:#FF6B7A;padding:10px;color:#FFFFFF;margin-bottom:20px;">';
    echo '<span style="font-weight:bold;">IMPORTANT /!\</span><br/>';
    if(!$isdir_data) echo $lang[LOCAL_LANG]['create_data_dir']."<br/>";
    if(!$isdir_downloads) echo $lang[LOCAL_LANG]['create_downloads_dir']."<br/>";
    echo '</p>';
  }
  // On ignore la vérification des chmod en fonction de IGNORE_CHMOD
  else if(!IGNORE_CHMOD)
  {
    $chmod_data = substr(sprintf('%o', fileperms('data')),-3);
    $chmod_downloads = substr(sprintf('%o',fileperms('downloads')),-3);
    if($chmod_data != 777 || $chmod_downloads != 777)
    {
      echo '<p style="background:#FF6B7A;padding:10px;color:#FFFFFF;margin-bottom:20px;">';
      echo '<span style="font-weight:bold;">IMPORTANT /!\</span><br/>';
      if($chmod_data != 777) echo $lang[LOCAL_LANG]['chmod_data_dir']."<br/>";
      if($chmod_downloads != 777) echo $lang[LOCAL_LANG]['chmod_downloads_dir']."<br/>";
      echo '</p>';
    }
  }
}

/*
 * Récupère l'épisode suivant et l'épisode précédent d'un dossier
 * en fonction de $file (épisode courant).
 * Retourne un array (prev=>X,next=>Y)
 */
function get_nextnprev($file)
{
  $current_dir = recursive_directory_tree(dirname($file));
  $current_file = array_keys($current_dir,$file);
  $current_file = $current_file[0];

  // Si le fichier courant n'est pas le dernier, on a notre $next
  $next = NULL;
  if($current_file != count($current_dir)-1)
  {
      // Si le fichier suivant est bien une vidéo
      if(isVideoFile($current_dir[$current_file+1]))
        $next = htmlspecialchars(urlencode($current_dir[$current_file+1]));
  }

  // Si le fichier courant n'est pas le premier, on a notre prev
  $prev = NULL;
  if($current_file != 0)
  {
      // Si le fichier précédent est bien une vidéo
      if(isVideoFile($current_dir[$current_file-1]))
        $prev = htmlspecialchars(urlencode($current_dir[$current_file-1]));
  }

  return array("prev"=>$prev,"next"=>$next);
}


/*
 * Verifie si une mise à jour est disponible
 * Retourne array("local_version"=>X,"current_version"=>Y,"changelog"=>Z) si une MàJ est disponible
 * retourne array() sinon;
 */
function check_update()
{
  $last_check = fileatime('version.txt');
  $time_since = time()-$last_check;

  // Check for a new version each 12h
  if($time_since > TIME_CHECK_UPDATE * 3600)
  {
    // Files to compare
    $local_version_file     = fopen('version.txt','r');
    $current_version_file   = fopen('https://github.com/MardamBeyK/Cakebox/raw/master/version.txt','r');

    // Num of versions
    $local_version    = fgets($local_version_file);
    $current_version  = fgets($current_version_file);

    // If not up to date
    if(floatval($local_version) < floatval($current_version))
    {
      $description_update = "";
      while(!feof($current_version_file))
      {
        $description_update[] = fgets($current_version_file);
      }

      return array("local_version"=>$local_version,"current_version"=>$current_version,"changelog"=>$description_update);
    }

  } else return array();
}

/*
 * Affiche le div de mise à jour avec changelog si MàJ dispo
 * N'affiche rien sinon
 */
function show_update($update_info)
{
    global $lang;
    $current_version = $update_info['current_version'];
    $description_update = $update_info['changelog'];

    echo '<div id="update">';
    echo "<h3>".$lang[LOCAL_LANG]['new_version']." : v$current_version !</h3>";
    echo '<ul>';
    foreach($description_update as $change) echo "<li>$change;</li>";
    echo '</ul>';
    echo '<a href="index.php?do_update" class="do_update">'.$lang[LOCAL_LANG]['click_here_update'].' !</a> <br />';
    echo '<a href="index.php?ignore_update&number='.$current_version.'" class="do_update">'.$lang[LOCAL_LANG]['ignore_update'].' !</a> <br />';
    echo '</div>';
}

/*
 * Affiche un message après la fin d'une MàJ
 */
function show_update_done()
{
    global $lang;
    echo '<div id="update">';
    echo "<h3>".$lang[LOCAL_LANG]['cakebox_uptodate']." !</h3><br />";
    echo '<a href="last_update.log" class="do_update">'.$lang[LOCAL_LANG]['click_here'].'</a> '.$lang[LOCAL_LANG]['watch_log_update'].'.<br />';
    echo $lang[LOCAL_LANG]['if_question'].', <a href="https://github.com/MardamBeyK/Cakebox/wiki/Impossible-de-mettre-%C3%A0-jour-!" class="do_update">'.$lang[LOCAL_LANG]['ask_it'].' !</a>';
    echo '</div>';
}

/**
  * Fais la mise à jour vers la dernière version disponible
  * @param $force Force la mise à jour si TRUE
  */
function do_update($force)
{
  // We must be sure there is an update available
  if(check_update() || $force)
  {

    // Extract "/dir/of/web/server" from "/dir/of/web/server/cakebox"
    $update_dir = escapeshellarg(substr(getcwd(),0,strpos(getcwd(),"/cakebox")));
    exec("bash scripts/patch_update $update_dir");
    sleep(1); // let time before redirection
    header('Location:index.php?update_done');

  }
}

/**
  * Ignore la mise à jour courante en falsifiant le numéro de version de Cakebox
  * @param $current_version Numéro de la nouvelle version à ignorer
  */
function ignore_update($current_version)
{
  $file = fopen('version.txt', 'r+');
  fputs($file, $current_version);
  fclose($file);
  header('Location:index.php');
}

/**
  * Retourne l'OS de l'utilisateur
  * @return "Linux-Windows-others" | "OSX" 
  */
function detect_OS()
{
  $ua = $_SERVER["HTTP_USER_AGENT"];
  if(strpos($ua, 'Macintosh')) return "OSX";
  else return "Linux-Windows-others";
}

?>

