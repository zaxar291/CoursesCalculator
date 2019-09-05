<?php
/**
 * Created by PhpStorm.
 * User: gener
 * Date: 8/23/2018
 * Time: 4:53 PM
 */

class LogService
{
    private $time;
    private $logId;
    private $logFile = 'includes/logs/log.html';
    private $logSiteFile = 'includes/backups/core/logs/logs.html';

    public function __construct($logid)
    {
        $this->time = date("Y-m-d h:i:s");
        $this->logId = $logid;
    }

    public function WriteLog($message)
    {
        if($this->logId == "#error")
        {
            return;
        }
        $file = fopen($this->logFile, 'a+');
        $content = fread($file, filesize($this->logFile));
        if($message == "Пользователь получил результат калькуляции")
        {
            fwrite($file, "<p style='color: green'>[".$this->time."]{Logid #".$this->logId."} -> ".$message."</p>");
        }
        if($message == "Пользователь выбрал курс для калькуляции")
        {
            fwrite($file, "<p style='color: yellow'>[".$this->time."]{Logid #".$this->logId."} -> ".$message."</p>");
        }
        if($message == "Пользователь выбрал класс для калькуляции")
        {
            fwrite($file, "<p style='color: orange'>[".$this->time."]{Logid #".$this->logId."} -> ".$message."</p>");
        }
        if($message == "Пользователь выбрал год для калькуляции")
        {
            fwrite($file, "<p style='color: red'>[".$this->time."]{Logid #".$this->logId."} -> ".$message."</p>");
        }
    }

    public function WriteSiteLog($message)
    {
        $file = fopen($this->logSiteFile, 'a+');
        fwrite($file, "[".date("Y-m-d h:i:s")."]{Logid #".$this->logId."} - ".$message."<br>");
    }
}