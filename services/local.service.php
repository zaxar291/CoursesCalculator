<?php
get_template_part("/includes/backups/core/backup_file.php");
class Local extends backup_file
{
    private $localHashes = array();
    public function __construct()
    {
        $this->localHashes = array("loginHash" => '$2y$10$VZlMo7qrtJsiKDeCWHuBCuejz9TkUAInVobIo/H7YanKoVCJQSP/W', "passwordHash" => '$2y$10$LYyPFU8y59l5ci6nhzk7VuOb0RSuDSkJyag.zHwq1nG02.whGIG7S');
        parent::__construct();
    }

    public function ValidateUser($login, $password)
    {
        if(password_verify($login, $this->localHashes["loginHash"]) and password_verify($password, $this->localHashes["passwordHash"]))
        {
            return 1;
        }else{
            return 0;
        }
    }

    public function ChangeFileSettings($host, $login, $pass, $name)
    {
        $jsonFile = json_decode(file_get_contents('includes/backups/core/configs/admin/database/package.json'));
        file_put_contents("includes/backups/core/configs/admin/database/package.json", json_encode(array(
            "name" => "DataBaseAuthInfo",
            "version" => $jsonFile->version++,
            "parametrs" => array(
                "db_host" => $host,
                "db_login" => $login,
                "db_password" => ($pass == "null") ? "" : $pass,
                "db_name" => $name
            ),
            "provide" => "services/local.service.php",
            "last_modify" => date("l d F Y h:i:s A"),
            "user_modify" =>  $jsonFile->user_modify,
            "system" => array(
                "is_success" => "true",
                "tables" => ''
            ),
            "last_change_file" => $jsonFile->last_change_file
        )));
        return 1;
    }

    public function UpdateFileInFileSettings($filename)
    {
        $jsonFile = json_decode(file_get_contents('includes/backups/core/configs/admin/database/package.json'));
        file_put_contents("includes/backups/core/configs/admin/database/package.json", json_encode(array(
            "name" => "DataBaseAuthInfo",
            "version" => $jsonFile->version++,
            "parametrs" => array(
                "db_host" => $jsonFile->parametrs->db_host,
                "db_login" => $jsonFile->parametrs->db_login,
                "db_password" => $jsonFile->parametrs->db_password,
                "db_name" => $jsonFile->parametrs->db_name
            ),
            "provide" => "services/local.service.php",
            "last_modify" => date("l d F Y h:i:s A"),
            "user_modify" =>  $jsonFile->user_modify,
            "system" => array(
                "is_success" => "true",
                "tables" => ''
            ),
            "last_change_file" => $filename
        )));
        return 1;
    }

    public function RestoreLocalDbCopy()
    {
        return $this->RollbackLastDbVersion();
    }
}