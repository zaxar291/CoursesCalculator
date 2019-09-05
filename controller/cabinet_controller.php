<?php
/**
 * Created by PhpStorm.
 * User: Захар
 * Date: 24.06.2018
 * Time: 14:09
 */

if (!function_exists("is_availible")) {
    header("Location: 403");
}
include(get_parent_dir() . "/model/" . $routes . "_model.php");

class cabinet_controller extends cabinet_model
{
    private $action;

    public function __construct()
    {
        parent::__construct();
    }

    public function user_auth()
    {
        if (!$_SESSION) {
            return 0;
        }
        if (!$_SESSION["auth"]) {
            return 0;
        }
        if (!isset($_SESSION["user_token"])) {
            return 0;
        }
        set_user_login($_SESSION["login"]);
        $token = new token_controller($_SESSION["user_token"]);
        if ($token->check_token() and $_SESSION["auth"]) {
            return 1;
        }else{
            return 0;
        }
    }

    public function GetHoockedTables()
    {
        $result = $this->GetResultFromDb("SELECT table_id, table_description FROM tables_teplate WHERE table_status='1'");
        if(!$result)
        {
            return '<option value="null">Ошибка выделения таблиц отображения, убедитесь, что есть хотя бы одна активная таблица вывода</option>';
        }
        $output = '';
        foreach($result as $key)
        {
            $output .= '<option value="'.$key["table_id"].'">'.$key["table_description"].'</option>';
        }
        return $output;
    }
    public function GetMetatexts()
    {
        $result = $this->GetResultFromDb("SELECT id, metatext_description FROM metatexts WHERE status='1'");
        if(!$result)
        {
            return '<option value="null">Не выбрано</option>';
        }
        $output = '<option value="null">Не выбрано</option>';
        foreach($result as $key)
        {
            $output .= '<option value="'.$key["id"].'">'.$key["metatext_description"].'</option>';
        }
        return $output;
    }

    public function GetCourses()
    {
        $result = $this->GetResultFromDb("SELECT group_id, content FROM courses WHERE status='1'");
        $prev = '';
        if(!$result)
        {
            return '<option value="null">Курсы не найдены</option>';
        }
        $output = '<option value="null">Не выбрано</option>';
        foreach ($result as $key)
        {
            if($prev !== $key["group_id"])
            {
                $output .= '<option value="'.$key["group_id"].'">'.$key["content"].'</option>';
                $prev = $key["group_id"];
            }

        }
        return $output;
    }

    public function GetPeriods()
    {
        $result = $this->GetResultFromDb("SELECT id,year_content FROM years WHERE status='1'");
        if(!$result)
        {
            return '<option value="null">Не найдено ни одного досутпного периода</option>';
        }
        $output = '<option value="null">---Не выбран--</option>';
        foreach($result as $key)
        {
            $output .= '<option value="'.$key["id"].'">'.$key["year_content"].'</option>';
        }
        return $output;
    }


    public function GetTypes()
    {
        $result = $this->GetResultFromDb("SELECT type_id,type_description FROM types WHERE 1");
        if(!$result)
        {
            return '<option value="null">Не найдено ни одного типа</option><option value="addnew">Добавить новый тип</option>';
        }
        $output = '<option value="null">---Не выбран--</option>';
        foreach($result as $key)
        {
            $output .= '<option value="'.$key["type_id"].'">'.$key["type_description"].'</option>';
        }
        return $output.'<option value="addnew">Добавить новый тип</option>';
    }

    public function JSON_GetYears()
    {
        $result = $this->GetResultFromDb("SELECT id,year_content FROM years WHERE status='1'");
        $output = array();
        foreach($result as $key)
        {
            $output[$key["id"]] = array("id" => $key["id"], "content" => $key["year_content"]);
        }
        return json_encode($output);
    }

    public function AdderControl($ngModel)
    {
        switch($ngModel->action)
        {
            case 'newuser' : return $this->AddUser($ngModel);break;
            case 'newmeta' : return $this->AddMeta($ngModel);break;
            case 'newperiod' : return $this->AddPeriod($ngModel);break;
            case 'newtable' : return $this->AddTable($ngModel);break;
            case 'newtype' : return $this->AddType($ngModel);break;
            case 'newcourse' : return $this->AddCourse($ngModel);break;
            default: return array("message_type" => "server_response", "is_success" => false, "content" => "Неизвестный аргумент получен, оперцации прерваны.");
        }
    }

    public function DeleterControl($type, $id)
    {
        switch($type)
        {
            case "u": return $this->DeleteUser($id);break;
            case "m": return $this->DeleteMeta($id);break;
            case "pe" : return $this->DeletePeriod($id);break;
            case "t" : return $this->DeleteTable($id);break;
            case 'co' : return $this->DeleteCourses($id);break;
        }
    }

    private function AddMeta($data)
    {
        if($this->InsertQueryIntoDb("INSERT INTO `metatexts`(`metatext_content`, `metatext_description`, `status`) VALUES ('".$data->content."', '".$data->description."', '".$data->isActive."')"))
        {
            return array("message_type" => "server_response", "is_success" => true, "content" => "Метатекст был успешно добавлен!");
        }else{
            return array("message_type" => "server_response", "is_success" => false, "content" => "Ошибка при подключении к бд, повторите свою попытку позже.");
        }
    }

