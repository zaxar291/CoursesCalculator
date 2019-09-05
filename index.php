<?php
    ini_set('display_errors','off');
    error_reporting('E_ALL');
    require 'controller/global_functions_conrtoller.php';
    require 'controller/db_controller.php';
    $routes = explode('/',$_SERVER["REQUEST_URI"])["2"];
    if($_GET)
    {
        $routes = explode("?", $routes)["0"];
    }
    set_parent_dir(__DIR__);
    if($routes  == "")
    {
        $routes = header("Location: calculator");
    }
    if(preg_match_all("/[A-Za-z]+.php/", $routes, $out))
    {
        header("Location: ".clear_file_from_php(get_file_from_array($out)));
    }elseif (is_file(get_parent_dir()."/controller/".$routes."_controller.php") and is_file(get_parent_dir()."/model/".$routes."_model.php" ) and is_file(get_parent_dir()."/view/".$routes.".php" ))
    {
        include(get_parent_dir().'/controller/'.$routes."_controller.php");
        include(get_parent_dir()."/view/".$routes.".php");
    }else{

        get_404_page();
    }
