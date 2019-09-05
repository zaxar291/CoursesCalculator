<?php
if(!function_exists("get_header")){header("Location: ../404");}
if(!isset($_GET))
{
    header('Location: ../404');
}
session_start();
if(isset($_GET['id']) and isset($_GET['token']) and isset($_GET['action_type']) and isset($_SESSION['login']) and isset($_SESSION['role']))
{
    if(clear($_GET['id']) !== '' and clear($_GET['token'] !== '') and clear($_GET['action_type']) !== '' and $_SESSION['login'] !== '' and session_role_valid())
    {
        set_user_login($_SESSION['login']);
        $worker = new global_admin_controller($_GET['action_type'], $_GET['id'], $_SESSION['user_token']);
        if($worker->check_token())
        {
            print_r($worker->do_action());
        }else{
            echo '<p align="center" style="color:red">Access denied for '.$_SESSION['login'].', your token is outdated, you should login in the admin panel one more time.</p>';
        }
    }else{
        echo 'Sth invalid';
    }
}else{
    echo 'Invalid';
}