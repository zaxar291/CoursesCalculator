<?php
/**
 * Created by PhpStorm.
 * User: Захар
 * Date: 19.06.2018
 * Time: 21:24
 */

class db_controller
{
    protected $connect;
    private $dbData;

    public function __construct()
    {
        $this->dbData = get_database_file()->parametrs;
        $this->CreateConnect();
    }

    protected function CreateConnect()
    {
        if(!$this->connect)
        {
            $this->connect = mysqli_connect($this->dbData->db_host, $this->dbData->db_login, $this->dbData->db_password, $this->dbData->db_name);
            if (!$this->connect) {
                echo json_encode(array("message_type" => "error", "message_content" => "База данных временно недоступна, пожалуйста, повторите попытку калькуляции позже, если проблема не исчезнет, вы можете <a style='cursor: pointer; text-decoration: none' onclick=\"ShowModal('feedback')\">связаться</a> с нами для получения более конкретной информации."));
                die();
            }
            mysqli_set_charset($this->connect, "utf8");
            $this->connect->query("set names utf8");
            return 1;
        }else{
            return 0;
        }
    }

    protected function InsertQueryIntoDb($sql)
    {
        return $this->connect->query($sql);
    }

    function GetResultFromDb($sql, $output = array())
    {
        $result = $this->connect->query($sql);
        if(!$result)
        {
            return 0;
        }
        if($result->num_rows > 0)
        {
            while($res = $result->fetch_assoc())
            {
                if(is_array($res))
                {
                    $output[] = $res;
                }else{
                    $output .= $res;
                }
            }
        }else{
            return 0;
        }
        return $output;
    }
}