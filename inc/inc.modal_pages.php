<!-- MODAL PANEL -->
<div id="config_panel" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Panneau de configuration</h3>
  </div>

<form name="config" class="form-horizontal" action="" method="post">
  <div class="modal-body">

    <ul class="nav nav-tabs" id="myTab">
      <li class="active"><a href="#general">General</a></li>
      <li><a href="#multimedia">Multimédia</a></li>
      <li><a href="#background">Image de fond</a></li>
    </ul>
  
      <div class="tab-content">
        <div class="tab-pane active" id="general">
          <fieldset>
          <div class="control-group">
            <!-- Select Basic -->
            <label class="control-label">Langue</label>
            <div class="controls">
              <select class="input-xlarge" name="lang">
                <option value="fr" <?php if($config->is_lang_french()) echo 'selected'; ?>>Français</option>
                <option value="en" <?php if($config->is_lang_english()) echo 'selected'; ?>>Anglais</option>
              </select>
            </div>
          </div>

          <div class="control-group">
              <label class="control-label">Affichage</label>
              <div class="controls">
                <!-- Multiple Checkboxes -->
                <label class="checkbox">
                  <input type="checkbox" value="true" name="show_hidden_content" <?php if($config->is_show_hidden()) echo 'checked'; ?>>
                  Afficher les fichiers et dossiers cachés
                </label>
                <label class="checkbox">
                  <input type="checkbox" value="true" name="show_last_add" <?php if($config->is_show_last_add()) echo 'checked'; ?>>
                  Mettre en évidence le contenu fraîchement téléchargé
                </label>
                <label class="checkbox">
                  <input type="checkbox" value="true" name="ignore_chmod" <?php if($config->is_ignore_chmod()) echo 'checked'; ?>>
                  Ignorer les erreurs de chmod sur l'accueil
                </label>
              </div>
          </div>

          <div class="control-group">
            <!-- Text input-->
            <label class="control-label" for="input01">Téléchargements</label>
            <div class="controls">
              <input type="text" class="input-xlarge" name="download_dir" value="<?php echo $config->download_dir; ?>">
              <span class="help-block">Indiquez le dossier où se trouve vos fichiers téléchargés. Par défaut "download".</span>
            </div>
          </div>

          <div class="control-group">
            <!-- Text input-->
            <label class="control-label" for="input01">Fichiers à exclure</label>
            <div class="controls">
              <input type="text" placeholder="" class="input-xlarge" name="excluded_files" value="<?php echo $config->excluded_files; ?>">
              <span class="help-block">Listez les fichiers que vous souhaitez exclure du listing, en les séparant par une virgule.</span>
            </div>
          </div>


          <div class="control-group">
            <label class="control-label">Les mises à jour</label>
            <div class="controls">
                <!-- Multiple Radios -->
                <label class="radio">
                  <input name="update_status" type="radio" value="12" name="update" <?php if($config->is_update_enabled()) echo 'checked'; ?>>
                  Vérifier automatiquement de temps en temps
                </label>
                <label class="radio">
                  <input name="update_status" type="radio" value="-1" name="update" <?php if($config->is_update_disabled()) echo 'checked'; ?>>
                  Ne jamais vérifier
                </label>
                <label class="radio">
                  <input name="update_status" type="radio" value="0" name="update" <?php if($config->is_update_eachtime()) echo 'checked'; ?>>
                  Vérifier à chaque visite
                </label>
            </div>
          </div>
        </fieldset>
        </div>

        <div class="tab-pane" id="multimedia">
          <div class="control-group">
            <!-- Select Basic -->
            <label class="control-label">Player par défaut</label>
            <div class="controls">
              <select class="input-xlarge" name="player">
                <option value="vlc" <?php if($config->is_player_vlc()) echo 'selected'; ?>>VLC</option>
                <option value="divxwebplayer" <?php if($config->is_player_divxwebplayer()) echo 'selected'; ?>>DivX Web Player</option>
              </select>
            </div>
          </div>
        </div>

        <div class="tab-pane" id="background">
          
          <!-- Radiobox invisible, séléction sur l'image uniquement, voir header.php <script> -->
          <?php foreach($config->backgrounds_list as $num => $background_file)
          {
            ?>
              <input type="radio" value="<?php echo $num; ?>" name="background" id="background_<?php echo $num; ?>" class="hidden_radio" <?php if($config->is_thisbackground_selected($background_file)) echo 'checked'; ?>/>
              <a id="linkbackground_<?php echo $num; ?>" href="javascript:set_radio('<?php echo $num; ?>');" class="radio-picture" 
                style="background:url(ressources/resize_picture.php?src=ressources/backgrounds/<?php echo $background_file; ?>&w=140&h=140) no-repeat scroll 0 0 white;">
                  &nbsp;
              </a>
            <?
          }
          ?>

        </div>
        <!--<div class="tab-pane" id="background">...</div>-->

        </div>
        <input type="hidden" name="submit" value="1" />
      
  </div>
  <div class="modal-footer">
    <input type="hidden" value="<?php echo $_SERVER['PHP_SELF']; ?>" name="php_self" />
    <input type="hidden" value="<?php echo (isset($_GET['file'])) ? $_GET['file']:''; ?>" name="get_file" />
    <button class="btn" data-dismiss="modal" aria-hidden="true">Annuler</button>
    <input type="submit" name="submit" value="Enregistrer" class="btn btn-primary"/>
  </div>
</div>
</form>

<!-- MODAL ABOUT US -->

<div id="about_us" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">A propos de nous</h3>
  </div>
  <div class="modal-body">

  <p>Cakebox est développé par <strong>MardamBey</strong> & <strong>Tuxity</strong>, depuis 2012.</p>
  <p>
    <strong>Site officiel :</strong> <a href="http://www.heycakebox.com">heycakebox.com</a> <br />
    <strong>Dépot Github :</strong> <a href="https://github.com/MardamBeyK/Cakebox">www.github.com/MardamBeyK/Cakebox</a><br />
    Remarques, suggestions, propositions : <a href="mailto:iam.mardambey@gmail.com">iam.mardambey@gmail.com</a>
  </p>  

  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Fermer</button>
  </div>
</div>


<!-- MODAL UPDATE -->

<div id="howto_update" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Comment mettre à jour Cakebox ?</h3>
  </div>
  <div class="modal-body">
<p>Connectez-vous en SSH sur votre serveur et placez-vous à la racine de votre serveur web, là où se trouve le dossier de Cakebox : </p>
    <div class="terminal">
      <span class="prompt">$</span> cd /var/www
    </div>
    <p>Il ne vous reste qu'à taper cette commande pour lancer la mise à jour :</p>
    <div class="terminal">
      <span class="prompt">$</span> wget http://www.github.com/MardambeyK/cakebox/raw/scrips/update.sh && chmod +x update.sh && ./update.sh
    </div>
    <p>C'est fini ! Cakebox est à jour. Bon stream.</p>
      </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Fermer</button>
  </div>
</div>