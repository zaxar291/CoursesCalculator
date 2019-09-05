<?php
class feedbacks {
    private $fedbacksdir = 'includes/feedbacks/';
    private $feedbackfilename = 'feedback.txt';
    private $time = '';

    public function __construct()
    {
        $this->time = mktime();
    }

    public function AddNewResponse($message)
    {
        $filecontent = '-'.$message->name.'  --'.$message->email.'  ---'.$message->message.'  ';
        if(is_dir($this->fedbacksdir.$this->time.'/'))
        {
            $this->time = mktime();
        }
        if(mkdir($this->fedbacksdir.$this->time.'/', 0777))
        {
            $file = fopen($this->fedbacksdir.$this->time.'/'.$this->feedbackfilename, "a+");
            if(fwrite($file, $filecontent))
            {
                return array("response_content" => "Спасибо за ваш вопрос, мы свяжемся с вами в ближайшее время!", "response_color" => "green");
            }else {
                return array("response_content" => "В данный момент работа системы связи невозможна, приносим прощения за временные неудобства", "response_color" => "red");
            }
        }else {
            return array("response_content" => "В данный момент работа системы связи невозможна, приносим прощения за временные неудобства", "response_color" => "red");
        }
    }
}