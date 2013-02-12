<?php

/*
---------------------------------
      INSTANCES GLOBALES
---------------------------------
*/

// Nothing.

/*
---------------------------------
      CLASS File
---------------------------------
*/

/**
 * Gestion des fichiers sur watch.php
 */

abstract class File
{
  protected $fullname;
  protected $name;
  protected $dirname;
  protected $type;
  protected $url;

  function __construct($fullname)
  {
    global $config; // Load conf
    $this->fullname = $fullname;
    $this->name = basename($fullname);
    $this->dirname = dirname($fullname);
    $this->type = pathinfo($fullname, PATHINFO_EXTENSION);
    $this->url = $config->get('download_link').$fullname;
  }

  /**
   * Accesseur du nom
   */
  public function get_name()
  {
    return $this->name;
  }
  
  /**
   * Accesseur du nom complet
   */
  public function get_fullname()
  {
    return $this->fullname;
  }

  /**
   * Accesseur de l'URL
   */
    public function get_url()
  {
    return $this->url;
  }

   /**
   * Retourne le format d'un fichier 
   * @filename Le nom du fichier à considérer
   * @return "video", "pdf", "music", "iso", "archive"
   */
  public static function get_type($fullname)
  {
    $extension = pathinfo($fullname, PATHINFO_EXTENSION);

    if($extension == "avi" || $extension == "mpeg" || $extension == "mp4" || $extension == "mkv") $type = "video";
    else if($extension == "mp3" || $extension == "midi" || $extension == "m4a" || $extension == "ogg" || $extension == "flac") $type = "music";
    else if($extension == "rar" || $extension == "zip") $type = "archive";
    else if($extension == "iso") $type = "iso";
    else $type = "other";

    return $type;
  }

  /**
   * Fichier video ou non ?
   * @param $fullname Le fichier à traiter
   * @return true, false
   */
  public static function isVideo($fullname)
  {
    return (File::get_type($fullname) == "video");
  }

  /**
   * Convertit la taille en Xo
   * @param $fullname Le fichier a traiter
   */
  static function get_file_size($fullname)
  {
     $fs = filesize($fullname);

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
   * Retourne la date de dernière modification d'un fichier
   * @param $fullname Le fichier a traiter
   */
  static function get_file_mtime($fullname)
  {
     return date("d F Y, H:i",filemtime($fullname));
  }

  /**
   * Retourne la date de dernier access d'un fichier
   * @param $fullname Le fichier a traiter
   */
  static function get_file_atime($fullname)
  {
     return date("d F Y, H:i",fileatime($fullname));
  }
}


/*
---------------------------------
      CLASS Video < File
---------------------------------
*/

/**
 * Gestion des fichiers videos
 */

class Video extends File
{
  private $seen;
  private $next_video;
  private $prev_video;

  function __construct($fullname)
  {
    parent::__construct($fullname);
    $this->seen = false; // TODO
    $this->find_next_prev_video();
  }

  //Accesseur des NEXT et PREV
  public function get_next() { return $this->next_video; }
  public function get_prev() { return $this->prev_video; }
  // Accesseur de SEEN (vidéo vue ou non vue)
  public function get_seen() { return $this->seen; }


  /**
   * Récupère les épisodes prec et suivant
   * Remplit les attributs $next et $prev
   */
  private function find_next_prev_video()
  {

    // Initialisation
    $this->prev = NULL;
    $this->next = NULL;

    // On récupère le contenu du repertoire courant
    $local_tree = new FileTree($this->dirname, TRUE);
    $current_dir = $local_tree->get_tree();

    // On récupère la position du fichier courant (pour voir avant et après)
    $current_file = array_keys($current_dir,$this->fullname);
    $current_file = $current_file[0];

    // Si le fichier courant n'est pas le dernier, on a notre $next
    if($current_file != count($current_dir)-1)
    {
        // Si le fichier suivant est bien une vidéo
        if(File::get_type($current_dir[$current_file+1]) == "video")
          $this->next = htmlspecialchars(urlencode($current_dir[$current_file+1]));
    }

    // Si le fichier courant n'est pas le premier, on a notre prev
    if($current_file != 0)
    {
        // Si le fichier précédent est bien une vidéo
        if(File::get_type($current_dir[$current_file-1]) == "video")
          $this->prev = htmlspecialchars(urlencode($current_dir[$current_file-1]));
    }
  }
}

?>