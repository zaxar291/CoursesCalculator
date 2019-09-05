<?php
/**
 * Created by PhpStorm.
 * User: DNAPC
 * Date: 20.06.2018
 * Time: 12:12
 */
class calculator_model extends db_controller
{
    private $output;
    public function __construct()
    {
        parent::__construct();
    }

    protected function get_result_from_db($sql)
    {
        if(!$this->connect)
        {
            if(!$this->CreateConnect())
            {
                $this->set_db_log('Не удалось установить соеденение с бд.');
                return 0;
            }
        }
        $result = $this->connect->query($sql);
        if($result->num_rows > 0)
        {
            while($res = $result->fetch_assoc())
            {
                $this->output[] = $res;
            }
            return 1;
        }else{
            $this->set_db_log('Пустой результат из бд, операция прервана.');
            return 0;
        }
    }
    protected function get_result()
    {
        return $this->output;
    }


    private function set_db_log($message)
    {
        $this->db_log = $message;
    }

    protected function get_db_log()
    {
        return $this->db_log;
    }
}
