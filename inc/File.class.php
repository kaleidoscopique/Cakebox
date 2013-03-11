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

class File
{
  protected $fullname;
  protected $name;
  protected $dirname;
  protected $type;
  protected $url;
  protected $sticky;
  protected $watch_include;

  function __construct($fullname)
  {
    global $config; // Load conf
    $this->fullname   =   $fullname;
    $this->name       =   basename($fullname);
    $this->dirname    =   dirname($fullname);
    $this->type       =   $this->get_type();
    $this->url        =   $config->get('download_link').$fullname;
    $this->sticky     =   false;
    $this->watch_include  =   "watch_default";

    if($this->type == "pdf") $this->watch_include = "watch_pdf";
    if($this->type == "video") $this->watch_include = "watch_video";

  }

  /**
   * Accesseurs
   */
  public function __get($name)
  {
    return $this->$name;
  }

   /**
   * Retourne le format d'un fichier 
   * @filename Le nom du fichier à considérer
   * @return "video", "pdf", "music", "iso", "archive"
   */
  public function get_type() { return File::get_file_type($this->fullname); }
  static function get_file_type($fullname=NULL)
  {
    $extension = pathinfo($fullname, PATHINFO_EXTENSION);

    if($extension == "avi" || $extension == "mpeg" || $extension == "mp4" || $extension == "mkv") $type = "video";
    else if($extension == "mp3" || $extension == "midi" || $extension == "m4a" || $extension == "ogg" || $extension == "flac") $type = "music";
    else if($extension == "rar" || $extension == "zip") $type = "archive";
    else if($extension == "iso") $type = "iso";
    else if($extension == "pdf") $type = "pdf";
    else $type = "other";

    return $type;
  }


  /**
   * Convertit la taille en Xo
   * @param $fullname Le fichier a traiter
   */
  public function get_size() { return File::get_file_size($this->fullname); }
  static function get_file_size($fullname=NULL)
  {
    if($fullname == NULL) $fullname = $this->fullname;
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
  public function get_mtime() { return File::get_file_mtime($this->fullname); }
  static function get_file_mtime($fullname)
  {
     return date("d F Y, H:i",filemtime($fullname));
  }

  /**
   * Retourne la date de dernier access d'un fichier
   * @param $fullname Le fichier a traiter
   */
  public function get_atime() { return File::get_file_atime($this->fullname); }
  static function get_file_atime($fullname)
  {
     return date("d F Y, H:i",fileatime($fullname));
  }
}