    private function AddTable($data)
    {
        if($this->InsertQueryIntoDb("INSERT INTO `tables_teplate`(`table_content`, `table_description`, `table_status`)  VALUES ('".$data->content."', '".$data->description."', '".$data->isActive."')"))
        {
            return array("message_type" => "server_response", "is_success" => true, "content" => "Таблица была успешно добавлен!");
        }else{
            return array("message_type" => "server_response", "is_success" => false, "content" => "Ошибка при подключении к бд, повторите свою попытку позже.");
        }
    }

    private function AddUser($data)
    {
        if(!empty($this->GetResultFromDb("SELECT id FROM users WHERE login='".$data->userName."'")))
        {
            return array("message_type" => "server_response", "is_success" => false, "content" => "Такой пользователь уже существует!");
        }
        if($this->InsertQueryIntoDb("INSERT INTO `users`(`login`, `pass`, `role`) VALUES ('".$data->userName."', '".password_hash($data->userPass, PASSWORD_DEFAULT)."', '".$data->userRole."')"))
        {
            return array("message_type" => "server_response", "is_success" => true, "content" => "Пользователь был успешно добавлен!");
        }else{
            return array("message_type" => "server_response", "is_success" => false, "content" => "Ошибка при подключении к бд, повторите свою попытку позже.");
        }
    }

    private function AddPeriod($data)
    {
        if($this->InsertQueryIntoDb("INSERT INTO `years`(`year_content`, `status`) VALUES ('".$data->content."', '".$data->isActive."')"))
        {
            return array("message_type" => "server_response", "is_success" => true, "content" => "Метатекст был успешно добавлен!");
        }else{
            return array("message_type" => "server_response", "is_success" => false, "content" => "Ошибка при подключении к бд, повторите свою попытку позже.");
        }
    }

    private function AddType($data)
    {
        if($this->InsertQueryIntoDb("INSERT INTO types (type_content, type_description) VALUES ('".$data->content."', '".$data->content."')"))
        {
            return array("message_type" => "server_response", "is_success" => true, "content" => "Тип добавлен, идёт обновление списка");
        }else{
            return array("message_type" => "server_response", "is_success" => false, "content" => "Ошибка добавления типа, бд оффлайн");
        }
    }

    private function AddCourse($data)
    {
        if(!$data->periods)
        {
            return array("message_type" => "server_response", "is_success" => false, "content" => "E_CORE compile error, operation abort");
        }
        $msg = array();
        $groupId = get_group_id();
        foreach($data->periods as $key)
        {
            if(isset($key->id))
            {
                if($this->InsertQueryIntoDb("INSERT INTO `courses`(`year`, `content`, `type`, `price`, `total`, `minimum`, `maximum`, `discount`, `status`, `hoocked_table_id`, `metatext_id`, `group_id`) VALUES ('$key->id', '$data->coursename', '$data->type', '$data->price', '$data->total', '$data->minClass', '$data->maxClass', '$data->discount', '1', '$data->tableTemplate', '$data->metaText', '$groupId')"))
                {
                    $msg =  array("message_type" => "server_response", "is_success" => true, "content" => "Курс добавлен, идёт обновление списка");
                }else{
                    $msg =  array("message_type" => "server_response", "is_success" => false, "content" => "Курс не добавлен, ошибка связи с бд.");
                }
            }
        }
        return $msg;
    }

    public function GetTotalCoursesListFromDataBase()
    {
       return $this->cabinet_model('courses_list');
    }

    private function DeleteUser($id)
    {
        if($this->isLastUser())
        {
            return array("is_success" => 'false');
        }
        if($this->InsertQueryIntoDb("DELETE FROM users WHERE id='$id'"))
        {
            return array("is_success" => 'true');
        }else{
            return array("is_success" => 'false');
        }
    }

    private function DeleteMeta($id)
    {
        if($this->InsertQueryIntoDb("DELETE FROM metatexts WHERE id='$id'"))
        {
            return array("is_success" => 'true');
        }else{
            return array("is_success" => 'false');
        }
    }

    private function isLastUser()
    {
        $count = 0;
        $result = $this->GetResultFromDb("SELECT * FROM users WHERE 1");
        foreach ($result as $user)
        {
            $count++;
        }
        if($count == 1)
        {
            return true;
        }
        return false;
    }

    private function DeletePeriod($id)
    {
        if($this->InsertQueryIntoDb("DELETE FROM years WHERE id='$id'"))
        {
            return array("is_success" => 'true');
        }else{
            return array("is_success" => 'false');
        }
    }

    private function DeleteTable($id)
    {
        if($this->InsertQueryIntoDb("DELETE FROM tables_teplate WHERE table_id='$id'"))
        {
            return array("is_success" => 'true');
        }else{
            return array("is_success" => 'false');
        }
    }

    private function DeleteCourses($id)
    {
        if($this->InsertQueryIntoDb("DELETE FROM courses WHERE group_id='$id'"))
        {
            return array("is_success" => 'true');
        }else{
            return array("is_success" => 'false');
        }
    }

