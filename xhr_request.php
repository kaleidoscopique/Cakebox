<?php

require('inc/functions.inc.php');

if(isset($_GET['mark_file']) && isset($_GET['file_name']))
{
    // Si le fichier n'est pas un path
    if(substr_count($_GET['file_name'],'/') == 0)
    {
        $file = fopen("data/".$_GET['file_name'], "w");
        fwrite($file,"1");
        fclose($file);
    }
}
else if(isset($_GET['unmark_file']) && isset($_GET['file_name']))
{
    // Si le fichier n'est pas un path && s'il existe bel et bien dans /data
    if(substr_count($_GET['file_name'],'/') == 0 && file_exists("data/".$_GET['file_name']))
    {
        unlink("data/".$_GET['file_name']);
    }
}
?>