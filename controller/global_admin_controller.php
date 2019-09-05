<?php
/**
 * Created by PhpStorm.
 * User: gener
 * Date: 12/07/2018
 * Time: 15:57
 */
if(!function_exists('is_availible'))
{
    header('Location: ../404');
}
include(get_parent_dir().'/model/global_admin_model.php');
class global_admin_controller extends global_admin_model
{
    private $action_name;
    private $actionid;
    private $user_token;

    public function __construct($action_name, $actionid, $user_token)
    {
        $this->action_name = $action_name;
        $this->actionid = $actionid;
        $this->user_token = $user_token;
        parent::__construct($user_token);
    }

    public function do_action()
    {
        switch ($this->action_name)
        {
            case 'getcourse': return $this->GetCourse($this->GetResultFromDb("SELECT courses.id, courses.price, courses.total, courses.minimum, courses.maximum, courses.discount, courses.metatext_id, courses.content, tables_teplate.table_description, courses.status, years.year_content, courses.type FROM `courses` LEFT JOIN years ON years.ID = courses.year LEFT JOIN tables_teplate ON courses.hoocked_table_id = tables_teplate.table_id LEFT JOIN metatexts ON metatexts.id = courses.metatext_id WHERE courses.group_id = '".$this->actionid."'"));
            case 'gettable': return $this->GetHoockedTable($this->GetResultFromDb("SELECT * FROM tables_teplate WHERE table_id='".$this->actionid."'"));break;
            case 'getmeta' : return $this->GetMetaTable($this->GetResultFromDb("SELECT * FROM metatexts WHERE id='".$this->actionid."'"));break;
            case 'getperiod' : return $this->GetPeriodTable($this->GetResultFromDb("SELECT * FROM years WHERE id='".$this->actionid."'"));break;
            case 'getuser' : return $this->GetUserTable($this->GetResultFromDb("SELECT * FROM users WHERE id='".$this->actionid."'"));break;
            default: return '<p align="center" style="color:red">Unknown action '.$this->action_name.', please, use only admin panel interface, try to reload page and do this action one more time, if it will not help to you, write message to us.</p>';
        }
    }
}