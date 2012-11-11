<?php

require('inc/functions.inc.php');

if(isset($_GET['get_list']))
{
    // Parse parameters FILTER
    if(isset($_GET['filter'])) $filter = str_replace("-edit","",$_GET['filter']); // because javascript send "videos-edit" or "all-edit" and php needs "videos" or "all".
    else $filter = FALSE;
    // Parse parameters EDITMODE
    $editmode = (isset($_GET['editmode'])) ? TRUE:FALSE;
    // Creation of the array
    $listof_dir = array(); // global var filled by recursive_directory_tree()

    // Create the tree_structure
    $tree_structure = recursive_directory_tree("downloads");
    print_tree_structure($tree_structure,$filter,$editmode);
}
else if(isset($_GET['drop_file']) && isset($_GET['file_name']))
{
    unlink($_GET['file_name']);
}
else if(isset($_GET['mark_file']) && isset($_GET['file_name']))
{
    $file = fopen("data/".$_GET['file_name'], "w");
    fwrite($file,"1");
    fclose($file);
}
else if(isset($_GET['unmark_file']) && isset($_GET['file_name']))
{
    unlink("data/".$_GET['file_name']);
}
?>