<?php

require_once('inc/Configuration.class.php');
require_once('inc/FileTree.class.php');
require_once('inc/File.class.php');


// Dérouler un dossier
if(isset($_GET['dir_content']))
{
    $treeStructure = new FileTree($_GET['dir_content']);
    $treeStructure->print_tree();
    if(!$treeStructure->get_tree()) echo "Dossier vide";
    exit(0);
}

// Marquer un fichier comme "vu"
if(isset($_GET['mark_file']) && isset($_GET['file_name']))
{
    // Si le fichier n'est pas un path
    if(substr_count($_GET['file_name'],'/') == 0)
    {
        $file = fopen("data/".$_GET['file_name'], "w");
        fwrite($file,"1");
        fclose($file);
        exit(0);
    }
}

// Démarquer un fichier "vu"
else if(isset($_GET['unmark_file']) && isset($_GET['file_name']))
{
    // Si le fichier n'est pas un path && s'il existe bel et bien dans /data
    if(substr_count($_GET['file_name'],'/') == 0 && file_exists("data/".$_GET['file_name']))
    {
        unlink("data/".$_GET['file_name']);
        exit(0);
    }
}

// Request : DELETE FILES
if(isset($_POST['delete']))
{
    // TODO.
}
?>