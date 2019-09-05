<?php
/**
 * Created by PhpStorm.
 * User: Захар
 * Date: 24.06.2018
 * Time: 14:10
 */

require('controller/token_controller.php');
class cabinet_model extends token_controller
{
    private $table_classes_list = array('table-active', 'table-primary', 'table-success', 'table-info');
    private $table_turn = false;
    public function __construct()
    {parent::__construct(get_user_token());}


    public function cabinet_model($type)
    {
        switch ($type)
        {
            case 'courses_list' : return $this->PrintTableWithFullCoursesListFromDb($this->GetResultFromDb('SELECT courses.group_id , courses.content, courses.status, years.year_content, types.type_content FROM `courses` LEFT JOIN years ON years.ID = courses.year LEFT JOIN types ON courses.type = types.type_id WHERE 1 ORDER BY group_id'));break;
            case 'total_periods' : return $this->PrintPeriodsList($this->GetResultFromDb('SELECT * FROM years WHERE 1'));break;
            case 'tables_control' : return $this->PrintTableControlForAdminPanel($this->GetResultFromDb('SELECT * FROM tables_teplate WHERE 1'));break;
            case 'metatdata_control' : return $this->PrintMetaDateData($this->GetResultFromDb("SELECT * FROM metatexts WHERE 1"));
            case 'user_control' : return $this->PrintUsersData($this->GetResultFromDb("SELECT id,login,role FROM users WHERE 1"));break;
            case 'db_control' : return $this->PrintDbInfo();break;
            default: return 'Unexpected property '.$type.'. Try your query again, or contact to the our support.';
        }
    }

    private function PrintTableWithFullCoursesListFromDb($courses_list, $prevcourse = '',  $output = '')
    {
        $i = 1;
        $year_list = $this->CompileYears($courses_list);
        $output .= '<div style="display:flex;justify-content: center;align-items: center;cursor:pointer;margin-bottom:15px"><div onclick="NewAdd(\'course-add\')"><img src="includes/images/global-add-button.png" class="btn-image"> Новый курс<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></div></div><table class="table"><thead><tr><td>Название курса</td><td>Группа</td><td class="mobile-cell">Статус</td><td></td><td></td></tr></thead><tbody>';
        foreach ($courses_list as $key)
        {
            if(is_array($key))
            {
                if($prevcourse == $key['content'])
                {
                    continue;
                }else{
                    $output .= '<tr class="'.$this->GetTableTurn().'"><td>'.$key['content'].'</td><td>'.$key['type_content'].'</td><td class="mobile-cell">'.$this->GetState($key["status"]).'</td><td><div><span class="glyphicon glyphicon-edit editopengl" data-toggle="tooltip" title="Редактировать курс" alt="Редактировать курс" data-showcourse="'.$key['group_id'].'" onclick="load(this)"  aria-hidden="true"></span></td><td><img src="includes/images/deletepicture.png" class="deleteimage" data-toggle="tooltip" onclick="DeleteObject(\'co\', \''.$key['group_id'].'\', \'courses_list\')" title="Удалить курс"></td></tr>';
                    $prevcourse = $key['content'];
                    $i++;
                }
            }
        }
        return $output.='</tbody></table><div style="margin-bottom: 50px"></div>';
    }

    private function PrintUsersData($query)
    {
        if(!$query || !is_array($query))
        {
            return 'Не удалось получить пользователей из бд. Был сформирован лог ошибки, ознакомится с ним можно здесь.';
        }
        $output = '<div style="display:flex;justify-content: center;align-items: center;cursor:pointer;margin-bottom:15px"><div onclick="NewAdd(\'user-add\')"><img src="includes/images/user-add-button.png" class="btn-image"> Новый пользователь<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></div></div><table border="1" class="table table-striped"><thead><tr><td>Логин</td><td>Роль</td><td></td><td></td></tr></thead><tbody>';
        foreach ($query as $key)
        {
            if($key["role"] == "editor")
            {
                $output .= '<tr><td><span onclick="loadAs(\''.$key["login"].'\')">'.$key["login"].'</span></td><td>'.$key["role"].'</td><td><span class="glyphicon glyphicon-edit editopenus" data-toggle="tooltip" title="Редактировать пользователя" data-showuser="'.$key['id'].'" onclick="load(this)" style="cursor:pointer"></span></td><td><img src="includes/images/deletepicture.png" class="deleteimage" data-toggle="tooltip" onclick="DeleteObject(\'u\', \''.$key['id'].'\', \'user_control\')" title="Удалить пользователя"></td></tr>';
            }else{
                $output .= '<tr><td>'.$key["login"].'</td><td>'.$key["role"].'</td><td><span class="glyphicon glyphicon-edit editopenus" data-toggle="tooltip" title="Редактировать пользователя" data-showuser="'.$key['id'].'" onclick="load(this)" style="cursor:pointer"></span></td><td><img src="includes/images/deletepicture.png" class="deleteimage" data-toggle="tooltip" onclick="DeleteObject(\'u\', \''.$key['id'].'\', \'user_control\')" title="Удалить пользователя"></td></tr>';
            }

        }
        return $output.'</tbody></table><p class="metatextadminpanel" align="center">Для редактирования любого из периодов нажмите на <span class="glyphicon glyphicon-edit" alt="Редактировать курс"  aria-hidden="true"></span>.<br>Удалить пользователя можно в панели редактирования, или нажав соответствующую кнопку.<br> В случае сбоев к работе редактора/таблицы пользователей, перейдите по этой ссылке.</p>';
    }

