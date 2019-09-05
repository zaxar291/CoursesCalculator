<?php
/**
 * Created by PhpStorm.
 * User: gener
 * Date: 26.07.2018
 * Time: 15:36
 */

if(!function_exists('is_availible'))
{
    header(' Location: ../404');
}
require('controller/token_controller.php');
class updatecore_model extends token_controller
{
    private $errors = array();
    public function __construct($user_token)
    {
        parent::__construct($user_token);
    }

    protected function GetUpdateResultByCourses($courses_list)
    {
        foreach ($courses_list as $key)
        {
            $this->CheckData($key);
            if(!empty($this->errors))
            {
                return array("error" => $this->errors);
            }
        }
        $output = array();
        foreach ($courses_list as $key)
        {
            if($this->InsertQueryIntoDb("UPDATE courses SET content='".$key->course_name."', price='".$key->course_price."', discount='".$key->course_discount."', total='".$key->course_total."', minimum='".$key->course_min."', maximum='".$key->course_max."', hoocked_table_id='".$key->hooked_table."', metatext_id='". $key->meta_text ."', status='".$key->course_status."' WHERE id='".$key->course_ident_id."'"))
            {
                $output[$key->course_ident_id] = array("success_button" => "Rурс " . $key->course_name ." успешно обновлён!");
            }else{
                $output[$key->course_ident_id] = array("error_button" => "Ошибка, курс " . $key->course_name ." не был обновлён в бд, попробуйте повторить попытку снова.");
            }
        }
        return array("messages_under_button" => $output);
    }

