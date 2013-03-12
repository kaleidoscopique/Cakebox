<?php

/*
---------------------------------
      INSTANCES GLOBALES
---------------------------------
*/

// Nothing.

/*
---------------------------------
      CLASS FileTree
---------------------------------
*/

/**
 * Gestion de l'arborescence des dossiers
 */
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
   * @param $exclude_dir Permet d'exclure les dossiers (pour n'avoir que les fichiers de $directory)
   **/
  private function generate_tree($directory = null, $exclude_dir = FALSE)
  {
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
            if (in_array($file, explode(',',$this->config->excluded_files)) || ($file[0] == '.' && $this->config->show_hidden_content))
              continue;

            // Si on pointe sur un fichier, on l'ajoute
            if (!is_dir($directory."/".$file))
              $return[] = $directory."/".$file;

            // Si on pointe sur un dossier et qu'on veut bien des dossiers, on l'ajoute (si dossier, on ajoute "array()")
            else if (!$exclude_dir)
              $return[$directory."/".$file] = $this->generate_tree($directory."/".$file); 
        }
    }
    // Si on pointe sur un fichier, on l'ajoute
    else
        $return[] = $directory;

    // Fin, retour
    return $return;
  }


  /**
    * Affichage de l'arborescence
    * @param $directory Le répertoire (fullname) à afficher
    */
  public function print_tree($subtree = NULL)
  {

    // Global var
    global $lang;
    global $config;

    // On utilise le tree total au premier appel récursif
    if($subtree == NULL) $subtree = $this->tree;

    // Pour chaque élément de l'arboresence
    foreach($subtree as $fullname => $file)
    {
      // Si on pointe un dossier ($fullname = "download/my_dir")
      if(is_array($file))
        $this->print_folder($fullname,$file);
      // Si on pointe sur un fichier
      else
        $this->print_file($file);
        
    }
  }
    
  /**
  * Affichage d'un dossier avec son arborescence
  * @param $fullname Le dossier à afficher
  * @param $subtree L'array associé au dossier $fullname (donc son contenu)
  */
  private function print_folder($fullname,$subtree)
  {
    global $lang;
    global $config;

    // Récupère le nom simple du dossier (sans les parents)
    $name = basename($fullname);   

    // Affiche le dossier et son arborescence
    echo '<div class="onedir toRoll" data-path="'.$fullname.'">
          <img src="ressources/folder.png" class="pointerLink imgfolder"/>
          <span class="pointerLink">'.stripslashes($name).'</span>
          </div>
          
          <div id="'.stripslashes($name).'" class="dirInList">';
          // Le contenu sera généré par Ajax (ajax.php)
          echo '</div>';
  }

  /**
  * Affichage d'un fichier
  * @param $fullname Son nom
  */
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

      echo '<a href="'.$this->config->download_link.$fullname.'" download="'.$this->config->download_link.$fullname.'">';
        echo '<img src="ressources/download.png" title="Download this file" /> &nbsp;';
      echo '</a>';

      echo '<a href="watch.php?file='.urlencode($fullname).'">';
        echo '<img src="ressources/extensions/'.File::get_file_type($fullname).'.png" title="Stream or download this file" /> &nbsp;';
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
              '.$lang[$this->config->lang]['size'].' : '.File::get_file_size($fullname).'<br/>
              '.$lang[$this->config->lang]['last_update'].' : '.File::get_file_mtime($fullname).'<br/>
              '.$lang[$this->config->lang]['last_access'].' : '.File::get_file_atime($fullname).'<br/>
            </span>
            </a>';

      // Fin
      echo '</div>';
  }
}

?>