<?php
/**
 * Created by PhpStorm.
 * User: gener
 * Date: 12/07/2018
 * Time: 15:58
 */
if(!function_exists('is_availible'))
{
    header(' Location: ../404');
}
require('controller/token_controller.php');
class global_admin_model extends token_controller
{
    public function __construct($user_token)
    {
        parent::__construct($user_token);
    }

    protected function GetCourse($query, $output = array(), $id = array())
    {
        $output = array('compiled_data' => $this->CompileData($query));
        $i = 1;
        ?>
        <div class="modal-header" style="padding:35px 50px;">
            <button type="button" class="close"  onclick="_customModalService.hide()" data-dismiss="modal">&times;</button>
            <h4 class="edit_title">Вы редактируете курс <?php echo (isset($output['compiled_data']['course'][1]['course_name'])) ?  $output['compiled_data']['course'][1]['course_name'] : 'Cannot parse title from this course'; ?></h4>
        </div>
        <div class="modal-body edit_content" style="padding:40px 50px;">
            <div class="select_area">
                <div class="meta_label_admin_text" style="margin-top: 0px">Для начала работы</div>
                <select id="admin_select" onchange="analise_course_admin()">
                    <option value="null" selected>Выберите период</option>
                    <?php foreach ($output['compiled_data']["course"] as $year) { ?>
                        <option value="<?php echo $year["id"] ?>"><?php echo $year['years_content'] ?></option>
                    <?php $i++; } ?>
                </select>
            </div>
            <?php $i = 1; foreach ($output['compiled_data']["course"] as $course){ ?>
                <div id="course_<?php echo $course['id'] ?>_content" style="display: none">
                    <div class="meta_label_admin_text">Название курса</div>
                    <input type="text" id="course_name_<?php echo $course['id'] ?>" class="admininputfield" value="<?php echo (isset($course['course_name'])) ?  $course['course_name'] : 'Cannot parse title from this course'; ?>">
                    <div id="course_name_errors_<?php echo $course['id'] ?>" class="error"></div>
                    <div class="meta_label_admin_text">Стоимость одного занятия</div>
                    <input type="number" id="course_price_<?php echo $course['id'] ?>" class="admininputfield" value="<?php echo (isset($course['course_prices'])) ?  $course['course_prices'] : '100'; ?>">
                    <div id="course_price_errors_<?php echo $course['id'] ?>" class="error"></div>
                    <div class="meta_label_admin_text">Скидка при разовом платеже за весь год</div>
                    <input type="number" id="course_discount_<?php echo $course['id'] ?>" class="admininputfield" value="<?php echo (isset($course['course_discount'])) ?  $course['course_discount'] : '200'; ?>">
                    <div id="course_discount_errors_<?php echo $course['id'] ?>" class="error"></div>
                    <div class="meta_label_admin_text">Общее кол-во пар</div>
                    <input type="number" id="course_total_<?php echo $course['id'] ?>" class="admininputfield" value="<?php echo (isset($course['course_total'])) ?  $course['course_total'] : 'e'; ?>">
                    <div id="course_total_errors_<?php echo $course['id'] ?>" class="error"></div>
                    <div class="meta_label_admin_text">Минимальный класс для прохождения</div>
                    <input type="number" id="course_min_<?php echo $course['id'] ?>" class="admininputfield" value="<?php echo (isset($course['course_minimum'])) ?  $course['course_minimum'] : 'e'; ?>">
                    <div id="course_min_errors_<?php echo $course['id'] ?>" class="error"></div>
                    <div class="meta_label_admin_text">Максимальный класс для прохождения</div>
                    <input type="number" id="course_max_<?php echo $course['id'] ?>" class="admininputfield" max="12" value="<?php echo (isset($course['course_maximum'])) ?  $course['course_maximum'] : 'e'; ?>">
                    <div id="course_max_errors_<?php echo $course['id'] ?>" class="error"></div>
                    <div class="meta_label_admin_text">Название шаблона таблицы отображения</div>
                    <div class="select_area">
                        <select class="select_element select"  id="hooked_table_<?php echo $course['id'] ?>">
                            <?php echo $this->GetCurrentHookedTable($course['course_name'], $this->GetYearId($this->GetResultFromDb("SELECT id FROM years WHERE year_content = '".$course['years_content']."'"))) ?>
                            <?php echo $this->GetHookedTables($this->GetActiveHookedTableForThisCourse($course['course_name'],$this->GetYearId($this->GetResultFromDb("SELECT id FROM years WHERE year_content = '".$course['years_content']."'")))) ?>
                        </select>
                    </div>
                    <div id="course_table_errors_<?php echo $course['id'] ?>" class="error"></div>
                    <div class="meta_label_admin_text">Название шаблона текста под результатом расчёта</div>
                    <div class="select_area">
                        <select class="select_element select" id="meta_text_<?php echo $course['id'] ?>">
                            <option value="null">Не отображать текст</option>
                            <?php echo $this->GetCurrentMetaText($course["metatext_id"]) ?>
                            <?php echo $this->GetAllMetaTextsList($course["metatext_id"]) ?>
                        </select>
                    </div>
                    <div class="course_state">
                        <label class="meta_label_admin_text">Статус курса</label><br>
                        <label title="Этот пункт имеет 2 положения: 1) Галочка не стоит, это означает, что курс будет недоступен для выбора  2) Галочка стоит, это означает, что курс будет доступен для выбора. При изменении этого параметра, операция будет применена ко всем периодам, к которым этот курс пренадлежит. " data-toggle="tooltip"><input type="checkbox"  id="course_state_<?php echo $course['id'] ?>" <?php if($course["course_status"]) { echo 'checked'; } ?>>  Курс доступен</label>
                    </div>
                    <div id="course_meta_errors_<?php echo $course['id'] ?>" class="error"></div>
                </div>
            <?php $i++; $id[$course['id']] = array($course['id']); } ?>
        </div>
        <div style="text-align: center">
            <button class="btn btn-default confirmed-btn" id="btn---admin" onclick='UpdateCourse(<?php echo json_encode($id) ?>)'>Сохранить изменения<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
            <div id="messages_under_admin_button" class="error"></div>
            <div id="success_messages_under_admin_button" class="success"></div>
        </div>
<?php
    }