    private function CheckData($course)
    {
        $id = $course->course_ident_id;
        if(isset($course->course_name))
        {
            if($course->course_name == "")
            {
                $this->errors[$course->course_name] = array('error_content' => 'Вы пропустили это поле!', 'error_object' => 'course_name_', "error_focused" => 'course_name_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
                return;
            }
        }else{
            $this->errors[$course->course_name] = array('error_content' => 'Не удалось обработать это поле, обновите страницу и повторите попытку редактирования.', 'error_object' => 'course_name_', 'item_id' => $id);
            return;
        }
        if(isset($course->course_price))
        {
            if($course->course_price == "" || $course->course_price <= 0 || !preg_match_all('/[0-9]+/', $course->course_price, $out))
            {
                $this->errors[$course->course_price] = array('error_content' => 'Цена должна быть выше 0 и должна состоять только из цифр.', 'error_object' => 'course_price_', "error_focused" => 'course_price_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
                return;
            }
        }else{
            $this->errors[$course->course_price] = array('error_content' => 'Не удалось обработать это поле, обновите страницу и повторите попытку редактирования.', 'error_object' => 'course_price_', "error_focused" => 'course_price_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
            return;
        }
        if(isset($course->course_discount))
        {
            if($course->course_discount <= 0 || !preg_match_all('/[0-9]+/', $course->course_discount, $out))
            {
                $this->errors[$course->course_discount] = array('error_content' => 'Скидка не может быть отрицательной и должна состоять только из цифр.', 'error_object' => 'course_discount_', "error_focused" => 'course_discount_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
                return;
            }
        }else{
            $this->errors[$course->course_discount] = array('error_content' => 'Не удалось обработать это поле, обновите страницу и повторите попытку редактирования.', 'error_object' => 'course_discount_', "error_focused" => 'course_discount_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
            return;
        }
        if(isset($course->course_total))
        {
            if($course->course_total < 0 || !preg_match_all('/[0-9]+/', $course->course_total, $out))
            {
                $this->errors[$course->course_total] = array('error_content' => 'Общее кол-во занятий не может быть отрицательным и должно состоять только из цифр.', 'error_object' => 'course_total_', "error_focused" => 'course_total_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
                return;
            }
        }else{
            $this->errors[$course->course_total] = array('error_content' => 'Не удалось обработать это поле, обновите страницу и повторите попытку редактирования.', 'error_object' => 'course_total_', "error_focused" => 'course_total_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
            return;
        }
        if(isset($course->course_min))
        {
            if($course->course_min <= 0)
            {
                $this->errors[$course->course_min] = array('error_content' => 'Класс не может быть меньше 1!', 'error_object' => 'course_min_', "error_focused" => 'course_min_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
                return;
            }
            if($course->course_min > 12)
            {
                $this->errors[$course->course_min] = array('error_content' => 'Максимально допустимый класс: 12', 'error_object' => 'course_min_', "error_focused" => 'course_min_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
                return;
            }
            if(!preg_match_all('/[0-9]+/', $course->course_total, $out))
            {
                $this->errors[$course->course_min] = array('error_content' => 'Класс может содержать только цифры!', 'error_object' => 'course_min_', "error_focused" => 'course_min_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
                return;
            }

        }else{
            $this->errors[$course->course_min] = array('error_content' => 'Не удалось обработать это поле, обновите страницу и повторите попытку редактирования.', 'error_object' => 'course_min_', "error_focused" => 'course_min_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
            return;
        }
        if(isset($course->course_max))
        {
            if($course->course_max <= 0)
            {
                $this->errors[$course->course_max] = array('error_content' => 'Класс не может быть меньше 1!', 'error_object' => 'course_max_', "error_focused" => 'course_max_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
                return;
            }
            if($course->course_max > 12)
            {
                $this->errors[$course->course_max] = array('error_content' => 'Максимально допустимый класс: 12', 'error_object' => 'course_max_', "error_focused" => 'course_max_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
                return;
            }
            if(!preg_match_all('/[0-9]+/', $course->course_max, $out))
            {
                $this->errors[$course->course_max] = array('error_content' => 'Класс может содержать только цифры!', 'error_object' => 'course_max_', "error_focused" => 'course_max_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
                return;
            }
            if($course->course_max <= $course->course_min)
            {
                $this->errors[$course->course_max] = array('error_content' => 'Ваш максимальный класс меньше или равен минимальному классу!', 'error_object' => 'course_max_', "error_focused" => 'course_max_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
                return;
            }
        }else{
            $this->errors[$course->course_max] = array('error_content' => 'Не удалось обработать это поле, обновите страницу и повторите попытку редактирования.', 'error_object' => 'course_max_', "error_focused" => 'course_max_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
            return;
        }

        if(!isset($course->hooked_table))
        {
            $this->errors[$course->hooked_table] = array('error_content' => 'Не удалось обработать это поле, обновите страницу и повторите попытку редактирования.', 'error_object' => 'hooked_table_', "error_focused" => 'course_table_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
        }

        if(!isset($course->meta_text))
        {
            $this->errors[$course->meta_text] = array('error_content' => 'Не удалось обработать это поле, обновите страницу и повторите попытку редактирования.', 'error_object' => 'meta_text_', "error_focused" => 'course_meta_errors_',  'item_id' => $id, 'slide_content' => 'course_'.$id.'_content');
        }

        return 1;
    }

    protected function UpdateUser($information)
    {
        $modelArray = array("login" => "", "role" => "", "isNewPass" => false, "pass" => "", "user_id" => "");
        if(isset($information->login))
        {
            $modelArray["login"] = $information->login;
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Не удалось получить новый логин пользователя, проверьте, что поле ввода логина не пустое.");
        }
        if(isset($information->role))
        {
            $modelArray["role"] = $information->role;
        }else{
            $modelArray["role"] = "editor";
        }
        if(!$information->isNewPass)
        {
            $modelArray["pass"] = $information->pass;
        }else{
            $modelArray["pass"] = password_hash($information->pass, PASSWORD_DEFAULT);
        }
        $modelArray["id"] = $information->id;
        if($this->InsertQueryIntoDb("UPDATE users SET login='".$modelArray["login"]."', role='".$modelArray["role"]."', pass='".$modelArray["pass"]."' WHERE id='".$modelArray["id"]."'"))
        {
            return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => null);
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Не удалось подключится к базе данных, повторите попытку обновления пользователя позже.");
        }
    }

    protected function UpdatePeriod($information)
    {
        $modelArray = array("id" => "", "content" => "", "is_active" => "");
        if(isset($information->id))
        {
            $modelArray["id"] = $information->id;
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Internal server error 500, operation braken.");
        }
        if(isset($information->content))
        {
            $modelArray["content"] = $information->content;
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Вы пропустили поле ввода контента периода!");
        }
        if(isset($information->isActive))
        {
            if($information->isActive)
            {
                $modelArray["is_active"] = "1";
            }else{
                $modelArray["is_active"] = "0";
            }
        }else{
            $modelArray["is_active"] = "1";
        }
        if($this->InsertQueryIntoDb("UPDATE years SET year_content='".$modelArray["content"]."', status='".$modelArray["is_active"]."' WHERE id='".$modelArray["id"]."'"))
        {
            return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => null);
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Не удалось подключится к базе данных, повторите попытку обновления пользователя позже.");
        }
    }
    protected function UpdateMeta($information)
    {
        $modelArray = array("id" => "", "description" => "", "content" => "", "is_active" => "");
        if(isset($information->description))
        {
            $modelArray["description"] = $information->description;
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Вы пропустили поле ввода описания метатекста!");
        }
        if(isset($information->content))
        {
            $modelArray["content"] = $information->content;
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Вы пропустили поле ввода контента метатекста!");
        }
        if(isset($information->isActive))
        {
            if($information->isActive)
            {
                $modelArray["is_active"] = "1";
            }else{
                $modelArray["is_active"] = "0";
            }
        }else{
            $modelArray["is_active"] = "1";
        }
        $modelArray["id"] = $information->id;
        if($this->InsertQueryIntoDb("UPDATE metatexts SET metatext_content='".$modelArray["content"]."', status='".$modelArray["is_active"]."', metatext_description='".$modelArray["description"]."' WHERE id='".$modelArray["id"]."'"))
        {
            return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => null);
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Не удалось подключится к базе данных, повторите попытку обновления пользователя позже.");
        }
    }

    protected function UpdateHoockedTable($information)
    {
        $modelArray = array("id" => "", "description" => "", "content" => "", "is_active" => true);
        if(isset($information->description))
        {
            $modelArray["description"] = $information->description;
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Вы пропустили поле ввода описания таблицы!");
        }
        if(isset($information->content))
        {
            $modelArray["content"] = $information->content;
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Вы не ввели никакой информации для вывода информации.");
        }
        if(isset($information->isActive))
        {
            if($information->isActive)
            {
                $modelArray["is_active"] = "1";
            }else{
                $modelArray["is_active"] = "0";
            }
        }else{
            $modelArray["is_active"] = "1";
        }
        $modelArray["id"] = $information->id;
        if($this->InsertQueryIntoDb("UPDATE `tables_teplate` SET table_content='".$modelArray["content"]."', table_status='".$modelArray["is_active"]."', table_description='".$modelArray["description"]."' WHERE table_id='".$modelArray["id"]."'"))
        {
            return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => null);
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Не удалось подключится к базе данных, повторите попытку обновления пользователя позже.");
        }
    }

    protected function UpdatePeriodGlobal($information)
    {
        if($information->algo !== 'null')
        {
            if($information->algo == "all")
            {
                get_template_part("/includes/backups/core/backup_file.php");
                $db_backuper = new backup_file();
                $db_backuper->CreateDbBackup();
                $courses_to_add = array();
                $courses_list = $this->GetResultFromDb("SELECT * FROM courses WHERE year='$information->periodId' ORDER BY group_id");
                $compareCourses = $this->GetCoursesList($this->GetResultFromDb("SELECT * FROM courses WHERE 1"));
                $counts = array("success" => 0, "failed" => 0);
                foreach($compareCourses as $key)
                {
                    if(!Compare($key["group_id"], $courses_list))
                    {
                        if($this->InsertQueryIntoDb("INSERT INTO `courses`(`year`, `content`, `type`, `price`, `total`, `minimum`, `maximum`, `discount`, `status`, `hoocked_table_id`, `metatext_id`, `group_id`) VALUES ('$information->periodId', '".$key["content"]."', '".$key["type"]."', '".$key["price"]."', '".$key["total"]."', '".$key["minimum"]."', '".$key["maximum"]."', '".$key["discount"]."', '".$key["status"]."', '".$key["hoocked_table_id"]."', '".$key["metatext_id"]."', '".$key["group_id"]."')"))
                        {
                            $counts["success"]++;
                        }else{
                            $counts["failed"]++;
                        }
                    }
                }
                if($counts["success"] == 0 && $counts["failed"] == 0)
                {
                    return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: выполено 0 операций, этот период уже был применён ко всем объектам в бд.");
                }else{
                    return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: статистика по изменении бд: успешных операций - ".$counts["success"].". Провальных операций - ".$counts["failed"]);
                }
            }
            if(preg_match_all("/[0-9]+/", $information->algo, $out))
            {
                get_template_part("/includes/backups/core/backup_file.php");
                $db_backuper = new backup_file();
                $db_backuper->CreateDbBackup();
                $courses_to_add = array();
                $courses_list = $this->GetResultFromDb("SELECT * FROM courses WHERE year='$information->periodId' ORDER BY group_id");
                $compareCourses = $this->GetCoursesList($this->GetResultFromDb("SELECT * FROM courses WHERE 1"));
                $counts = array("success" => 0, "failed" => 0);
                foreach($compareCourses as $key)
                {
                    if(!Compare($key["group_id"], $courses_list, "group_id") and $key["type"] == $information->algo)
                    {
                        if($this->InsertQueryIntoDb("INSERT INTO `courses`(`year`, `content`, `type`, `price`, `total`, `minimum`, `maximum`, `discount`, `status`, `hoocked_table_id`, `metatext_id`, `group_id`) VALUES ('$information->periodId', '".$key["content"]."', '".$key["type"]."', '".$key["price"]."', '".$key["total"]."', '".$key["minimum"]."', '".$key["maximum"]."', '".$key["discount"]."', '".$key["status"]."', '".$key["hoocked_table_id"]."', '".$key["metatext_id"]."', '".$key["group_id"]."')"))
                        {
                            $counts["success"]++;
                        }else{
                            $counts["failed"]++;
                        }
                    }
                }
                if($counts["success"] == 0 && $counts["failed"] == 0)
                {
                    return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: выполено 0 операций, этот период уже был применён ко всем объектам в бд.");
                }else{
                    return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: статистика по изменении бд: успешных операций - ".$counts["success"].". Провальных операций - ".$counts["failed"]);
                }
            }
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "Не удалось подключится к базе данных, повторите попытку обновления пользователя позже.");
        }
    }

    protected function UpdateTableGlobal($information)
    {
        if($information->algo !== "null")
        {
            if($information->algo == "all")
            {
                if($information->period == "allperiods")
                {
                    if($this->InsertQueryIntoDb("UPDATE courses SET hoocked_table_id='$information->tableId' WHERE 1"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }else{
                    if($this->InsertQueryIntoDb("UPDATE courses SET hoocked_table_id='$information->tableId' WHERE year='$information->period'"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }
            }
            if(preg_match_all("/[0-9]+/", $information->algo, $out))
            {
                if($information->period == "allperiods")
                {
                    if($this->InsertQueryIntoDb("UPDATE courses SET hoocked_table_id='$information->tableId' WHERE type='$information->algo'"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }else{
                    if($this->InsertQueryIntoDb("UPDATE courses SET hoocked_table_id='$information->tableId' WHERE type='$information->algo' AND year='$information->period'"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }
            }
            if($information->algo == "course")
            {
                if($information->period == "allperiods")
                {
                    if($this->InsertQueryIntoDb("UPDATE courses SET hoocked_table_id='$information->tableId' WHERE group_id='$information->metaContent'"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }else{
                    if($this->InsertQueryIntoDb("UPDATE courses SET hoocked_table_id='$information->tableId' WHERE group_id='$information->metaContent' AND year='$information->period'"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }
            }
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: не удалось получить алгоритм следования, попробуйте повторить операцию снова");
        }
    }
    
    protected function UpdateMetaGlobal($information)
    {
        if($information->algo !== "null")
        {
            if($information->algo == "all")
            {
                if($information->period == "allperiods")
                {
                    if($this->InsertQueryIntoDb("UPDATE courses SET metatext_id='$information->metaId' WHERE 1"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }else{
                    if($this->InsertQueryIntoDb("UPDATE courses SET metatext_id='$information->metaId' WHERE year='$information->period'"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }
            }
            if(preg_match_all("/[0-9]+/", $information->algo, $out))
            {
                if($information->period == "allperiods")
                {
                    if($this->InsertQueryIntoDb("UPDATE courses SET metatext_id='$information->metaId' WHERE type='$information->algo'"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }else{
                    if($this->InsertQueryIntoDb("UPDATE courses SET metatext_id='$information->metaId' WHERE type='$information->algo' AND year='$information->period'"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }
            }
            if($information->algo == "course")
            {
                if($information->period == "allperiods")
                {
                    if($this->InsertQueryIntoDb("UPDATE courses SET metatext_id='$information->metaId' WHERE group_id='$information->metaContent'"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }else{
                    if($this->InsertQueryIntoDb("UPDATE courses SET metatext_id='$information->metaId' WHERE group_id='$information->metaContent' AND year='$information->period'"))
                    {
                        return array("action_type" => "server_response", "observer" => null, "is_success" => true, "message" => "E_CORE: Операция выполнена, успешно.");
                    }else{
                        return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: ошибка бд, операция прервана.");
                    }
                }
            }
        }else{
            return array("action_type" => "server_response", "observer" => null, "is_success" => false, "message" => "E_CORE: не удалось получить алгоритм следования, попробуйте повторить операцию снова");
        }
    }

    private function GetPeriodsList($period_id)
    {
        $result = $this->GetResultFromDb("SELECT * FROM courses WHERE year='$period_id'");
    }

    private function GetCoursesList($query, $output = array())
    {
        $prev = '';
        foreach($query as $key)
        {
            if($prev !== $key["group_id"])
            {
                $output[$key["id"]] = $key;
                $prev = $key["group_id"];
            }
        }
        return $output;
    }
}