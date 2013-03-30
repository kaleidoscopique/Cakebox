<?php

/*
---------------------------------
      INSTANCES GLOBALES
---------------------------------
*/

// Check d'un update
$update = new Update();

/*
---------------------------------
      CLASS Update
---------------------------------
*/

/**
 * Gestion des mises à jour
 */

class Update
{
  private $config;
  private $update_available;
  private $local_version;
  private $current_version; // Dernière version du repos Cakebox
  private $changelog; // Si mise à jour

  function __construct()
  {
    global $config;
    $this->config = $config;
    if($this->config->time_check_update >= 0) $this->check();
  }

  // Accesseur update dispo ?
  public function is_update_available() { return $this->update_available; }
  public function get_local_version() { return $this->local_version; }
  public function get_current_version() { return $this->current_version; }
  public function get_changelog() { return $this->changelog; }

  /*
   * Verifie si une mise à jour est disponible
   * Remplit les attributs
   */
  private function check()
  {
    // Time
    $last_check = fileatime('version.txt');
    $time_since = time()-$last_check;

    // Local version
    $local_version_file = fopen('version.txt','r+');
    $this->local_version  = fgets($local_version_file);
    $force_check_update = strpos($this->local_version,"+"); // Si on trouve un "+" à la fin, on force le checkupdate
    $this->local_version = trim(str_replace('+','',$this->local_version)); // On enlève le '+' (et le linefeed) pour ne garder que le nombre

    // Check for a new version each 12h
    if($time_since > $this->config->time_check_update * 3600 || $force_check_update)
    {
      // Version disponible en dépôt
      $current_version_file = fopen('https://raw.github.com/MardamBeyK/Cakebox/v3-dev/version.txt','r');
      $this->current_version  = fgets($current_version_file);

      // Si mise à jour dispo
      if(floatval($this->local_version) < floatval($this->current_version))
      {
        // Flags
        $this->update_available = true;

        // Lecture du fichier
        while(!feof($current_version_file))
          $this->changelog[] = fgets($current_version_file);

        // On rajoute un "+" à côté du num de version en local
        // pour forcer la proposition de mise à jour par la suite
        if(!$force_check_update)
        {
            rewind($local_version_file);
            fwrite($local_version_file,$this->local_version."+\n");
        }
      }

      // Fermeture des flux
      fclose($current_version_file);
      fclose($local_version_file);
    } 
    else 
    {
      $this->update_available = false;
    }
  }

  /*
   * Affiche un message après la fin d'une MàJ
   */
  public function show_update_done()
  {
      global $lang;
      echo '<div id="update">';
      echo "<h3>".$lang[$this->config->lang
      ]['cakebox_uptodate']." (v".$this->local_version.") !</h3><br />";
      echo '<a href="last_update.log" class="do_update">'.$lang[$this->config->lang]['click_here'].'</a> '.$lang[$this->config->lang]['watch_log_update'].'.<br />';
      echo $lang[$this->config->lang]['if_question'].', <a href="https://github.com/MardamBeyK/Cakebox/wiki/Impossible-de-mettre-%C3%A0-jour-!" class="do_update">'.$lang[$this->config->lang]['ask_it'].' !</a>';
      echo '</div>';
  }

  /**
  * Ignore la mise à jour courante en falsifiant le numéro de version de Cakebox
  * @param $current_version Numéro de la nouvelle version à ignorer
  */
  public function ignore()
  {
    $file = fopen('version.txt', 'r+');
    fputs($file, $this->current_version);
    fclose($file);
    header('Location:index.php');
  }
}

?>