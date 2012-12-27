<?php

// Nombre d'heures entre chaque vérif de mise à jour (défaut : 12)
// Mettez 0 pour vérifier à chacune de vos visites
define('TIME_CHECK_UPDATE', 12);

// Permet le mode d'édition
define('EDITMODE_ENABLE', TRUE);

// Affiche les fichiers et dossiers cachés
define('DISPLAY_HIDDEN_FILESDIRS', FALSE);

// Exclusion de certains fichiers ou dossiers à l'affichage
$excludeFiles = array(".htaccess", "");