    protected function GetHoockedTable($query)
    {
        foreach ($query as $table)
        {
            ?>
            <div class="modal-header" style="padding:35px 50px;">
                <button type="button" class="close" onclick="_customModalService.hide()" data-dismiss="modal">&times;</button>
                <h4 class="edit_title">Вы редактируете таблицу <?php echo $table["table_description"]?></h4>
            </div>
            <div class="modal-body edit_content" style="padding:10px 50px;">
                <div id="table-total-settings">
                    <div class="meta_label_admin_text">Описание таблицы</div>
                    <input type="text" class="admininputfield" id="table-description" value="<?php echo (!empty($table["table_description"])) ?  $table["table_description"] : 'Описание не введено' ?>">
                    <div id="table-description-errors" class="error"></div>
                    <div class="meta_label_admin_text">Шаблон таблицы</div>
                    <div id="table_ob">
                    <textarea id="table-content" class="editor-global"  style="width: 100%;height: 250px; display: none;">
                        <?php echo $table["table_content"] ?>
                    </textarea>
                    </div>
                    <div id="table-template-errors" class="error"></div>
                    <div class="meta_label_admin_text">Статус таблицы</div>
                    <div style="text-align:center">
                        <label data-toggle="tooltip" title="Если галочка не стоит - таблица не будет активна и её нельзя будет выбрать в качестве таблицы для выввода данных. Если галочка стоит - таблица будет доступна для соответствующих операций"><input type="checkbox" id="table-status" <?php if($table["table_status"]) { echo 'checked';}?>>Таблица активна</label>
                    </div>
                </div>
                <div class="meta_label_admin_text">Раздел специальных команд</div>
                <div class="select_area">
                    <select class="select_element select" onchange="AnaliseSpecialSelectTables()" id="special-command">
                        <option data-type="null" value="null">--Не выбрана--</option>
                        <option data-type="all" value="all">Применить ко всем элементам в бд</option>
                        <?php echo $this->GetTypes() ?>
                        <option value="course">Применить к определённому курсу</option>
                    </select>
                </div>
                <div id="course-select" style="display: none">
                    <div class="meta_label_admin_text">Выберите курс, к которому нужно применить команду</div>
                    <div class="select_area">
                        <select class="select_element select" id="course-selecter">
                            <?php echo $this->GetAvailibleCourses($table["table_id"]) ?>
                        </select>
                    </div>
                </div>
                <div id="period-select-d" style="display: none">
                    <div class="meta_label_admin_text">Выберите период, к которому будет применена команда</div>
                    <div class="select_area">
                        <select class="select_element select" id="period-select">
                            <option value="none">--Не выбран--</option>
                            <option value="allperiods">Все периоды</option>
                            <?php echo $this->GetAvailiblePeriods() ?>
                        </select>
                    </div>
                </div>
                <div id="messages_place" class="error"></div>
                <input type="hidden" id="table_id" value="<?php echo $table['table_id']; ?>">
                <div style="text-align: center; margin-top: 15px" id="button-total-settings">
                    <button class="btn btn-default confirmed-btn" style="margin-top: 15px;" data-toggle="tooltip" title="Обновить таблицу вывода" id="btn---admin" onclick="UpdateHoockedTable()">Сохранить изменения<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                </div>
                <div style="text-align: center; margin-top: 15px; display: none" id="button-global-settings">
                    <button class="btn btn-default confirmed-btn" style="margin-top: 15px;" data-toggle="tooltip" title="Начать применение команды, будет создан дамп бд" id="btn---admin-global" onclick="UpdateGlobalTable()">Сохранить изменения<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                </div>
            </div>
            <?php
        }
    }

