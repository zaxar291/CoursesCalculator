<?php
if(!function_exists("get_header")){header("Location: ../404");}
if(!isset($_GET))
{
    header('Location: ../404');
}
session_start();
if(isset($_GET["type"]))
{
    $msg = '';
    set_user_login($_SESSION['login']);
    if($_GET["type"] == "UpdateCore")
    {
        $updater = new updatecore_controller($_GET["observer"], json_decode($_GET["content"]), get_user_token());
        if($updater->check_token())
        {
           $msg = $updater->UpdateCore();
        }else{
            $msg = array("action_type" => "server_response", "observer" => $_GET["observer"], "is_success" => false, "message" => "Доступ к ядру обновлений запрещён, у вас нет прав на выполнение этой команды.");
        }
    }
    if($_GET["type"] == "GlobalUpdateCore")
    {
        $updater = new updatecore_controller($_GET["observer"], json_decode($_GET["content"]), get_user_token());
        if($updater->check_token())
        {
            $msg = $updater->UpdateCoreGlobal();
        }else{
            $msg = array("action_type" => "server_response", "observer" => $_GET["observer"], "is_success" => false, "message" => "Доступ к ядру обновлений запрещён, у вас нет прав на выполнение этой команды.");
        }
    }
    if($_GET["type"] == "ChangeDbSettings")
    {
        get_template_part("/includes/backups/core/backup_file.php");
        $backupFile = new backup_file();
        $lastFileContent = $backupFile->GetSettingsFile();
        $content = json_decode($_GET["content"]);
        if($_GET["algo"] == "fileSettings")
        {
            file_put_contents("includes/backups/core/configs/admin/database/package.json", json_encode(array(
                "name" => "DataBaseAuthInfo",
                "version" => $lastFileContent->version++,
                "parametrs" => array(
                    "db_host" => $content->host,
                    "db_login" => $content->username,
                    "db_password" => $content->password,
                    "db_name" => $content->name
                ),
                "provide" => "includes/backups/core/backup_file.php",
                "last_modify" => date("l d F Y h:i:s A"),
                "user_modify" => (isset($_SESSION["login"])) ? $_SESSION["login"] : "Неизвестный пользователь! Внимание!",
                "system" => array(
                    "is_success" => "true",
                    "tables" =>$lastFileContent->tables
                ),
                "last_change_file" => $lastFileContent->last_change_file
            )));
            $msg = array("message_type" => "server_response", "is_success" => true);
        }
        if($_GET["algo"] == "RestoreDb")
        {
            $msg = $backupFile->RollbackLastDbVersion();
        }
    }
    echo json_encode($msg);
    exit;
}
if(isset($_FILES))
{
    if(isset($_FILES["dbFile"]))
    {
        $file = $_FILES["dbFile"];
        $temp = $file["tmp_name"];
        if(file_exists($temp))
        {
            if(preg_match("/[A-Za-z0-9_?-?]/", $file["name"], $out))
            {
                if(move_uploaded_file($temp, "includes/backups/core/storage/".$file["name"]))
                {
                    get_template_part("/services/local.service.php");
                    $local = new Local();
                    $local->UpdateFileInFileSettings("includes/backups/core/storage/".$file["name"]);
                    echo json_encode($local->RestoreLocalDbCopy());
                }else{
                    echo json_encode(array("message_type" => "server_response", "is_success" => false, "message" => "Ошибка файл, ".$file["name"] . " не может быть перемещён в папку "."includes/backups/core/storage/".$file["name"]));
                }
            }else{
                echo json_encode(array("message_type" => "server_response", "is_success" => false, "message" => "Ошибка файл, ".$file["name"] . " не является файлом формата .json!"));
            }
        }else{
            echo json_encode(array("message_type" => "server_response", "is_success" => false, "message" => "Ошибка закачки файла, ".$file["error"]));
        }
        exit;
    }
}
if(isset($_GET["action"]) && isset($_GET["token"]))
{
    set_user_login($_SESSION['login']);
    $updater = new updatecore_controller($_GET["action"], json_decode($_GET["content"]), $_GET["token"]);
    if($updater->check_token())
    {
        echo json_encode($updater->do_action());
    }
}

