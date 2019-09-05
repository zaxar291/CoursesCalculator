<?php
/**
 * Created by PhpStorm.
 * User: DNAPC
 * Date: 20.06.2018
 * Time: 12:08
 */
if(!function_exists("is_availible")){header("Location: 403");}
include(get_parent_dir()."/model/".$routes."_model.php");
class calculator_controller extends calculator_model
{
    private $logs;
    public function __construct()
    {
        get_template_part("/services/log.service.php");
        $this->logs = new LogService($_GET["sess"]);
        parent::__construct('');
    }

    public function make_result()
    {
        $logs = new LogService($_GET["sess"]);
        $logs->WriteSiteLog("Обращение к ядру калькуляций.... начато");
        if($_GET["action_type"] == "year")
        {
            $logs->WriteSiteLog("Пользователь запросил список ддоступных периодов.");
            $logs->WriteLog("Пользователь выбрал год для калькуляции");
            return $this->GetYears();
        }
        if($_GET["action_type"] == "classe")
        {
            $logs->WriteSiteLog("Пользователь запросил список доступных классов.");
            $logs->WriteLog("Пользователь выбрал класс для калькуляции");
            return $this->GetListClasses();
        }
        if($_GET["action_type"] == "course")
        {
            $logs->WriteSiteLog("Пользователь запросил список доступных курсов.");
            $logs->WriteLog("Пользователь выбрал курс для калькуляции");
            return $this->GetCourses();
        }
        if($_GET["action_type"] == "countcourses")
        {
            $logs->WriteSiteLog("Пользователь запросил результат.");
            $logs->WriteLog("Пользователь получил результат калькуляции");
            return $this->CountResult();
        }
    }

    private function GetYears($output = array("message_type" => "", "object_type" => "years", "on:null" => "--Выберите период--", "parammetrs" => array("showYears" => true, "showClasses" => false, "showCourses" => false, "showResult" => false), "content" => array()))
    {
        $this->logs->WriteSiteLog("Начинаю запрос списка досутпных периодов для пользователя...");
        if($this->get_result_from_db("SELECT * FROM years WHERE status='1' ORDER BY year_content"))
        {
            $this->logs->WriteSiteLog("Список получен, формирую вывод информации...");
                foreach($this->get_result() as $key)
                {
                    array_push($output["content"], array("id" => $key["id"], "content" => $key["year_content"]));
                }
                $output["message_type"] = "success";
                $this->logs->WriteSiteLog("Информация сгенерирована, завершено успешно.");
                return $output;
        }else{
            $this->logs->WriteSiteLog("<p style='color:red'>Произошла ошибка в процессе выделения списка доступных периодов, вероятная причина ошибки - ".$this->get_db_log()."</p>");
            return array("message_type" => "error", "message_content" => "Ошибка базы данных, повторите попытку калькуляции позже, или свяжитесь с техничекой поддержкой для получения более детальной информации", "parammetrs" => array("showYears" => false, "showClasses" => false, "showCourses" => true, "showResult" => false));
        }
    }

    private function GetListClasses($output = array("message_type" => "", "object_type" => "classes", "on:null" => "--Выберите класс--", "parammetrs" => array("showYears" => true, "showClasses" => true, "showCourses" => false, "showResult" => false), "content" => array()))
    {
        $this->logs->WriteSiteLog("Начинаю запрос списка досутпных классов для пользователя...");
        if($this->get_result_from_db("SELECT * FROM classes WHERE status='1' ORDER BY id"))
        {
            $this->logs->WriteSiteLog("Список классов получен, обработка информации...");
            foreach($this->get_result() as $key)
            {
                $output["content"][$key["id"]] = array("id" => $key["id"], "content" => $key["content"]);
            }
            $output["message_type"] = "success";
            $this->logs->WriteSiteLog("Обработка завершена.");
            return $output;
        }else{
            $this->logs->WriteSiteLog("<p style='color:red'>Произошла ошибка в процессе выделения списка доступных классов, вероятная причина ошибки - ".$this->get_db_log()).'</p>';
            return array("message_type" => "error", "message_content" => "Ошибка базы данных, повторите попытку калькуляции позже, или свяжитесь с техничекой поддержкой для получения более детальной информации", "parammetrs" => array("showYears" => true, "showClasses" => false, "showCourses" => false, "showResult" => false));
        }
    }

    private function GetCourses($courses = array("message_type" => "", "object_type" => "courses", "on:null" => "--Выберите курс--", "parammetrs" => array("showYears" => true, "showClasses" => true, "showCourses" => true, "showResult" => false)))
    {
        $this->logs->WriteSiteLog("Начинаю запрос списка досутпных курсов для пользователя...");
        if($this->get_result_from_db("SELECT * FROM courses LEFT JOIN types ON courses.type = types.type_id WHERE year='".$_GET["year"]."' AND minimum <= '".$_GET["meta"]."' AND maximum >= '".$_GET["meta"]."' AND status='1'"))
        {
            $this->logs->WriteSiteLog("Список получен, идёт формирование результата...");
            foreach ($this->get_result() as $key)
            {
                if(!isset($courses[$key["type_content"]]))
                {
                    $courses[$key["type_content"]] = array("label" => $key["type_content"], 'courses' => array());
                }

                $courses[$key["type_content"]]["content"][$key["id"]] = array("course_id" => $key["group_id"], "course_content" => $key["content"], "label" => $key["type_content"]);
            }
            $courses["message_type"] = "success";
            $this->logs->WriteSiteLog("Результат сформирован, завершено");
            return $courses;
        }else{
            $this->logs->WriteSiteLog("<p style='color:red;display:inline'>Произошла ошибка в процессе выделения списка доступных курсов, вероятная причина ошибки - ".$this->get_db_log()."</p>");
            return array("message_type" => "error", "message_content" => "По вашему классу не найдено ни одного курса", "parammetrs" => array("showYears" => true, "showClasses" => true, "showCourses" => false, "showResult" => false));
        }
    }

    private function CountResult()
    {
        $this->logs->WriteSiteLog("Начинаю расчёт курса...");
        if(!$_GET['course'])
        {
            $this->logs->WriteSiteLog("<p style='color:red'>Ошибка, отправлен пустой курс, работа прекращена.</p>");
            return array("message_type" => "error", "message_content" => "E_CORE: Пустой курс отправлен, работа прекращена");
        }
        $this->logs->WriteSiteLog("Запрос данных из бд..");
        if(set_output_db_result_into_array($this->GetResultFromDb("SELECT * FROM `courses`  LEFT JOIN tables_teplate ON tables_teplate.table_id = courses.hoocked_table_id LEFT JOIN metatexts ON metatexts.id = courses.metatext_id WHERE courses.group_id='".decode_special_string($_GET["course"])."' AND year='".$_GET["year"]."'")))
        {
            $this->logs->WriteSiteLog("Анализ данных, выбраных пользователем - Курс:".get_course_name()).'';
			require ("controller/hooks.php");
			$hook = new hooks_worker();
            $this->logs->WriteSiteLog("Расшифровка курса.");
            return "<br><p class='option-p-text'>Результат расчёта предоставлен ниже в виде таблицы</p>".$hook->hook_controller("decode").'<p align="center" id="metatext">'.get_output_db_metatext_from_decode_core().'</p>';
        }else{
            return array("message_type" => "error", "message_content" => "Критическая ошибка в процессе расчёта: пустой результат из бд, операции прерваны.");
        }

    }

    private function Calc($type, $total, $price, $discount)
    {
        if($type == "total")
        {
            return $total * $price - $discount;
        }
        if($type == "onefour")
        {
            return $total * $price / 4;
        }
    }
}