    public function GetTemplateForCourse($course_id, $output = array("content" => "", "years" => array("allperiods" => null, "injectedPeriods" => null), "types" => array("selected_type" => "", "alltypes" => array()), "price" => 0, "discount" => 0, "total" => 0, "minimum" => 0, "maximum" => 0, "hoocked_table" => array("selected_table" => "", "alltables" => array()), "metatexts" => array("selected_metatext" => "", "allmetatexts" => array())))
    {
        $result = $this->GetResultFromDb("SELECT * FROM courses WHERE group_id='$course_id'");
        if(!$result || !is_array($result))
        {
            return array("message_type" => "server_response", "is_success" => false, "message" => "Такой курс не найден в бд, попробуйте перезагрузить страницу и повторить попытку создания снова");
        }
        $prev = '';
        foreach ($result as $key)
        {
            if($prev !== $key["group_id"])
            {
                if(isset($key["content"]))
                {
                    $output["content"] = $key["content"];
                }else{
                    $output["content"] = "#parseError";
                }
                $output["years"]["allperiods"] = $this->JSON_GetYears();
                $output["years"]["injectedPeriods"] = $this->GetYearsByGroupId($key["group_id"]);
                $output["types"]["selected_type"] = $key["type"];
                $output["types"]["alltypes"] = $this->JSONGetTypes($key["type"]);
                if(isset($key["price"]))
                {
                    $output["price"] = $key["price"];
                }else{
                    $output["price"] = -1;
                }
                if(isset($key["discount"]))
                {
                    $output["discount"] = $key["discount"];
                }else{
                    $output["discount"] = -1;
                }
                if(isset($key["total"]))
                {
                    $output["total"] = $key["total"];
                }else{
                    $output["total"] = -1;
                }
                if(isset($key["minimum"]))
                {
                    $output["minimum"] = $key["minimum"];
                }else{
                    $output["minimum"] = -1;
                }
                if(isset($key["maximum"]))
                {
                    $output["maximum"] = $key["maximum"];
                }else{
                    $output["maximum"] = -1;
                }
                $output["hoocked_table"]["selected_table"] = $key["hoocked_table_id"];
                $output["hoocked_table"]["alltables"] = $this->JSONGetHoockedTables($key["hoocked_table_id"]);
                $output["metatexts"]["selected_metatext"] = $key["metatext_id"];
                $output["metatexts"]["allmetatexts"] = $this->JSONGetMetatexts($key["metatext_id"]);

            }
            $prev = $key["group_id"];
        }
        return $output;
    }

    private function GetYearsByGroupId($group_id)
    {
        $result = $this->GetResultFromDb("SELECT year FROM courses WHERE group_id='$group_id'");
        if(!$result)
        {
            return null;
        }
        $output = array();
        foreach($result as $key)
        {
            $output[$key["year"]] = array("id" => $key["year"]);
        }
        return $output;
    }

    private function JSONGetTypes($selected)
    {
        $result = $this->GetResultFromDb("SELECT type_id,type_description FROM types WHERE 1");
        if(!$result)
        {
            return array("type_status" => false);
        }
        $output = array("type_status" => true, "on::null" => "--Не выбран--", "on::add" => "Новый тип", "types" => array());
        foreach($result as $key)
        {
            if($key["type_id"] == $selected)
            {
                $output["types"][$key["type_id"]] = array("id" => $key["type_id"], "description" => $key["type_description"], "attr" => "selected");
            }else{
                $output["types"][$key["type_id"]] = array("id" => $key["type_id"], "description" => $key["type_description"], "attr" => null);
            }
        }
        return $output;
    }

    private function JSONGetHoockedTables($selected)
    {
        $result = $this->GetResultFromDb("SELECT table_id, table_description FROM `tables_teplate` WHERE table_status='1'");
        if(!$result)
        {
            return array("table_status" => false);
        }
        $output = array("table_status" => true, "on::null" => "--Не выбрана--", "tables" => array());
        foreach($result as $key)
        {
            if($key["table_id"] == $selected)
            {
                $output["tables"][$key["table_id"]] = array("id" => $key["table_id"], "description" => $key["table_description"], "attr" => "selected");
            }else{
                $output["tables"][$key["table_id"]] = array("id" => $key["table_id"], "description" => $key["table_description"], "attr" => null);
            }
        }
        return $output;
    }

    private function JSONGetMetatexts($selected)
    {
        $result = $this->GetResultFromDb("SELECT id, metatext_description FROM `metatexts` WHERE status='1'");
        if(!$result)
        {
            return array("metatext_status" => false);
        }
        $output = array("metatext_status" => true, "on::null" => "--Не выбран--", "metatexts" => array());
        foreach($result as $key)
        {
            if($key["id"] == $selected)
            {
                $output["metatexts"][$key["id"]] = array("id" => $key["id"], "description" => $key["metatext_description"], "attr" => "selected");
            }else{
                $output["metatexts"][$key["id"]] = array("id" => $key["id"], "description" => $key["metatext_description"], "attr" => null);
            }
        }
        return $output;
    }
}