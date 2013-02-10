<div id="config_panel" class="modal_window">
  <h1>Panneau de configuration</h1>

  <ul class="nav nav-tabs" id="myTab">
    <li class="active"><a href="#general">General</a></li>
    <li><a href="#multimedia">Multimédia</a></li>
    <li><a href="#background">Image de fond</a></li>
  </ul>
 
<form class="form-horizontal">
  <div class="tab-content">
    <div class="tab-pane active" id="general">

      
      <fieldset>
      <div class="control-group">
        <!-- Select Basic -->
        <label class="control-label">Langue</label>
        <div class="controls">
          <select class="input-xlarge">
            <option>Français</option>
            <option>Anglais</option>
          </select>
        </div>
      </div>

      <div class="control-group">
          <label class="control-label">Affichage</label>
          <div class="controls">
            <!-- Multiple Checkboxes -->
            <label class="checkbox">
              <input type="checkbox" value="Afficher les fichiers et dossiers cachés">
              Afficher les fichiers et dossiers cachés
            </label>
            <label class="checkbox">
              <input type="checkbox" value="Mettre en évidence le contenu fraîchement téléchargé">
              Mettre en évidence le contenu fraîchement téléchargé
            </label>
            <label class="checkbox">
              <input type="checkbox" value="Ignorer les erreurs de chmod sur l'accueil">
              Ignorer les erreurs de chmod sur l'accueil
            </label>
          </div>
      </div>

      <div class="control-group">
        <!-- Text input-->
        <label class="control-label" for="input01">Téléchargements</label>
        <div class="controls">
          <input type="text" placeholder="downloads" class="input-xlarge">
          <span class="help-block">Indiquez le dossier où se trouve vos fichiers téléchargés. Par défaut "download".</span>
        </div>
      </div>

      <div class="control-group">
        <!-- Text input-->
        <label class="control-label" for="input01">Fichiers à exclure</label>
        <div class="controls">
          <input type="text" placeholder="" class="input-xlarge">
          <span class="help-block">Listez les fichiers que vous souhaitez exclure du listing, en les séparant par une virgule.</span>
        </div>
      </div>


      <div class="control-group">
        <label class="control-label">Les mises à jour</label>
        <div class="controls">
            <!-- Multiple Radios -->
            <label class="radio">
              <input type="radio" value="Vérifier automatiquement de temps en temps" name="update" checked="checked">
              Vérifier automatiquement de temps en temps
            </label>
            <label class="radio">
              <input type="radio" value="Ne jamais vérifier" name="update">
              Ne jamais vérifier
            </label>
            <label class="radio">
              <input type="radio" value="Vérifier à chaque visite" name="update">
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
          <select class="input-xlarge">
            <option>VLC</option>
            <option>DivX Web Player</option>
          </select>
        </div>
      </div>
    </div>

    <div class="tab-pane" id="background">...</div>
    <!--<div class="tab-pane" id="background">...</div>-->

    <div class="controls">
      <button class="btn btn-success">Sauvegarder</button>
       <button class="btn btn-default modal_close" onclick="return false;">Fermer</button>
    </div>   

    </div>
  </form>

</div>

<div id="about_us" class="modal_window">
  <h1>A propos de nous</h1>
  <p>Cakebox est développé par <strong>MardamBey</strong> & <strong>Tuxity</strong>, depuis 2012.</p>
  <p>
    <strong>Site officiel :</strong> <a href="http://www.heycakebox.com">heycakebox.com</a> <br />
    <strong>Dépot Github :</strong> <a href="https://github.com/MardamBeyK/Cakebox">www.github.com/MardamBeyK/Cakebox</a><br />
    Remarques, suggestions, propositions : <a href="mailto:iam.mardambey@gmail.com">iam.mardambey@gmail.com</a>
  </p>  
  <div class="modal_button_div">
    <div class="controls">
       <button class="btn btn-default modal_close">Fermer</button>
    </div>
  </div>
</div>