    protected function GetMetaTable($query, $output = array())
    {
        foreach ($query as $meta)
        {
            ?>
            <div class="modal-header" style="padding:35px 50px;">
                <button type="button" class="close" onclick="_customModalService.hide()" data-dismiss="modal">&times;</button>
                <h4 class="edit_title">Вы редактируете текст <?php echo $meta["metatext_description"]?></h4>
            </div>
            <div class="modal-body edit_content" style="padding:10px 50px;">
                <div id="meta-total-settings">
                    <div class="meta_label_admin_text">Описание метатекста</div>
                    <input type="text" id="meta-description" class="admininputfield" value="<?php echo $meta["metatext_description"] ?>">
                    <div id="meta-description-errors" class="error"></div>
                    <div class="meta_label_admin_text">Содерржание метатекста</div>
                    <textarea id="table_content_textarea" class="editor-global" style="width: 100%;height: 250px;">
                        <?php echo $meta["metatext_content"] ?>
                    </textarea>
                    <div id="meta-content-errors" class="error"></div>
                    <div class="meta_label_admin_text">Статус метатекста</div>
                    <div style="text-align:center">
                        <label><input type="checkbox" id="meta-status" <?php if($meta["status"]) { echo 'checked';}?>>Метатекст активен</label>
                    </div>
                </div>
                <div class="meta_label_admin_text">Раздел специальных команд</div>
                <div class="select_area">
                    <select class="select_element select" onchange="AnaliseSpecialSelectMeta()" id="special-command">
                        <option data-type="null" value="null">--Не выбрана--</option>
                        <option data-type="all" value="all">Применить ко всем элементам в бд</option>
                        <?php echo $this->GetTypes() ?>
                        <option value="course">Применить к определённому курсу</option>
                    </select>
                </div>
                <div id="course-select" style="display: none">
                    <div class="meta_label_admin_text">Выберите курс, к которому нужно применить команду</div>
                    <div class="select_area">
                        <select class="select_element select" id="course-selecter">
                            <?php echo $this->GetAvailibleCourses($meta["id"]) ?>
                        </select>
                    </div>
                </div>
                <div id="period-select-d" style="display: none">
                    <div class="meta_label_admin_text">Выберите период, к которому будет применена команда</div>
                    <div class="select_area">
                        <select class="select_element select" id="period-select">
                            <option value="none">--Не выбран--</option>
                            <option value="allperiods">Все периоды</option>
                            <?php echo $this->GetAvailiblePeriods() ?>
                        </select>
                    </div>
                </div>
                <div id="messages_place" class="error"></div>
                <input type="hidden" id="meta_id" value="<?php echo $meta['id']; ?>">
                <div style="text-align: center; margin-top: 15px" id="button-total-settings">
                    <button class="btn btn-default confirmed-btn" style="margin-top: 15px;" data-toggle="tooltip" title="Сохранить изменения" id="btn---admin" onclick="UpdateMeta()">Сохранить<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                </div>
                <div style="text-align: center; margin-top: 15px; display: none" id="button-global-settings">
                    <button class="btn btn-default confirmed-btn" style="margin-top: 15px;" data-toggle="tooltip" title="Начать выполнение команды, будет создан дамп бд" id="btn---admin-global" onclick="UpdateGlobalMeta()">Сохранить<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                </div>
            </div>
            <?php
        }
    }