    private function PrintMetaDateData($query)
    {
        if(!$query || !is_array($query))
        {
            return 'Не удалось получить метатексты из бд. Был сформирован лог ошибки, ознакомится с ним можно здесь.';
        }
        $output = '<div style="display:flex;justify-content: center;align-items: center;cursor:pointer;margin-bottom:15px"><div onclick="NewAdd(\'meta-add\')"><img src="includes/images/global-add-button.png" class="btn-image"> Новый метатекст<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></div></div><table border="1" class="table table-striped"><thead><tr><td>Описание метатекста</td><td>Статус метатекста</td><td></td><td></td></tr></thead><tbody>';
        foreach ($query as $key)
        {
            $output .= '<tr><td>'.$key["metatext_description"].'</td><td>'.$this->GetState($key["status"]).'</td><td><span class="glyphicon glyphicon-edit editopenmt" data-toggle="tooltip" title="Редактировать метатекст" data-showmeta="'.$key['id'].'" onclick="load(this)"></span></td><td><img src="includes/images/deletepicture.png"  class="deleteimage"  data-toggle="tooltip" onclick="DeleteObject(\'m\', \''.$key['id'].'\', \'metatdata_control\')" title="Удалить метатекст"></td></tr>';
        }
        return $output.'</tbody></table><p class="metatextadminpanel" align="center">Для редактирования любого из периодов нажмите на <span class="glyphicon glyphicon-edit" alt="Редактировать курс"  aria-hidden="true"></span>.<br>Удалить период можно в панели редактирования, или нажав соответствующую кнопку.<br> В случае сбоев к работе редактора/таблицы периодов, перейдите по этой ссылке.</p>';
    }


    function PrintPeriodsList($periods_list, $output = '')
    {
        $output = '<div style="display:flex;justify-content: center;align-items: center;cursor:pointer;margin-bottom:15px"><div onclick="NewAdd(\'period-add\')"><img src="includes/images/global-add-button.png" class="btn-image"> Новый период<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></div></div><table border="1" class="table table-striped"><thead><tr><td>Контент периода</td><td>Статус периода</td><td></td><td></td></tr></thead><tbody>';
        foreach ($periods_list as $key)
        {
            $output .= '<tr><td>'.$key['year_content'].'</td><td>'.$this->GetState($key['status']).'</td><td><span class="glyphicon glyphicon-edit editopenpd" data-toggle="tooltip" title="Редактировать период" data-showperiod="'.$key['id'].'" onclick="load(this)"></span></td><td><img src="includes/images/deletepicture.png" class="deleteimage"  data-toggle="tooltip" onclick="DeleteObject(\'pe\', \''.$key['id'].'\', \'total_periods\')" title="Удалить период"></td></tr>';
        }
        return $output .= '</tbody></table><p class="metatextadminpanel" align="center">Для редактирования любого из периодов нажмите на <span class="parent"><span class="glyphicon glyphicon-edit runnner-out" alt="Редактировать период"  aria-hidden="true"></span></span>.<br>Удалить период можно в панели редактирования, или нажав соответствующую кнопку.<br> В случае сбоев к работе редактора/таблицы периодов, перейдите по этой ссылке.</p>';
    }
    private  function CompileYears($dbarray, $outputarr = array())
    {
        foreach($dbarray as $key)
        {
            if(is_array($key))
            {
                if(!isset($outputarr[$key['content']]))
                {
                    $outputarr[$key['content']] = $key["year_content"]." ";
                }else{
                    $outputarr[$key['content']] .= $key["year_content"]." ";
                }
            }
        }
        return $outputarr;
    }

