<?php
/**
 * Created by PhpStorm.
 * User: gener
 * Date: 8/20/2018
 * Time: 5:42 PM
 */

class VisitState
{
    private $ip;
    private $filejson = 'includes/visitstate/package.json';
    private $server = array();
    private $log = array("AnaliseInputIp" => "", "is_adress" => "", "ChangeIpCount" => "", "AddAdress" => "");

    public function __construct($ip, $server) {
        $this->ip = $ip;
        $this->server = $server;
    }

    public function AnaliseInputIp() {
        $this->AddLog(__FUNCTION__, "->Starting AnaliseInputIp() function, complete.\n");
        if ($this->is_adress()) {
            if ($this->ChangeIpCount()) {
                return 1;
            } else {
                return 0;
            }
        } else {
            if ($this->AddAdress()) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    private function is_adress($message = "") {
        $message = "->Start is_adress() function, complete. \n";
        if ($this->ip == null || $this->ip == "") {
            $message .= "->Error, not adress given, given IpAdress->$this->ip, brake.\n";
            return 0;
        }
        if (is_file($this->filejson)) {
            $ip = $this->ip;
            $data = json_decode(file_get_contents($this->filejson));
            foreach ($data as $key) {
                foreach ($key as $k) {
                    if ($k->ip == $this->ip) {
                        $message .= "->Ip adress found, return 1, completed.\n";
                        $this->AddLog(__FUNCTION__, $message);
                        return 1;
                    } else {
                        $message .= "->Ip adress not found, return 0, completed.\n";
                        $this->AddLog(__FUNCTION__, $message);
                    }
                }
            }
        }
    }

    private function AddAdress($message = "->Starting AddAdress() function, completed.\n") {
        if (is_file($this->filejson)) {
            $output[] = array("ip" => $this->ip, "count" => "1", "time" => date("his"));
            $data = json_decode(file_get_contents($this->filejson));
            if ($data == "") {
                $this->AddLog(__FUNCTION__, $message .= "->JSON file empty, adding first adress, completed.\n");
                file_put_contents($this->filejson, json_encode(array($output)));
                return 1;
            }
            $message .= "->Trying to rewrite all ip adresses, completed.\n";
            foreach ($data as $key) {
                foreach ($key as $k => $v) {
                    $output[] = array("ip" => $v->ip, "count" => "1", "time" => date("his"));
                }
            }
            $message .= "->Rewrite ip adresses completed.\n";
        } else {
            return 0;
        }
        file_put_contents($this->filejson, json_encode(array($output)));
        $message .= "->JSON file was rewrite succesful.\n";
        $this->AddLog(__FUNCTION__, $message);
        return 1;
    }

    private function ChangeIpCount($output = array(), $message = "->Start ChangeIpCount() function, completed.\n") {
        $message .= "->Try to get JSON file content.\n";
        $data = json_decode(file_get_contents($this->filejson));
        $message .= "->JSON data received.\n";
        if ($data == "" || $data == null) {
            $message .= "->JSON data empty, error, operation brake.\n";
            return 0;
        }
        $message .= "->JSON foreach start.\n";
        foreach ($data as $key) {
            foreach ($key as $k => $v) {
                if ($v->ip == $this->ip) {
                    $output[] = array("ip" => $v->ip, "count" => $v->count + 1, "time" => date("his"));
                    $message .= "->Neadable ip adress found, is -> $v->ip.\n";
                } else {
                    $output[] = array("ip" => $v->ip, "count" => $v->count, "time" => $v->time);
                    $message .= "->Not this ip adress ,continue, now ip is -> $v->ip.\n";
                }
            }
        }
        $message .= "->Try to start writing JSON file.\n";
        file_put_contents($this->filejson, json_encode(array($output)));
        $message .= "->Rewrite completed.\n";
        $this->AddLog(__FUNCTION__, $message);
        return 1;
    }

    public function CreateServerLog() {
        return 1;
//        if (!is_dir("includes/visitstate/logs/")) {
//            if (!mkdir("includes/visitstate/logs/", 0777)) {
//                return 0;
//            }
//        }
//        if (mkdir($dir = "includes/visitstate/logs/" . time(), 0777)) {
//            $file = fopen($dir . "/log.txt", "a+");
//                fwrite($file, $this->GetLog());
//            } else {
//                return 0;
//            }
        }

        private function GetLog($output = "") {
            foreach ($this->log as $key => $value) {
                $output .= $value;
            }
            return $output . "->End.\n**************************************************************************************************************************************************************************************************************************************************************************************************************8\n".$this->GetServerRequest();
        }

        private function AddLog($function, $message) {
            $this->log[$function] = $message;
            return;
        }

        private function GetServerRequest($output = ""){
            foreach ($this->server as $key => $value){
                if(!is_array($key)){
                $output .= "$key ------> $value\n";
            }
        }
        return $output."\n";
    }
}