<?php

// Configuration par défaut
if (file_exists("config.ini"))
  $config = new Configuration();
else
	die("Il faut créer le fichier de configuration !! @@assistant");


class Configuration
{
  // General
  private $lang;
  private $ignore_chmod;
  private $download_dir;
  private $download_link;
  private $excluded_files;
  private $show_hidden_content;
  private $show_last_add;
  // Update
  private $time_check_update;
  // Video
  private $video_player;
  // Check Errors
  private $error_no_data_dir;
  private $error_chmod_data_dir;

  function __construct()
  {
    $config_array               =   parse_ini_file("config.ini", true);
    $this->lang                 =   $config_array['General']['lang'];
    $this->ignore_chmod         =   $config_array['General']['ignore_chmod'];
    $this->download_dir         =   $config_array['General']['download_dir'];
    $this->download_link        =   $config_array['General']['download_link'];
    $this->excluded_files       =   $config_array['General']['excluded_files'];
    $this->show_hidden_content  =   $config_array['General']['show_hidden_content'];
    $this->show_last_add        =   $config_array['General']['show_last_add'];
    $this->time_check_update    =   $config_array['Update']['time_check_update'];
    $this->video_player         =   $config_array['Video']['player'];
    $this->check_dir(); // Vérification des dossiers data & downloads
  }

  public function get($attr)
  {
    return $this->$attr;
  }

  /*
   * Vérifie la permission des dossiers importants (downloads et data)
   * et affiche une erreur en cas de besoin
   */
  private function check_dir()
  {
    // Vérification des dossiers
    if(!is_dir("data") || !is_dir("downloads"))
    {
      $this->error_no_data_dir = true;
    }
    // Vérification des CHMOD
    else if(!$this->ignore_chmod)
    {
      $chmod_data = substr(sprintf('%o', fileperms('data')),-3);
      $chmod_downloads = substr(sprintf('%o',fileperms('downloads')),-3);
      if($chmod_data != 777 || $chmod_downloads != 777)
      {
        $this->error_chmod_data_dir = true;
      }
    }
  }

  public function get_error_no_data_dir()
  {
    return $this->error_no_data_dir;
  }

  public function get_error_chmod_data_dir()
  {
    return $this->error_chmod_data_dir;
  }
}


class FileTree
{

  private $tree;
  private $config;

  function __construct($path, $exclude_dir = FALSE)
  {
    global $config;
    $this->config = $config;
    $this->tree = $this->generate_tree($path, $exclude_dir);
  }

  /**
  * Getter de l'arboresence 
  * @return Array
  */
  public function get_tree()
  {
    return $this->tree;
  }

  /**
   * Récupère récursivement le contenu d'un répertoire
   * et le retourne sous forme d'array
   * @param $directory Le répertoire à traiter
   * @param $exclude_dir Permet d'exclure les dossiers (aka n'avoir que les fichiers de $directory)
   **/
  private function generate_tree($directory = null, $exclude_dir = FALSE)
  {
    // Global var 
    global $excludeFiles;

    // Si on ne spécifie pas de $directory, on prend le courant
    if ($directory == null) $directory = getcwd();

    // Le array final, à retourner
    $return = array();

    // Verifie si on pointe un dossier
    if (is_dir($directory)) 
    {
        // Récupère le contenu du dossier
        foreach(scandir($directory) as $file) {

            // Ignore les fichiers cachés si configuré, ignore les fichiers exclus
            if (in_array($file, $this->config->get('excluded_files')) || $this->config->get('show_hidden_content')) 
              continue;

            // Si on pointe sur un fichier, on l'ajoute
            if (!is_dir($directory."/".$file))
              $return[] = $directory."/".$file;

            // Si on pointe sur un dossier et qu'on veut bien des dossiers, on l'ajoute (si dossier, on ajoute "array()")
            else if (!$exclude_dir)
              $return[$directory."/".$file] = (!empty($subtree)) ? $this->get_tree($directory."/".$file) : array();
        }
    }
    // Si on pointe sur un fichier, on l'ajoute
    else
        $return[] = $directory;

    // Fin, retour
    return $return;
  }


  public function print_tree($directory = '')
  {

    // Global var
    global $lang;

    if (empty($this->tree))
    {
      echo '<div style="margin-bottom:5px;" class="onefile">';
      echo $lang[$config->get('lang')]['empty_dir'];
      echo '</div>';
      return;
    }

    // Pour chaque élément de l'arboresence
    foreach($this->tree as $fullname => $file)
    {
      // Si on pointe un dossier ($fullname = "download/my_dir")
      if(is_array($file))
        $this->print_folder($fullname);
      // Si on pointe sur un fichier
      else
        $this->print_file($file);
        
    }
  }
    

  private function print_folder($fullname)
  {
    // Récupère le nom simple du dossier (sans les parents)
    $name = addslashes(basename($fullname));   

    // Affiche le dossier et son arborescence
    echo '<div class="onedir">
          <img src="ressources/folder.png" class="pointerLink imgfolder"/>
          <span class="pointerLink">'.stripslashes($name).'</span>
          </div>
          
          <div id="'.stripslashes($name).'" class="dirInList">';
          // style="display:none;"
          if(!empty($tree[$fullname])) $this->print_tree($fullname);
          else echo "Dossier vide";
          echo '</div>';
  }