    protected function GetPeriodTable($query)
    {
        foreach ($query as $period)
        {
            ?>
            <div class="modal-header" style="padding:35px 50px;">
                <button type="button" class="close" onclick="_customModalService.hide()" data-dismiss="modal">&times;</button>
                <h4 class="edit_title">Вы редактируете период <?php echo $period["year_content"]?></h4>
            </div>
            <div class="modal-body edit_content" style="padding:10px 50px;">
                <div id="period-total-settings">
                    <div class="meta_label_admin_text">Контент периода</div>
                    <input type="text" id="period-content" class="admininputfield" value="<?php echo $period["year_content"] ?>">
                    <div id="period-content-errors" class="error"></div>
                    <div class="meta_label_admin_text">Статус периода</div>
                    <div style="text-align: center">
                        <label><input type="checkbox" id="period-status" <?php if($period["status"]) {echo 'checked';} ?>>Период активен</label>
                    </div>
                </div>
                <div class="meta_label_admin_text">Раздел специальных команд</div>
                <div class="select_area">
                    <select class="select_element select" onchange="AnaliseSpecialSelectPeriods()" id="special-command">
                        <option data-type="null" value="null">--Не выбрана--</option>
                        <option data-type="all" value="all">Применить ко всем элементам в бд</option>
                        <?php echo $this->GetTypes() ?>
                    </select>
                </div>
                <div id="course-select" style="display: none">
                    <div class="meta_label_admin_text">Выберите курс, к которому нужно применить команду</div>
                    <div class="select_area">
                        <select class="select_element select" id="course-selecter">
                            <?php echo $this->GetAvailibleCoursesForThisPeriod($period["id"]) ?>
                        </select>
                    </div>
                </div>
                <div id="messages_place" class="error"></div>
                <input type="hidden" id="period_id" value="<?php echo $period['id']; ?>">
                <div style="text-align: center; margin-top: 15px" id="button-total-settings">
                    <button class="btn btn-default confirmed-btn" style="margin-top: 15px;" data-toggle="tooltip" title="Сохранить период" id="btn---admin" onclick="UpdatePeriod()">Сохранить<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                </div>
                <div style="text-align: center; margin-top: 15px; display: none" id="button-global-settings">
                    <button class="btn btn-default confirmed-btn" style="margin-top: 15px;" data-toggle="tooltip" title="Применить команду, будет создана резервная копия бд" id="btn---admin-global" onclick="UpdateGlobalPeriod()">Сохранить<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
                </div>
            </div>
            <?php
        }
    }

    protected function GetUserTable($query)
    {
        foreach($query as $user)
        {
            ?>
            <div class="modal-header" style="padding:35px 50px;">
                <button type="button" class="close" onclick="_customModalService.hide()" data-dismiss="modal">&times;</button>
                <h4 class="edit_title">Вы редактируете пользователя <?php echo $user["login"]?></h4>
            </div>
            <div class="modal-body edit_content" style="padding:10px 50px;">
            <div class="meta_label_admin_text">Логин пользователя</div>
            <input type="text" id="user-login" class="admininputfield" value="<?php echo $user["login"] ?>">
            <div id="user-login-error" class="error"></div>
            <div class="meta_label_admin_text">Роль пользователя на сайте</div>
            <div class="select_area">
                <select class="select_element select"  id="user-role">
                    <option value="administrator" <?php ($user["role"] == 'administrator') ?  'checked' :  '' ?>>Администратор</option>
                    <option value="editor" <?php ($user["role"] == 'editor') ?  'checked' :  '' ?>>Редактор</option>
                </select>
                <div id="server_user-role_messages" class="error"></div>
            </div>
            <div class="meta_label_admin_text">Введите новый пароль</div>
            <input type="text" id="user-pass-1" class="admininputfield" placeholder="Пример: 1111">
            <div class="meta_label_admin_text">Подтвердите новый пароль</div>
            <input type="text" id="user-pass-2" class="admininputfield" placeholder="Пример: 1111">
            <div id="user-pass-error" class="error"></div>
            <div id="messages_place" class="error"></div>
            <input type="hidden" id="user_id" value="<?php echo $user['id']; ?>">
            <input type="hidden" id="user_pass" value="<?php echo $user['pass']; ?>">
            <div style="text-align: center; margin-top: 15px">
                <button class="btn btn-default confirmed-btn" style="margin-top: 15px;" id="btn---admin" onclick="UpdateUser()">Сохранить<span id="btn---loader" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
            </div>
            </div>
            <?php
        }
    }

    private function CompileData($input, $output = array())
    {
        $i = 1;
        if(is_array($input))
        {
            foreach ($input as $key)
            {
                $output['course'][$i] = array("id"=>$key["id"], "count" => $i, "course_minimum" => $key["minimum"], "course_maximum" => $key["maximum"], "course_total" => $key["total"], "course_discount" => $key["discount"], "course_name"=>$key["content"], "course_prices" => $key["price"], "course_status"=>$key["status"], "hooked_table_description"=>$key["table_description"], "years_content"=> $key["year_content"], "type"=>$key["type"], "metatext_id" => $key["metatext_id"]);
                $i++;
            }
            return $output;
        }else{
            return $input;
        }
    }

