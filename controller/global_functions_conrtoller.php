<?php
/**
 * Created by PhpStorm.
 * User: Захар
 * Date: 12.06.2018
 * Time: 21:30
 */
$output;
$save;
$dir;
$db_result;
$login;
function is_availible()
{
}

function get_page_error()
{
    include(get_parent_dir() . "/view/403.php");
}

function get_404_page()
{
    include(get_parent_dir() . "/view/404.php");
}

function decode_special_string($string)
{
    return str_ireplace("%3", "+", $string);
}

function get_file_from_array($array, $output = '')
{
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                get_file_from_array($value);
            } else {
                save_into_var($value);
            }
        }
    } else {
        return get_saved_var();
    }
    return get_saved_var();
}

function save_into_var($v)
{
    global $save;
    $save = $v;
}

function get_saved_var()
{
    global $save;
    return $save;
}

function clear_file_from_php($file)
{
    return str_replace('.php', '', $file);
}

function set_parent_dir($directory)
{
    global $dir;
    $dir = $directory;
}

function get_parent_dir()
{
    global $dir;
    return $dir;
}

function get_header()
{
    global $dir;
    if (is_file($dir . "/includes/header.php")) {
        include($dir . "/includes/header.php");
    } else {
        create_error(' файл хедера не найден!');
    }

}

function get_footer()
{
    global $dir;
    if (is_file($dir . "/includes/footer.php")) {
        include($dir . "/includes/footer.php");
    } else {
        create_error(' файл футера не найден!');
    }
}

function get_nagivation_menu()
{
    global $dir;
    if (is_file($dir . "/includes/menu.php")) {
        include($dir . "/includes/menu.php");
    } else {
        create_error(' файл навигационного меню не найден!');
    }
}

function create_error($message)
{
    $file = fopen("includes/backups/core/logs/logs.html", "a+");
    $file_content = fread($file, filesize("includes/backups/core/logs/logs.html"));
    fwrite($file, $file_content . "\n [" . date("Y-m-d h:i:s") . "] - ($message)\n");
}

function php_get_controllers()
{
    include(get_parent_dir() . "/controller/db_controler.php");
}

function clear($string)
{
    return strip_tags(nl2br(htmlspecialchars($string)));
}

function get_admin_panel()
{
    include('includes/admin_panel.php');
}

function set_output_db_result_into_array($result)
{
    $logs = new LogService($_GET["sess"]);
    $logs->WriteSiteLog("Результат получен, начинаю заполнение массива данных...");
    global $db_result;
    if (isset($result["0"])) {
        $db_result = $result["0"];
        $logs->WriteSiteLog("Массив данных успешно заполен.");
        return 1;
    } else {
        $logs->WriteSiteLog("<p style='color:red'>Массив данных не был заполен, работа прекращена</p>.");
        return 0;
    }

}

function get_output_db_table_for_decode_core()
{
    global $db_result;
    return $db_result["table_content"];
}

function get_output_db_metatext_from_decode_core()
{
    global $db_result;
    return str_ireplace(".", "<br>", $db_result["metatext_content"]);
}

function get_course_name()
{
    global $db_result;
    return (isset($db_result["content"])) ? $db_result["content"] : "";
}

function get_course_total()
{
    global $db_result;
    return (isset($db_result["total"])) ? $db_result["total"] : "";
}

function get_course_price()
{
    global $db_result;
    return (isset($db_result["price"])) ? $db_result["price"] : "";
}

function get_course_full_price()
{
    return get_course_price() * get_course_total();
}

function get_course_quater_price()
{
    return get_course_price() * get_course_total() / 4;
}

function get_course_half_price()
{
    return get_course_price() * get_course_total() / 4;
}

function get_course_price_for_one_lesson()
{
    return get_course_price() + 50;
}

function get_course_oneeight_price()
{
    return (get_course_price() + 25) * get_course_total() / 8;
}

function set_user_login($log)
{
    global $login;
    $login = $log;
}

function get_user_login()
{
    global $login;
    return $login;
}

function get_user_token()
{
    if (!isset($_SESSION["user_token"])) {
        return 0;
    }
    return $_SESSION["user_token"];
}

function get_image($imagename)
{
    return 'includes/images/' . $imagename;
}

function session_role_valid()
{
    if ($_SESSION['role'] == 'administrator' or $_SESSION['role'] == 'editor') {
        return 1;
    }
    return 0;
}

function get_modal_place_for_admin_panel()
{
    return get_parent_dir() . '/includes/modal.php';
}

function rscandir($base = '', &$data = array())
{
    $array = array_diff(scandir($base), array('.', '..'));
    foreach ($array as $value) {
        if (is_dir($base . $value)) {
            $data[] = $base . $value . '/';
            $data = rscandir($base . $value . '/', $data);
        } elseif (is_file($base . $value)) {
            $data[] = $base . $value;
        }
    }
    return $data;
}

function get_database_file()
{
    return json_decode(file_get_contents('includes/backups/core/configs/admin/database/package.json'));
}

function get_template_part($filename)
{
    global $dir;
    if (is_file($dir . $filename)) {
        include($dir . $filename);
    } else {
        create_error('файл по адресу ' . $dir . $filename . ' не найден ');
    }
}

function data_base_loaded()
{
    $settings = get_database_file()->parametrs;
    if (!mysqli_connect($settings->db_host, $settings->db_login, $settings->db_password, $settings->db_name)) {
        return 0;
    } else {
        return 1;
    }
}

function get_auth_form()
{
    require 'includes/auth_form.php';
}

function get_sess_id()
{
    $id = json_decode(file_get_contents("includes/backups/core/storage/SESSID.json"));
    file_put_contents("includes/backups/core/storage/SESSID.json", json_encode(array("SESSID" => $id->SESSID + 1)));
    return $id->SESSID;
}

function get_group_id()
{
    $id = json_decode(file_get_contents("includes/backups/core/storage/GROUPID.json"));
    file_put_contents("includes/backups/core/storage/GROUPID.json", json_encode(array("GROUPID" => $id->GROUPID + 1)));
    return $id->GROUPID;
}

function Compare($observable, $observer, $comparer)
{
    foreach ($observer as $key) {
        if ($key[$comparer] == $observable) {
            return 1;
        }
    }
    return 0;
}

function _getSiteStateJson()
{

}