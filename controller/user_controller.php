<?php
/**
 * Created by PhpStorm.
 * User: DNAPC
 * Date: 26.06.2018
 * Time: 14:38
 */

class user_controller extends token_controller
{
    private $pass;
    protected $login;

    public function __construct($login, $pass)
    {
        $this->login = $login;
        $this->pass = $pass;
        parent::__construct('');
    }

    public function is_user()
    {
        return $this->user_valid();
    }

    private function user_valid()
    {
        set_user_login($this->login);
        if (password_verify($this->pass, $this->GetResultFromDb("SELECT * FROM users WHERE login='" . $this->login . "'")["0"]["pass"])) {
            $_SESSION["auth"] = 1;
            $_SESSION["login"] = $this->login;
            $_SESSION['role'] = $this->GetResultFromDb("SELECT * FROM users WHERE login='" . $this->login . "'")['0']['role'];
            return 1;
        } else {
            return 0;
        }
    }

}