    private function GetHookedTables($outcludet_content,  $output = '')
    {
        foreach ($this->GetResultFromDb("SELECT * FROM tables_teplate WHERE table_id != '".$outcludet_content."'") as $key)
        {
            $output .= '<option value="'.$key["table_id"].'">'.$key["table_description"].'</option>';
        }
        return $output;
    }

    private function GetCurrentHookedTable($content, $year)
    {
        foreach ($this->GetResultFromDb("SELECT courses.hoocked_table_id, tables_teplate.table_description FROM courses LEFT JOIN tables_teplate ON courses.hoocked_table_id = tables_teplate.table_id WHERE content='".$content."' AND year='".$year."'") as $key)
        {
            return '<option value="'.$key["hoocked_table_id"].'" selected>'.$key["table_description"].'</option>';
        }
    }

    private function GetYearId($array)
    {
        return $array[0]["id"];
    }

    private function GetActiveHookedTableForThisCourse($content, $year)
    {
        foreach ($this->GetResultFromDb("SELECT courses.hoocked_table_id, tables_teplate.table_description FROM courses LEFT JOIN tables_teplate ON courses.hoocked_table_id = tables_teplate.table_id WHERE content='".$content."' AND year='".$year."'") as $key)
        {
            return $key["hoocked_table_id"];
        }
    }

    private function GetCurrentMetaText($metatext_id)
    {
        foreach ($this->GetResultFromDb("SELECT * FROM metatexts WHERE id='".$metatext_id."' AND status='1'") as $key)
        {
            return '<option value="'.$key["id"].'" selected>'.$key['metatext_description'].'</option>';
        }
    }

    private function GetAllMetaTextsList($outcludet, $output = '')
    {
        foreach ($this->GetResultFromDb("SELECT * FROM metatexts WHERE id != '".$outcludet."'") as $key)
        {
            $output .= '<option value="'.$key["id"].'">'.$key['metatext_description'].'</option>';
        }
        return $output;
    }

    private function GetHooksTable()
    {
        include("controller/hooks.php");
        $hooks = new hooks_worker();
        $output_table = '<table><thead><tr><td>Использование команды</td><td>Описание команды</td></tr></thead><tbody>';
        foreach ($hooks->hook_controller('get') as $key)
        {
            $output_table .= '<tr><td>'.$key["hook_name"].'</td><td>'.$key["hook_description"].'</td></tr>';
        }
        return $output_table.'</tbody></table>';
    }

    private function GetTypes()
    {
        $output = '';
        foreach ($this->GetResultFromDb('SELECT * FROM types WHERE 1') as $key)
        {
            $output .= '<option value="'.$key["type_id"].'">Применить команду ко всем элементам с типом '.$key["type_content"].'</option>';
        }
        return $output;
    }

    private function GetAvailibleCoursesForThisPeriod($period_id)
    {
        $output = '';
        $prev = '';
        $result = $this->GetResultFromDb("SELECT group_id, content FROM courses WHERE year='$period_id' ORDER BY group_id");
        if(empty($result))
        {
            $result = $this->GetResultFromDb("SELECT group_id, content FROM courses ORDER BY group_id");
            foreach ($result as $key)
            {
                if($key["content"] !== $prev)
                {
                    $output .= '<option value="'.$key["group_id"].'">'.$key["content"].'</option>';
                    $prev = $key["content"];
                }else{
                    continue;
                }

            }
        }else{
            return '<option value="none">Все объекты уже привязаны к этому периоду!</option>';
        }
        return $output;
    }

    private function GetAvailibleCourses()
    {
        $output = '';
        $prev = '';
        $result = $this->GetResultFromDb("SELECT group_id, content FROM courses WHERE 1 ORDER BY group_id");
        if(!empty($result))
        {
            $result = $this->GetResultFromDb("SELECT group_id, content FROM courses ORDER BY group_id");
            foreach ($result as $key)
            {
                if($key["content"] !== $prev)
                {
                    $output .= '<option value="'.$key["group_id"].'">'.$key["content"].'</option>';
                    $prev = $key["content"];
                }else{
                    continue;
                }
            }
        }else{
            return '<option value="none">Курсы не найдены</option>';
        }
        return $output;
    }

    private function GetAvailiblePeriods()
    {
        $output = '';
        $result = $this->GetResultFromDb("SELECT id, year_content FROM years WHERE status='1'");
        if(!empty($result))
        {
            foreach ($result as $period)
            {
                $output .= '<option value="'.$period["id"].'">'.$period["year_content"].'</option>';
            }
        }else{
            return '<option value="none">Не найдено ни одного периода в бд</option>';
        }
        return $output;
    }
}