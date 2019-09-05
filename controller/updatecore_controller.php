<?php
/**
 * Created by PhpStorm.
 * User: gener
 * Date: 26.07.2018
 * Time: 15:36
 */

if (!function_exists('is_availible')) {
    header('Location: ../404');
}
include(get_parent_dir() . '/model/updatecore_model.php');

class updatecore_controller extends updatecore_model
{
    private $type;
    private $courses;

    public function __construct($action_type, $content_array, $user_token)
    {
        $this->type = $action_type;
        $this->courses = $content_array;
        parent::__construct($user_token);
    }

    public function do_action()
    {
        if ($this->type == 'updatecoursescore') {
            return $this->GetUpdateResultByCourses($this->courses);
        }
        if ($this->type == 'special') {
            include("includes/backups/core/backup_file.php");
            $db_backuper = new backup_file();
            $db_backuper->CreateDbBackup();
            if (!isset($this->courses->table_id)) {
                return array("message_type" => "error", "message" => "Не удалось получить выбраную таблицу. Вы уверены, что данная таблица уже была сохранена? Попробуйте обновить страницу и повторить попытку изменения снова.");
            }
            if (isset($this->courses->value)) {
                if ($this->courses->value == 'all') {
                    $this->InsertQueryIntoDb("UPDATE courses SET hoocked_table_id='" . $this->courses->table_id . "' WHERE 1");
                    return array("message_type" => "error", "message" => "Выбраная операция была применена ко всем элементам в бд. Не забудьте проверить работоспособность сайта. В случае нарушения в работе сайта, попробуйте откатить изменения ( Управление подключениями->Работа с БД->Восстановить полседную версию )");
                } else if (preg_match("/[0-9]+/", $this->courses->value, $out)) {
                    $this->InsertQueryIntoDb("UPDATE courses SET hoocked_table_id='" . $this->courses->table_id . "' WHERE type='" . $this->courses->value . "'");
                    return array("message_type" => "error", "message" => "Выбраная операция была применена к выбраным элементам. Не забудьте проверить работоспособность сайта. В случае нарушения в работе сайта, попробуйте откатить изменения ( Управление подключениями->Работа с БД->Восстановить полседную версию )");
                }
            }
        }
    }

    public function UpdateCore()
    {
        switch($this->type)
        {
            case 'UserUpdateCore' : return $this->UpdateUser($this->courses);break;
            case 'PeriodUpdateCore' : return $this->UpdatePeriod($this->courses);break;
            case 'MetaUpdateCore' : return $this->UpdateMeta($this->courses);break;
            case 'TableUpdateCore' : return $this->UpdateHoockedTable($this->courses);break;
            default: return array("message_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Ядро обновления не смогло получить данные относительно обновляемого объекта.");
        }
    }

    public function UpdateCoreGlobal()
    {
        if($this->courses == '')
        {
            return array("message_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Неверный формат данных передан в обновительное ядро.");
        }
        switch ($this->type)
        {
            case 'UpdatePeriodGlobalCore' : return $this->UpdatePeriodGlobal($this->courses);break;
            case 'UpdateTableGlobalCore' : return $this->UpdateTableGlobal($this->courses);break;
            case 'UpdateMetaGlobalCore' : return $this->UpdateMetaGlobal($this->courses);break;
        }
    }

}