<?php

/**
 * Return the path of the icon file
 * @filename Name of the file to considerate
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
 * Convert size with readable units
 * @param $fs The size
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
 * List content of  $directory
 * Return the list of the files (array)
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
 * Show the tree structure file
 * $filter = all / video
 */
function print_tree_structure($treestructure,$filter="all",$editmode=FALSE)
{
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
                  Size : '.convert_size(filesize($file)).'<br/>
                  Last update : '.date("d F Y, H:i",filemtime($file)).'<br/>
                  Last access : '.date("d F Y, H:i",fileatime($file)).'<br/>
              </span> ';
          echo '</a>';

          echo '</div>';
        }
    }
  }
}


/**
 * rmdir function for not empty dir
 * @param $dir The dir to delete
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
*  Function ustr_replace for "unique str_replace"
*  Replace a string only once in a string
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
 * Check the permissions of data and downloads
 */
function check_dir()
{
 
  $isdir_data = is_dir("data");
  $isdir_downloads = is_dir("downloads");
  if(!$isdir_data || !$isdir_downloads)
  {
    echo '<p style="background:#FF6B7A;padding:10px;color:#FFFFFF;margin-bottom:20px;">';
    echo '<span style="font-weight:bold;">WARNING</span><br/>';
    if(!$isdir_data) echo "You must create a folder named \"data\" in the folder of Cakebox<br/>";
    if(!$isdir_downloads) echo "You must create a folder named \"downloads\" in the folder of Cakebox<br/>";
    echo '</p>';
  }
  else
  {
    $chmod_data = substr(sprintf('%o', fileperms('data')),-3);
    $chmod_downloads = substr(sprintf('%o',fileperms('downloads')),-3);
    if($chmod_data != 777 || $chmod_downloads != 777)
    {
      echo '<p style="background:#FF6B7A;padding:10px;color:#FFFFFF;margin-bottom:20px;">';
      echo '<span style="font-weight:bold;">WARNING</span><br/>';
      if($chmod_data != 777) echo "You must change the permissions of \"/data\" to 777 (chmod).<br/>";
      if($chmod_downloads != 777) echo "You must change the permissions of \"/downloads\" to 777 (chmod).<br/>";
      echo '</p>';
    }
  }
}


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

?>