    private function GetState($status)
    {
        if($status)
        {
            return '<p style="margin-top:7px">Активен</p>';
        }
        return '<p style="margin-top:7px">Неактивен</p>';
    }

    private function PrintTableControlForAdminPanel($table_list, $output = '')
    {
        $output = '<div style="display:flex;justify-content: center;align-items: center;cursor:pointer;margin-bottom:15px"><div onclick="NewAdd(\'table-add\')"><img src="includes/images/global-add-button.png" class="btn-image"> Новая таблица<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></div></div><table border="1" class="table table-striped"><thead><tr><td>Контент таблицы</td><td>Статус таблицы</td><td></td><td></td></tr></thead><tbody>';
        foreach ($table_list as $table)
        {
            $output .= '<tr><td>'.$table['table_description'].'</td><td>'.$this->GetState($table['table_status']).'</td><td><span class="glyphicon glyphicon-edit editopentb" data-showtable="'.$table['table_id'].'" data-toggle="tooltip" aria-hidden="true" title="Редактировать шаблон таблицы" alt="Редактировать шаблон таблицы" onclick="load(this)"></span></td><td><img src="includes/images/deletepicture.png" class="deleteimage"  data-toggle="tooltip" onclick="DeleteObject(\'t\', \''.$table['table_id'].'\', \'tables_control\')" title="Удалить таблицу"></td></tr>';
        }
        return $output .= '</tbody></table>';
    }

    private function PrintDbInfo()
    {
        include('includes/backups/core/backup_file.php');
        $backup = new backup_file();
        $data = $backup->GetSettingsFile()->parametrs;
        ?>
        <div id="database_control_place">
            <?php if($_SESSION['role'] == 'administrator') { ?>
            <div id="dbConnectionProp" onclick="ChangeVisibility('dbConnectionPropList')" class="dbControl">Изменить настройки подключения к бд</div>
                <div id="dbGetLastFile" onclick="window.open('cabinet/?type=GetLastDbFile&auth=true$dinamycData=resource')" class="dbControl">Скачать последнюю версию бд</div>
            <?php } ?>
            <div id="dbRollbackProp" onclick="ChangeVisibility('restore-controls-place')" class="dbControl">Восстановление бд</div>
            <div id="restore-controls-place" style="display: none">
                <button class="modalbtn" id="restoreDbLocal" onclick="RollbackLastDbVersion('local')"><span id="r-f">Восстановить из локальных файлов</span><span id="btn-f---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button><br>
                <input type="file" id="f-file">
                <button class="modalbtn" id="restoreDbFile" onclick="RollbackLastDbVersion('file')"><span id="r-l">Восстановить из загруженого файла</span><span id="btn-l---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                <div id="server-answers-db" class="error"></div>
            </div>


        <?php if($_SESSION['role'] == 'administrator') { ?>
            <div id="dbConnectionPropList" class="dbPropList" style="display: none">
                <div id="ob---db-connections">
                    <p class="m--custom">Настройка хоста бд.</p>
                    <input type="text" style="height: 40px; border-radius: 4px" class="text-field" id="dbHost" value="<?php echo $data->db_host; ?>" disabled><br>
                    <label><input type="checkbox" onclick="ChangeState('dbHost')" id="dbHostAuto" checked>Определить хост автоматически</label>
                    <p class="m--custom">Настройка логина бд.</p>
                    <input type="text" style="height: 40px; border-radius: 4px" class="text-field" id="dbLogin" value="<?php echo $data->db_login; ?>" ><br>
                    <p class="m--custom">Настройка пароля бд.</p>
                    <input type="text" style="height: 40px; border-radius: 4px" class="text-field" id="dbPass" value="<?php echo $data->db_password; ?>" ><br>
                    <p class="m--custom">Настройка имени бд.</p>
                    <input type="text" style="height: 40px; border-radius: 4px" class="text-field" id="dbName" value="<?php echo $data->db_name; ?>" ><br>

                    <button class="btn-btn" onclick="ChangeDbSettings()">Применить</button>
                    <p id="msgs"></p>
                </div>
            </div>
        <?php } ?>
            <div id="dbRollbackPropList" class="dbPropList" style="display: none">

            </div>
        </div>
        <?php
    }

    function GetRandomClassForTable()
    {
        return $this->table_classes_list[array_rand($this->table_classes_list, 1)];
    }

    function GetTableTurn()
    {
        if($this->table_turn)
        {
            $this->table_turn = !$this->table_turn;
            return 'special--row';
        }
        if(!$this->table_turn)
        {
            $this->table_turn = !$this->table_turn;
            return 'usual';
        }
    }
}