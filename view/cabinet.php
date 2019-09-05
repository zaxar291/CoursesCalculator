<?php if(!function_exists("get_header")){header("Location: 404");}
    session_start();
if(isset($_GET["debug"]) and isset($_GET["atype"]))
{
    if($_GET["debug"] == true && $_GET["atype"] == "rollback")
    {
        $previnfo = json_decode($_SESSION["previnfo"]);
        $_SESSION["user_token"] = $previnfo->userToken;
        $_SESSION["login"] = $previnfo->userLogin;
        $_SESSION["role"] = 'administrator';
        header("Location: cabinet");
    }
}
if(isset($_GET["action"]))
{
    if($_GET["action"] == "logout")
    {
        $_SESSION["auth"] = "";
        $_SESSION["login"] = "";
        $_SESSION["role"] = "";
        header("Location: cabinet");
    }
}

if(isset($_GET["get_admin_cabinet_part"]) and isset($_GET["auth_token"]))
{
    set_user_login($_SESSION["login"]);
    $cabinet = new cabinet_controller();
    if($cabinet->check_token())
    {
        echo $cabinet->cabinet_model($_GET["get_admin_cabinet_part"]);
    }else{
        echo 'Permission denied for '.(isset($_SESSION["login"])) ? $_SESSION['login'] : 'user';
    }
    exit();
}
if(isset($_FILES))
{
    if(isset($_FILES["dbFile"]))
    {
        $file = $_FILES["dbFile"];
        $temp = $file["tmp_name"];
        if(file_exists($temp))
        {
            if(preg_match("/[A-Za-z0-9_?-?]/", $file["name"], $out))
            {
                if(move_uploaded_file($temp, "includes/backups/core/storage/".$file["name"]))
                {
                    get_template_part("/services/local.service.php");
                    $local = new Local();
                    $local->UpdateFileInFileSettings("includes/backups/core/storage/".$file["name"]);
                    echo json_encode($local->RestoreLocalDbCopy());
                }else{
                    echo json_encode(array("message_type" => "server_response", "is_success" => false, "message" => "Ошибка файл, ".$file["name"] . " не может быть перемещён в папку "."includes/backups/core/storage/".$file["name"]));
                }
            }else{
                echo json_encode(array("message_type" => "server_response", "is_success" => false, "message" => "Ошибка файл, ".$file["name"] . " не является файлом формата .json!"));
            }
        }else{
            echo json_encode(array("message_type" => "server_response", "is_success" => false, "message" => "Ошибка закачки файла, ".$file["error"]));
        }
        exit;
    }
}
if(isset($_GET["type"]))
{
    if($_GET["type"] == "loadAsEditor")
    {
        set_user_login($_SESSION["login"]);
        $token = new token_controller(get_user_token());
        if($token->check_token())
        {
            $_SESSION["previnfo"] = json_encode(array("userLogin" => get_user_login(), "userToken" => get_user_token()));
            set_user_login($_GET["name"]);
            $_SESSION["user_token"] = $token->get_token();
            $_SESSION["login"] = $_GET["name"];
            $_SESSION["role"] = 'editor';
            echo json_encode(array("is_success" => true, "content" => "cabinet?debug=true&objectType=editor&authHash=".md5(uniqid(rand(), true))."&sessid=".rand()));
        }else{
            echo json_encode(array("is_success" => false, "content" => "Доступ к этой операции запрещён, отказано."));
        }
        exit;
    }
    if($_GET["type"] == "authmethod")
    {
        if($_GET["dbState"] == "0")
        {
            get_template_part("/services/local.service.php");
            $local = new Local();
            if($local->ValidateUser($_GET["login"], $_GET["pass"]))
            {
                echo json_encode(array("message_type" => "server_response", "action_type" => "validate_user", "access" => true, "local" => true));
                return;
            }else{
                echo json_encode(array("message_type" => "server_response", "action_type" => "validate_user", "access" => false, "local" => true));
                return;
            }
        }else{
            require('controller/user_controller.php');
            $cabinet = new user_controller(clear($_GET["login"]), clear($_GET["pass"]));
            if($cabinet->is_user())
            {
                echo  json_encode(array("message_type" => "server_response", "action_type" => "validate_user", "access" => true, "url" => "cabinet?token=".$cabinet->create_token(), "local" => false));
                exit();
            }else{
                echo  json_encode(array("message_type" => "server_response", "action_type" => "validate_user", "access" => false, "url" => "null", "local" => false));
                exit();
            }
        }
    }
    if($_GET["type"] == "updatedbfile")
    {
        get_template_part("/services/local.service.php");
        $local = new Local();
        $settings = json_decode($_GET["content"]);
        $local->ChangeFileSettings($settings->host, $settings->login, $settings->pass, $settings->name);
        if(data_base_loaded())
        {
            echo json_encode(array("message_type" => "server_response", "response_type" => "database_response", "id_success" => true));
            return;
        }else{
            echo json_encode(array("message_type" => "server_response", "response_type" => "database_response", "id_success" => false));
            return;
        }
    }
    if($_GET["type"] == "GetLastDbFile")
    {
        $file = get_database_file()->last_change_file;
        header ("Content-Type: application/octet-stream");
        header ("Accept-Ranges: bytes");
        header ("Content-Length: ".filesize($file));
        header ("Content-Disposition: attachment; filename=".$file);
        readfile($file);
        exit;
    }
    if($_GET["type"] == "restorebd")
    {
        if($_GET["algo"] == 'local')
        {
            get_template_part("/services/local.service.php");
            $local = new Local();
            echo json_encode($local->RestoreLocalDbCopy());
            exit;
        }
    }
    if($_GET["type"] == "newadd")
    {
        if($_GET["algo"] == "user-add")
        {
            $msg = array("message_type" => "server_response", "is_success" => true, "content" => '
                <div class="modal-header" style="padding:35px 50px;">
                     <button type="button" class="close" onclick="_customModalService.hide()" data-dismiss="modal">&times;</button>
                     <h3>Добавление нового пользователя</h3>
                </div>
                <div class="modal-body edit_content" style="padding:40px 50px">
                    <div class="meta_label_admin_text">Логин пользователя</div>
                    <input type="text" id="user-name" class="admininputfield" placeholder="Введите логин пользователя">
                    <div id="server_user-name_messages" class="error"></div>
                    <div class="meta_label_admin_text">Роль пользователя на сайте</div>
                    <div class="select_area">
                        <select class="select_element select"  id="user-role">
                            <option value="null">-Выберите--</option>
                            <option value="administrator">Администратор</option>
                            <option value="editor">Редактор</option>
                        </select>
                        <div id="server_user-role_messages" class="error"></div>
                    </div>
                    <div class="meta_label_admin_text">Пароль пользователя</div>
                    <input type="text" id="user-pass-1" class="admininputfield" placeholder="Введите пароль для пользователя">
                    <div class="meta_label_admin_text">Подтверждение пароля пользователя</div>
                    <input type="text" id="user-pass-2" class="admininputfield" placeholder="Подтвердите пароль для пользователя">
                    <div id="server_user-pass_messages" class="error"></div>
                    <div style="text-align:center;margin-top:20px;">
                        <button class="btn btn-default confirmed-btn" id="btn---admin" onclick=\'AddUser()\'>Создать пользователя<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                        <div id="messages-server"></div>
                    </div>
                </div>
            ');
        }
        if($_GET["algo"] == 'meta-add')
        {
            $msg = array("message_type" => "server_response", "is_success" => true, "content" => '
                <div class="modal-header" style="padding:35px 50px;">
                     <button type="button" class="close" onclick="_customModalService.hide()" data-dismiss="modal">&times;</button>
                     <h3>Добавление нового метатекста</h3>
                </div>
                <div class="modal-body edit_content" style="padding:40px 50px">
                    <div class="meta_label_admin_text">Описание метатекста</div>
                    <input type="text" id="meta-description" class="admininputfield" placeholder="Введите описание метатекста">
                    <div id="server_meta-description_messages" class="error"></div>
                    <div class="meta_label_admin_text">Контент метатекста</div>
                    <div class="wrapper">
                    <textarea class="editor-global" id="meta-content" style="max-width: 100%;width:100%;height: 250px;">
                        Введите текст, который будет отображён под таблице вывода. Учитывайте, что точка - автоматически означает перевод строки в выводе.
                    </textarea>
                    </div>
                    <div id="server_meta-content_messages" class="error"></div>
                    <div class="meta_label_admin_text">Статус метатекста</div>
                    <div style="text-align: center;"><label title="Если переключатель включён - метатекст будет доступен к выводу. Если нет - метатекст не будет доступен." data-toggle="tooltip"><input type="checkbox" id="meta-status" checked>Метатекст доступен</label></div>
                    <div style="text-align:center;margin-top:20px;">
                        <button class="btn btn-default confirmed-btn" id="btn---admin" onclick=\'AddMeta()\'>Создать метатекст<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                        <div id="messages-server"></div>
                    </div>
                </div>
            ');
        }
        if($_GET["algo"] == 'period-add')
        {
            $msg = array("message_type" => "server_response", "is_success" => true, "content" => '
                <div class="modal-header" style="padding:35px 50px;">
                     <button type="button" class="close" onclick="_customModalService.hide()" data-dismiss="modal">&times;</button>
                     <h3>Добавление нового периода</h3>
                </div>
                <div class="modal-body edit_content" style="padding:40px 50px">
                    <div class="meta_label_admin_text">Контент периода</div>
                    <input type="text" id="period-content" class="admininputfield" placeholder="Введите контент периода">
                    <div id="server_period-description_messages" class="error"></div>
                    <div class="meta_label_admin_text">Статус периода</div>
                    <div style="text-align: center;"><label title="Если переключатель включён - период будет доступен к выводу. Если нет - период не будет доступен." data-toggle="tooltip"><input type="checkbox" id="period-status" checked>Период доступен</label></div>
                    <div style="text-align:center;margin-top:20px;">
                        <button class="btn btn-default confirmed-btn" id="btn---admin" onclick=\'AddPeriod()\'>Создать период<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                        <div id="messages-server"></div>
                    </div>
                </div>
            ');
        }
        if($_GET["algo"] == 'table-add')
        {
            $msg = array("message_type" => "server_response", "is_success" => true, "content" => '
                <div class="modal-header" style="padding:35px 50px;">
                     <button type="button" class="close" onclick="_customModalService.hide()" data-dismiss="modal">&times;</button>
                     <h3>Добавление новой таблицы</h3>
                </div>
                <div class="modal-body edit_content" style="padding:40px 50px">
                    <div class="meta_label_admin_text">Описание таблицы</div>
                    <input type="text" id="table-description" class="admininputfield" placeholder="Введите описание таблицы">
                    <div id="server_table-description_messages" class="error"></div>
                    <div class="meta_label_admin_text">Контент таблицы</div>
                    <div class="wrapper">
                    <textarea class="editor-global" id="meta-content" style="max-width: 100%;width:100%;height: 250px;">
                        Введите текст, который будет отображён под таблице вывода. Учитывайте, что точка - автоматически означает перевод строки в выводе.
                    </textarea>
                    </div>
                    <div id="server_table-content_messages" class="error"></div>
                    <div class="meta_label_admin_text">Статус таблицы</div>
                    <div style="text-align: center;"><label title="Если переключатель включён - таблица будет доступна к выводу. Если нет - таблица не будет доступна." data-toggle="tooltip"><input type="checkbox" id="table-status" checked>Таблица доступна</label></div>
                    <div style="text-align:center;margin-top:20px;">
                        <button class="btn btn-default confirmed-btn" id="btn---admin" onclick=\'AddTable()\'>Создать Таблицу<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                        <div id="messages-server"></div>
                    </div>
                </div>
            ');
        }
        if($_GET["algo"] == "course-add")
        {
            $cabinet = new cabinet_controller();
            $msg = array("message_type" => "server_response", "is_success" => true, "content" => '
                <div class="modal-header" style="padding:35px 50px;">
                     <button type="button" class="close" onclick="_customModalService.hide()" data-dismiss="modal">&times;</button>
                     <h3>Добавление нового курса</h3>
                </div>
                <div id="course--types-opacity" style="background: black; opacity: 0.5; width: 100%; height: 100%; position: absolute; z-index: 100; display: none"></div>
                <div class="modal-body edit_content" style="padding:40px 50px">
                    <div class="meta_label_admin_text">Режим Создания курса</div>
                    <div class="select_area">
                        <select class="select_element select" id="course-algo" onchange="SwitcherCourse()">
                            <option value="standart">Вручную</option>
                            <option value="template">На основании шаблона другого курса</option>
                        </select>
                    </div>
                    <div id="template_place" style="display: none">
                    <div class="meta_label_admin_text">Выберите курс, на основе которого создать новый</div>
                        <div class="error" id="course-fast-errors"></div>
                        <div class="select_area">
                            <select class="select_element select" id="course-template-selector"  onchange="LoadCourseTemplate()">
                                '.$cabinet->GetCourses().'
                            </select>
                        </div>
                        <div style="display:flex;align-items: center;justify-content: center;">
                            <span id="btn--loader" style="text-align: center; display: none;">Идёт загрузка курса....<img style="height: 30px" src="includes/images/loader_admin.gif"></span>
                        </div>
                        <div id="course-template-errors" class="error"></div>
                    </div>
                    <div id="standart-place">
                    <div class="meta_label_admin_text">Название курса</div>
                    <input type="text" id="course-name"  class="admininputfield" placeholder="Введите название курса">
                    <div id="course-name_errors" class="error"></div>
                    <div class="meta_label_admin_text">Периоды, к которым пренадлежит курс</div>
                    <div class="periods-ob">
                        <div class="select_area">
                            <select id="periods-course" onchange="AnaliseSelectCoursesPeriod()" class="select_element select">
                              '.$cabinet->GetPeriods().'
                            </select>
                        </div>
                        <div class="periods-list-for-user" id="periods-list-for-user" style="display:flex"></div>
                        <input type="hidden" id="course-encodes" value=\''.$cabinet->JSON_GetYears().'\'">
                        <input type="hidden" value="" id="injected-periods">
                    </div>
                    <div id="course-period_errors" class="error"></div>
                    <div class="meta_label_admin_text">Группа(тип) курса</div>
                    <div class="select_area">
                        <select class="select_element select" id="course-type" onchange="AnaliseCourseTypes()">
                            '.$cabinet->GetTypes().'
                        </select>
                        <div id="addplace" class="addplace" style="z-index: 101;position:relative; background: white; display: none"><label>Добавление нового типа</label><input type="text" class="admininputfield" id="type---global" placeholder="Введите тип"><div class="modalbtn" onclick="AddType()">Добавить</div><div class="modalbtn" style="background-color:red" onclick="RollTypesSate()">Закрыть</div><div id="server-type-messages"></div></div>
                    </div>
                    <div id="course-group_errors" class="error"></div>
                    <div class="meta_label_admin_text">Стоимость одного занятия</div>
                    <input type="number" id="course-price" class="admininputfield" placeholder="Введите цену курса">
                    <div id="course-price_errors" class="error"></div>
                    <div class="meta_label_admin_text">Скидка при разовом платеже при оплате за весь год</div>
                    <input type="number" id="course-discount" class="admininputfield" placeholder="Введите размер скидки">
                    <div id="course-discount_errors" class="error"></div>
                    <div class="meta_label_admin_text">Общее кол-во пар</div>
                    <input type="number" id="course-lessons" class="admininputfield" placeholder="Введите кол-во учебных пар">
                    <div id="course-total_errors" class="error"></div>
                    <div class="meta_label_admin_text">Минимальный класс для прохождения курса</div>
                    <input type="text" data-toggle="tooltip" title="Введите в поле цифру от 1 до 11 включительно" id="course-min" class="admininputfield" placeholder="Введите цифру от 1 до 11">
                    <div id="course-min_errors" class="error"></div>
                    <div class="meta_label_admin_text">Максимальный класс для прохождения курса</div>
                    <input type="text" data-toggle="tooltip" title="Введите в поле цифру от 1 до 11 включительно" id="course-max" class="admininputfield" placeholder="Введите цифру от 1 до 11">
                    <div id="course-max_errors" class="error"></div>
                    <div class="meta_label_admin_text">Шаблон таблицы отображения для курса</div>
                    <div class="select_area">
                        <select class="select_element select" id="course-table">
                               '.
                $cabinet->GetHoockedTables()
                .'
                        </select>
                    </div>
                    <div id="course-table_errors" class="error"></div>
                    <div class="meta_label_admin_text">Шаблон метатекста, который будет отображён под таблицей вывода</div>
                    <div class="select_area">
                        <select class="select_element select" id="course-meta">
                               '.
                $cabinet->GetMetatexts()
                .'
                        </select>
                    </div>
                    <div style="text-align:center;margin-top:20px;">
                        <button class="btn btn-default confirmed-btn" id="btn---admin" onclick=\'AddCourse()\'>Создать курс<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                        <div id="messages-server"></div>
                    </div>
                    </div>
                    
                </div>
            ');
        }
        echo json_encode($msg);
        exit;
    }
    if($_GET["type"] == "add")
    {
        $content = json_decode($_GET["content"]);
        set_user_login($_SESSION["login"]);
        $cabinet = new cabinet_controller();
        if($cabinet->check_token())
        {
            echo json_encode($cabinet->AdderControl($content));
        }else{
            echo json_encode(array("message_type" => "server_response", "is_success" => false, "content" => "Доступ запрещён, у вас нет прав на выполнение этого действия."));
        }
        exit;
    }
    if($_GET["type"] == "deleteaction")
    {
        set_user_login($_SESSION["login"]);
        $cabinet = new cabinet_controller();
        if($cabinet->check_token())
        {
            echo json_encode($cabinet->DeleterControl($_GET["object"], $_GET["id"]));
        }else {
            echo json_encode(array("is_success" => "false"));
        }
        exit;
    }
    if($_GET["type"] == "get")
    {
        $cabinet = new cabinet_controller();
        if($_GET["item"] == "types")
        {
            echo json_encode(array("message_type" => "server_response", "is_success" => true, "content" => $cabinet->GetTypes()));
            exit;
        }
    }
    if($_GET["type"] == "getCourseTemplate")
    {
        $cabinet = new cabinet_controller();
        echo json_encode($cabinet->GetTemplateForCourse($_GET["course"]));
        exit;
    }
}

get_header(); ?>
    <body>
<?php get_nagivation_menu(); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/editor.js"></script>
    <div id="content">
        <div id="ob">
            <?php
                if(!data_base_loaded())
                {
                    get_auth_form();
                    echo '<input type="hidden" id="dbStatus" value="0">';
                }else{
                    $cabinet = new cabinet_controller();
                    echo '<input type="hidden" id="dbStatus" value="1">';
                    if($cabinet->user_auth())
                    {
                        get_admin_panel();
                    }else{
                        get_auth_form();
                    }
                }
            ?>
        </div>
    </div>
<?php get_footer(); ?>