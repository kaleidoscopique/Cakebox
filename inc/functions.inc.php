<?php

// Nombre d'heures entre chaque vérif de mise à jour (défaut : 12)
// Mettez 0 pour vérifier à chacune de vos visites
define('TIME_CHECK_UPDATE', 12); 

/*
  *** NE MODIFIEZ RIEN A PARTIR D'ICI ***
  *** DO NOT MODIFY ANYTHING FROM HERE ***
*/
define('LOCAL_DL_PATH','downloads');

/**
 * Retourne l'extention CAKEBOX d'un fichier en fonction de son type
 * ou le chemin vers l'icone associé
 * @filename Le nom du fichier à considérer
 */
function get_file_icon($filename,$short_return=FALSE)
{
  $extension = pathinfo($filename, PATHINFO_EXTENSION);

  if($extension == "avi" || $extension == "mpeg" || $extension == "mp4" || $extension == "mkv") $extension = "avi";
  else if($extension == "mp3" || $extension == "midi" || $extension == "m4a" || $extension == "ogg" || $extension == "flac") $extension = "mp3";
  else if($extension == "iso" || $extension == "rar" || $extension == "zip") $extension = "iso";
  else $extension = "other";

  if($short_return) return $extension;
  else return "ressources/ext/".$extension.".png";
}

/**
 * Convertit la taille en Xo
 * @param $fs La taille à convertir
 */
function convert_size($fs)
{
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
            if ($file == "." || $file == ".." || $file == ".htaccess") {
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
 */
function print_tree_structure($treestructure,$filter="all",$editmode=FALSE)
{
  global $lang;

  foreach($treestructure as $key => $file)
  {
    if(is_array($file))
    {
      $key = addslashes(basename($key));
      echo '<div class="onedir">';
      if($editmode) echo '<input name="Files[]" id="Files" type="checkbox" value="'.htmlspecialchars($key).'" onclick="CheckLikes(this);"/>';
      echo '<img src="ressources/folder.png" onclick="showhidedir(\''.$key.'\');return false;" class="pointerLink imgfolder" />
      <span class="pointerLink" onclick="showhidedir(\''.$key.'\');return false;">'.$key.'</span></div>';
      echo '<div id="'.$key.'" class="dirInList" style="display:none;">';
      print_tree_structure($file,$filter,$editmode);
      echo '</div>';
    }
    else
    {
      // Afficher tous les fichiers ou seulement les vidéos
      if($filter == "all" ||
         ($filter == "videos" && get_file_icon(basename($file),TRUE) == "avi"))
      {
        echo '<div style="margin-bottom:5px;" class="onefile" id="div-'.htmlspecialchars($file).'">';

          // La checkbox de l'editmode
          if($editmode) echo '<input name="Files[]" id="Files" type="checkbox" value="'.htmlspecialchars($file).'"/>';         

          // Affichage de l'image à gauche du titre
          $current = htmlspecialchars(urlencode($file));
          echo '<a href="watch.php?file='.$current.'">';
            echo '<img src="'.get_file_icon($file).'" title="Stream or download this file" /> &nbsp;';
          echo '</a>';

          // Affichage du titre (soulignement si marqué comme vu)
          if(file_exists("data/".basename($file))) echo '<span style="border-bottom:2px dotted #76D6B7;">';
            echo basename(htmlspecialchars($file));
          if(file_exists("data/".basename($file))) echo '</span>' ;

          // Création de l'infobulle
          echo '<a href="#" class="update_info">';
          echo ' (?)
                <span class="tooltip">
                  <span></span>
                  '.$lang[LOCAL_LANG]['size'].' : '.convert_size(filesize($file)).'<br/>
                  '.$lang[LOCAL_LANG]['last_update'].' : '.date("d F Y, H:i",filemtime($file)).'<br/>
                  '.$lang[LOCAL_LANG]['last_access'].' : '.date("d F Y, H:i",fileatime($file)).'<br/>
              </span> ';
          echo '</a>';

          echo '</div>';
        }
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
  else
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
      if(get_file_icon(basename($current_dir[$current_file+1]),true) == "avi") 
        $next = htmlspecialchars(urlencode($current_dir[$current_file+1]));
  }

  // Si le fichier courant n'est pas le premier, on a notre prev
  $prev = NULL;
  if($current_file != 0) 
  {
      // Si le fichier précédent est bien une vidéo
      if(get_file_icon(basename($current_dir[$current_file-1]),true) == "avi") 
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
    echo '<a href="index.php?do_update" class="do_update">'.$lang[LOCAL_LANG]['click_here_update'].' !</a>';
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
    exec("bash patch_maj $update_dir",$output);
    header('index.php?update_done');

  }
}

?>

