<?php

/*
---------------------------------
      INSTANCES GLOBALES
---------------------------------
*/

// Configuration par défaut
if (file_exists("config.ini"))
  $config = new Configuration();
else
	die("Il faut générer le fichier de conf ou faire un petit assistant de configuration en un clic.");

/*
---------------------------------
      CLASS CONFIGURATION
---------------------------------
*/

/**
 * Gestion du fichier config.ini de Cakebox
 */
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
  private $background;
  // Update
  private $time_check_update;
  // Video
  private $video_player;
  // Check Errors
  private $error_no_data_dir;
  private $error_chmod_data_dir;
  // Path
  private $cakebox_absolute_path;

  // Parsing du fichier de configuration
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
    $this->background           =   $config_array['General']['background'];
    $this->time_check_update    =   $config_array['Update']['time_check_update'];
    $this->video_player         =   $config_array['Video']['player'];
    $this->cakebox_absolute_path =  str_replace('inc','',dirname(__FILE__)); // Get /var/www/cakebox
    $this->check_dir(); // Vérification des dossiers data & downloads
  }

  /**
    * Accesseur générique
    * @param $attr L'attribut à retourner
    */
  public function get($attr)
  {
    return $this->$attr;
  }

  /*
   * Vérifie la permission des dossiers importants (downloads et data)
   * et remplit les attributs concernés en conséquence.
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

  /**
  * Update le fichier de configuration
  * @param $post Le tableau de champs
  */
  public function update($post)
  {
    $config_content = array(
        // Options générales
        'General' => array(
          'lang'            => $post['lang'],
          'ignore_chmod'    => (isset($post['ignore_chmod'])) ? "true":"false",
          'download_dir'    => $post['download_dir'],
          'download_link'   => 'http://admin:admin@localhost/cakebox/',
          'excluded_files'  => $post['excluded_files'],
          'show_hidden_content' => (isset($post['show_hidden_content'])) ? "true":"false",
          'show_last_add'   => (isset($post['show_last_add'])) ? "true":"false",
          'background'      => 'original.jpg'
          ),

        // Options d'update
        'Update' => array(
          'time_check_update' => $post['update_status']
          ),

        // Options liées à la vidéo
        'Video' => array(
          'player'          => $post['player']
          )
      );

    // Écrit la configuration et redirige l'utilisateur (sert aussi à rafraichir la page)
    if($this->write_ini_file($config_content, "config.ini", TRUE))
      header('Location:index.php?update=ok');
    else
      header('Location:index.php?update=fail');
    
  }

  /**
  * Écrire dans un fichier ini
  * @param $assoc_arr L'array de contenu (array('first' => array('value1'=>...,...), 'second'=>array('value1',...)))
  * @param $path Le fichier ini à écrire
  * @param $has_sections S'il y a des sections dans l'ini
  */
  function write_ini_file($assoc_arr, $path, $has_sections=FALSE) { 
    $content = ""; 
    if ($has_sections) { 
        foreach ($assoc_arr as $key=>$elem) { 
            $content .= "[".$key."]\n"; 
            foreach ($elem as $key2=>$elem2) { 
                if(is_array($elem2)) 
                { 
                    for($i=0;$i<count($elem2);$i++) 
                    { 
                        $content .= $key2."[] = \"".$elem2[$i]."\"\n"; 
                    } 
                } 
                else if($elem2=="") $content .= $key2." = \n"; 
                else $content .= $key2." = \"".$elem2."\"\n"; 
            } 
        } 
    } 
    else { 
        foreach ($assoc_arr as $key=>$elem) { 
            if(is_array($elem)) 
            { 
                for($i=0;$i<count($elem);$i++) 
                { 
                    $content .= $key2."[] = \"".$elem[$i]."\"\n"; 
                } 
            } 
            else if($elem=="") $content .= $key2." = \n"; 
            else $content .= $key2." = \"".$elem."\"\n"; 
        } 
    } 

    if (!$handle = fopen($path, 'w')) { 
        return false; 
    } 
    if (!fwrite($handle, $content)) { 
        return false; 
    } 
    fclose($handle); 
    return true; 
}

  /**
    * Accesseur de la variable d'erreur NO_DATA_DIR
    */
  public function get_error_no_data_dir()
  {
    return $this->error_no_data_dir;
  }

  /**
    * Accesseur de la variable d'erreur CHMOD_DATA_DIR
    */
  public function get_error_chmod_data_dir()
  {
    return $this->error_chmod_data_dir;
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
// OBSOLÈTE, NON UTILISÉE, A RÉIMPLEMETER PROPREMENT
function showLastAdd($file)
{
  if (LAST_ADD)
    if (((date('U') - filemtime($file)) / 3600) <= TIME_LAST_ADD)
      echo '<img src="ressources/new.png" title="Nouveau fichier !" /> &nbsp;';
}
// OBSOLÈTE, NON UTILISÉE, A RÉIMPLEMETER PROPREMENT
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
// A REIMPLEMENTER PROPREMENT
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

 
?>