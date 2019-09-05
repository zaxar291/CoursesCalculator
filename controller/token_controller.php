<?php
/**
 * Created by PhpStorm.
 * User: DNAPC
 * Date: 20.06.2018
 * Time: 12:08
 */

class token_controller extends db_controller
{
    private $token;
    private $user_token;
    private $user_login;

    public function __construct($user_token)
    {
        parent::__construct();
        $this->user_token = $user_token;
        $this->token = $this->get_token();
    }

    public function check_token()
    {
        if($this->token == $this->user_token and $this->get_token() !== 0)
        {
            return 1;
        }else{
            return 0;
        }
    }

    private function is_token($json)
    {
        if(time() - 9999999 < $json["0"]["token_set_time"])
        {
            return $json["0"]['user_checked_token'];
        }else{
            return 0;
        }
    }

    public function get_token()
    {
        $token = $this->GetResultFromDb("SELECT * FROM users WHERE login='".get_user_login()."'");
        if($token == 0)
        {
            return 0;
        }else{
            return $this->is_token($token);
        }
    }

    public function create_token()
    {
        if($this->InsertQueryIntoDb("UPDATE users SET user_checked_token='".md5(uniqid(rand(), true))."', token_set_time='".time()."' WHERE login='".get_user_login()."'"))
        {

            return $_SESSION['user_token'] =  $this->get_token();
        }else{
            return 0;
        }
    }

    public function set_user_login($login)
    {
        $this->user_login = $login;
    }
}