  private function print_file($fullname)
  {

      // Global Var
      global $lang;

      // Traitement du paramètre
      $path_info = pathinfo($fullname);
      $name = basename($fullname);
      $digest_fullname = str_replace("/", "-", htmlspecialchars($fullname));
      $protected_name = htmlspecialchars($name);

      // Affichage des icones à gauche
      echo '<div style="margin-bottom:5px;" class="onefile" id="file-'.$digest_fullname.'">';

      echo '<a href="'.$this->config->get('download_link').$fullname.'" download="'.$this->config->get('download_link').$fullname.'">';
        echo '<img src="ressources/download.png" title="Download this file" /> &nbsp;';
      echo '</a>';

      echo '<a href="watch.php?file='.urlencode($fullname).'">';
        echo '<img src="ressources/ext/'.File::get_type($fullname).'.png" title="Stream or download this file" /> &nbsp;';
      echo '</a>';

      // Affichage du titre du fichier (soulignement si marqué comme vu)
      if (file_exists("data/".$path_info['basename']))
      {
        echo '<span style="border-bottom:2px dotted #76D6B7;">';
        echo $protected_name;
        echo '</span>';
      }
      else echo $protected_name;

      // Création de l'infobulle
      echo '<a href="#" class="tooltip">&nbsp;(?)
            <span>
              '.$lang[$this->config->get('lang')]['size'].' : '.File::get_file_size($fullname).'<br/>
              '.$lang[$this->config->get('lang')]['last_update'].' : '.File::get_file_mtime($fullname).'<br/>
              '.$lang[$this->config->get('lang')]['last_access'].' : '.File::get_file_atime($fullname).'<br/>
            </span>
            </a>';

      // Fin
      echo '</div>';
  }
}


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

  public function get_name()
  {
    return $this->name;
  }
    
  public function get_fullname()
  {
    return $this->fullname;
  }
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
    else $extension = "other";

    return $type;
  }

  public static function isVideo($fullname)
  {
    return (File::get_type($fullname) == "video");
  }

  /**
   * Convertit la taille en Xo
   * @param $filePath Le fichier a traiter
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
   * @param $filePath Le fichier a traiter
   */
  static function get_file_mtime($fullname)
  {
     return date("d F Y, H:i",filemtime($fullname));
  }

  /**
   * Retourne la date de dernier access d'un fichier
   * @param $filePath Le fichier a traiter
   */
  static function get_file_atime($fullname)
  {
     return date("d F Y, H:i",fileatime($fullname));
  }
}

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

  public function print_player()
  {
    if($this->player == "vlc")
    {

    }
    elseif($this->player == "divxplayer")
    {

    }
  }

  public function get_seen()
  {
    return $this->seen;
  }

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

  public function get_next()
  {
    return $this->next_video;
  }

  public function get_prev()
  {
    return $this->prev_video;
  }

}

/********************************/
/*          FONCTIONS           */
/********************************/

/**
 * Affiche l'icone NEW si
 * le fichier a été ajouté il y moins de
 * X heures (variable TIME_LAST_ADD)
 **/
function showLastAdd($file)
{
  if (LAST_ADD)
    if (((date('U') - filemtime($file)) / 3600) <= TIME_LAST_ADD)
      echo '<img src="ressources/new.png" title="Nouveau fichier !" /> &nbsp;';
}

function showLastAddFolder($key)
{
  $stat = stat($key);
  if (LAST_ADD || ((date('U') - $stat['mtime']) / 3600) <= TIME_LAST_ADD)
    return 'folder_new.png';
  else
    return 'folder.png';
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
    echo "<h3>".$lang[$config->get('lang')]['new_version']." : v$current_version !</h3>";
    echo '<ul>';
    foreach($description_update as $change) echo "<li>$change;</li>";
    echo '</ul>';
    echo '<a href="index.php?do_update" class="do_update">'.$lang[$config->get('lang')]['click_here_update'].' !</a> <br />';
    echo '<a href="index.php?ignore_update&number='.$current_version.'" class="do_update">'.$lang[$config->get('lang')]['ignore_update'].' !</a> <br />';
    echo '</div>';
}

/*
 * Affiche un message après la fin d'une MàJ
 */
function show_update_done()
{
    global $lang;
    echo '<div id="update">';
    echo "<h3>".$lang[$config->get('lang')]['cakebox_uptodate']." !</h3><br />";
    echo '<a href="last_update.log" class="do_update">'.$lang[$config->get('lang')]['click_here'].'</a> '.$lang[$config->get('lang')]['watch_log_update'].'.<br />';
    echo $lang[$config->get('lang')]['if_question'].', <a href="https://github.com/MardamBeyK/Cakebox/wiki/Impossible-de-mettre-%C3%A0-jour-!" class="do_update">'.$lang[$config->get('lang')]['ask_it'].' !</a>';
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