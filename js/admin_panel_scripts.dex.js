var activenow = '';
var prevloadedcourse = '';
var errorscin = '';
var disnow = '';
$(document).ready(function () {
    $(".arrow").click(function(){
        $("#admin_panel_menu").slideToggle("fast");
    });
    load_data('courses_list');
    $('.element_admin_panel').click(function () {
        var data = $($(this).get()[0]).data('action');
        if(data == "logout")
        {location.href = 'cabinet?action=logout';}
        if(data=='rollbackProfile'){location.href='cabinet?debug=true&atype=rollback'}

        if (activenow !== '') {
            if (data == activenow) {
                return;
            }
            if ($('#' + data + '_place').text() !== '') {
                $('#' + data + '_place').text('');
            }
            $('#' + activenow).removeClass('active');
            $('#' + data).addClass('active');
            load_data(data);
            Animate(data);
            activenow = data;
        } else {
            $('#' + data).addClass('active');
            load_data(data);
            $('#' + data + '_place').slideToggle('fast');
            activenow = data;
        }
    });
    $(".custom-modal-fade").click(function(){
       _customModalService.hide();
    });
    $(window).scroll(function(){
        if($(this).scrollTop() > 0)
        {
            $("#top_menu").css("position", "fixed");
            $("#top_menu").css("z-index", "2");
            $("#top_menu").css("width", "100%");
        }else{
            $("#top_menu").css("position", "");
        }
    });
});

function Animate(appendable) {
    $('#' + activenow + '_place').slideToggle('slow');
    $('#' + appendable + '_place').slideToggle('fast');
}

function load_data(properties) {
    $('#loader_body').css('display', 'flex');
    $.ajax({
        url: "cabinet/",
        data: "get_admin_cabinet_part=" + properties + "&auth_token=" + $("#user_token").val(),
        success: function (msg) {
            $('#' + properties + '_place').append(msg);
            activenow = properties;
            setTimeout(function () {
                $('#loader_body').css('display', 'none')
            }, 500);
            $("[data-toggle=tooltip]").tooltip();
        }
    });

}

function load(element) {
    var elem = $(element).get()["0"];
    if ($(elem).hasClass('editopengl')) {
        do_action_query('global_admin/', 'action_type=getcourse&id=' + $(elem).data('showcourse') + '&token=' + $("#user_token").val(), 'Редакктирование курса', 'course');
        return;
    }
    if ($(elem).hasClass('editopentb')) {
        do_action_query('global_admin/', 'action_type=gettable&id=' + $(elem).data('showtable') + '&token=' + $("#user_token").val(), 'Редактрование таблицы вывода', 'table');
        return;
    }
    if ($(elem).hasClass('editopenmt')) {
        do_action_query('global_admin/', 'action_type=getmeta&id=' + $(elem).data('showmeta') + '&token=' + $("#user_token").val(), 'Редактрование таблицы вывода', 'table');
        return;
    }
    if ($(elem).hasClass('editopenpd')) {
        do_action_query('global_admin/', 'action_type=getperiod&id=' + $(elem).data('showperiod') + '&token=' + $("#user_token").val(), 'Редактрование таблицы вывода', 'table');
        return;
    }
    if ($(elem).hasClass('editopenus')) {
        do_action_query('global_admin/', 'action_type=getuser&id=' + $(elem).data('showuser') + '&token=' + $("#user_token").val(), 'Редактрование таблицы вывода', 'table');
        return;
    }
}

function do_action_query(url, query, title, type) {
    $.ajax({
        url: url,
        data: query,
        success: function (msg) {
            $('#append_modal_content').text('');
            $('#append_modal_content').append(msg);
            if($(".editor-global") !== undefined)
            {
                $(".editor-global").Editor();
                $(".Editor-editor").append($(".editor-global").val());
            }
            _customModalService.show("#admin_modal");
            $("[data-toggle=tooltip]").tooltip();
        }
    });
}

function analise_course_admin() {
    if (prevloadedcourse !== '' && prevloadedcourse !== "course_" + $("#admin_select").val() + "_content") {
        $("#" + prevloadedcourse).slideToggle('fast');
    }
    $("#course_" + $("#admin_select").val() + "_content").slideToggle('fast');
    prevloadedcourse = "course_" + $("#admin_select").val() + "_content";
}

function UpdateCourse(id_array) {
    $("#btn---loader").css("display", "");
    $("#btn---admin").prop('disabled', 'true');
    $(errorscin).text('');
    var objectsvalues = [];
    var s = 0;
    for (var i in id_array) {
        objectsvalues.push(GetDataAboutEditedCourse(id_array[i][0]));
        s++;
    }
    SendNewInformationAboutCourse(CompareCoursesData(objectsvalues));
}

function GetDataAboutEditedCourse(id) {
    return {
        "course_ident_id": id,
        'course_name': $('#course_name_' + id).val(),
        'course_price': $("#course_price_" + id).val(),
        'course_discount': $("#course_discount_" + id).val(),
        "course_total": $("#course_total_" + id).val(),
        "course_min": $("#course_min_" + id).val(),
        "course_max": $("#course_max_" + id).val(),
        "hooked_table": $("#hooked_table_" + id).val(),
        "meta_text": $("#meta_text_" + id).val(),
        "course_status": document.getElementById("course_state_"+id).checked
    };
}

function SendNewInformationAboutCourse(coursearray)
{
    $.ajax({
        url: 'updatecore/',
        data: 'action=updatecoursescore&content='+JSON.stringify(coursearray)+"&token="+$("#user_token").val(),
        success: function(msg) {
            var data = JSON.parse(msg);
            if(data["error"] !== undefined)
            {
                var data = data["error"];
                for (var i in data)
                {
                    console.log(data[i]);
                    $("#"+data[i]["error_focused"]+data[i]["item_id"]).append(data[i]["error_content"]);
                    $("#admin_select").val(data[i]["item_id"]);
                    if(document.getElementById(data[i]["slide_content"]).style.display === 'none')
                    {
                        $("#"+data[i]["slide_content"]).slideToggle("fast");
                        $("#" + prevloadedcourse).slideToggle('fast');
                        prevloadedcourse = data[i]["slide_content"]+data[i]["item_id"];
                    }
                    errorscin = "#"+data[i]["error_focused"]+data[i]["item_id"];
                    $("#"+data[i]["error_object"]+data[i]["item_id"]).focus();
                }
            }

            if(data["messages_under_button"] !== undefined)
            {
                var data = data["messages_under_button"];
                for (var i in data)
                {
                    if(data[i]["error_button"] !== undefined)
                    {
                        $("#success_messages_under_admin_button").text("");
                        $("#messages_under_admin_button").text("");
                        $("#messages_under_admin_button").append(data[i]["error_button"]);
                    }
                    if(data[i]["success_button"] !== undefined)
                    {
                        $("#success_messages_under_admin_button").text("");
                        $("#messages_under_admin_button").text("");
                        $("#messages_under_admin_button").append(data[i]["success_button"]);
                    }
                }
            }

            if(data["success_button"] !== undefined)
            {
                $("#success_messages_under_admin_button").text("");
                $("#messages_under_admin_button").text("");
                $("#success_messages_under_admin_button").append(data["success_button"]);
            }

            setTimeout(() => {
                $("#btn---loader").css("display", "none");
                document.getElementById('btn---admin').disabled = false;
            }, 1000);
        }
    });
}

function CallTableCreator()
{
    $("#modal_table").slideToggle("fast");
}

function CreateTable()
{
    var st = $("#st").val();
    var rows = $("#rows").val();
    $("#modal_table").slideToggle("fast");
    var table = '<table border="1" align="center">';
    for (var t = 1; t <= st; t++)
    {
        if(t==1)
        {
            table += '<thead>';
        }else{
            table += '<tbody>';
        }
        table += '<tr>';
        for(var i = 1; i <= rows; i++)
        {
            if(t==1)
            {
                table += '<th>Введите контент '+rows+'</th>';
            }else{
                table += '<td>Введите контент '+rows+'</td>';
            }

        }
        table += '</tr>';
        if(t==1)
        {
            table += '</thead>';
        }else{
            table += '</tbody>';
        }
    }
    $("#table_content").append(table + '</table>')
}

function CompareCoursesData(coursedata, coursename = '', coursestatus = '')
{
    if(coursedata.length > 1)
    {
        for(var i in coursedata)
        {
            if(coursename === '')
            {
                coursename = coursedata[i]['course_name'];
            }
            if(coursename !== ''){
                coursedata[i]['course_name'] = coursename;
            }
            if(coursestatus === '')
            {
                coursestatus = coursedata[i]["course_status"];
            }
            if(coursestatus !== '')
            {
                coursedata[i]["course_status"] = coursestatus;
            }
        }
    }
    return coursedata;
}

function ChangeVisibility(id)
{
    $("#"+id).slideToggle("fast");
}

function SwitcherCourse()
{
    if(document.getElementById('standart-place').display == 'none')
    {
        $("#standart-place").slideToggle("fast");
    }
    if(document.getElementById('template_place').display !== 'none')
    {
        $("#template_place").slideToggle("fast");
    }
}

function DoSpecialCommand()
{
    $("#btn---loaders").css("display", "");
    $("#btn---admin").prop('disabled', 'true');
    var data = CollectData();
    if(data == false)
    {
        $("#messages_place").text("");
        $("#messages_place").append("Ошибка, вы не выбрали ни одной операции!");
        $("#btn---loader").css("display", "none");
        document.getElementById('btn---admin').disabled = false;
        return;
    }
    $.ajax({
        url: 'updatecore/',
        data: 'action=special&content='+data+'&token='+$("#user_token").val(),
        success: function(msg)
        {
            var jSon = JSON.parse(msg);
            $("#messages_place").text("");
            $("#messages_place").append(jSon["message"]);
            $("#btn---loader").css("display", "none");
            document.getElementById('btn---admin').disabled = false;
        }
    });
}

function CollectData()
{
    if($("#types").val() !== 'non_selected')
    {
        return JSON.stringify({"action": "setTableToCurrentTypes", 'value': $("#types").val(), "table_id": $("#table_id").val()});
    }else {
        return false;
    }
}

function ChangeState(id)
{
    document.getElementById(id).disabled = !document.getElementById(id).disabled;
}

function ChangeDbSettings()
{
    var ngModel = {host: 0, username: null, password: null, name: null};
    if(document.getElementById('dbHostAuto').checked == true)
    {
        ngModel.host = '127.0.0.1';
    }else{
        if($("#dbHost").val() !== '')
        {
            ngModel.host = $("#dbHost").val();
        }else{
            $("#msgs").text("");
            $("#msgs").text("Вы не заполнили поле хоста бд!");
            return;
        }
    }
    if($("#dbLogin").val() !== "")
    {
        ngModel.username = $("#dbLogin").val();
    }else{
        $("#msgs").text("");
        $("#msgs").text("Вы пропустили поле ввода логина бд!");
        return;
    }
    ngModel.password = $("#dbPass").val();
    if($("#dbName").val() !== "")
    {
        ngModel.name = $("#dbName").val();
    }else{
        $("#msgs").text("");
        $("#msgs").text("Вы пропустили поле ввода имени бд!");
        return;
    }
    $.ajax({
        url: "updatecore/",
        data: "type=ChangeDbSettings&algo=fileSettings&content="+JSON.stringify(ngModel),
        success: function(data)
        {
            var msg = JSON.parse(data);
            if(msg["is_success"] == true)
            {
                $("#msgs").text("");
                $("#msgs").text("Файл бд успешно обновлён!");
            }else{
                $("#msgs").text("");
                $("#msgs").text(msg["content"]);
            }
        }
    });
}

function RollbackLastDbVersion(way)
{
    if(way == "local")
    {
        $("#r-f").text("");
        $("#r-f").text("Идёт восстановление... ");
        $("#btn-f---loader").css("display", "");
        document.getElementById('restoreDbLocal').disabled = true;
        document.getElementById('restoreDbFile').disabled = true;
        $.ajax({
            url: "updatecore/",
            data: "type=ChangeDbSettings&algo=RestoreDb",
            success: function(data)
            {
                var msg = JSON.parse(data);
                if(msg["is_success"] == true)
                {
                    $("#r-f").text("");
                    $("#r-f").text("Восстановление завершено!");
                    $("#btn-f---loader").css("display", "none");
                    document.getElementById('restoreDbLocal').disabled = false;
                    document.getElementById('restoreDbFile').disabled = false;
                    setTimeout(function(){
                        $("#r-f").text("");
                        $("#r-f").text("Восстановить из локальных файлов");
                    }, 5000);
                    $("#server-answers-db").text("");
                    $("#server-answers-db").text("Последняя версия бд была успешно восстановлена!");
                }else{
                    $("#r-f").text("");
                    $("#r-f").text("Ошибка восстановления!");
                    $("#btn-f---loader").css("display", "none");
                    document.getElementById('restoreDbLocal').disabled = false;
                    document.getElementById('restoreDbFile').disabled = false;
                    setTimeout(function(){
                        $("#r-f").text("");
                        $("#r-f").text("Восстановить из локальных файлов");
                    }, 5000)
                    $("#server-answers-db").text("");
                    $("#server-answers-db").text(msg["content"]);
                }
            }
        });
    }
    if(way == "file")
    {
        $("#r-l").text("");
        $("#r-l").text("Идёт восстановление... ");
        $("#btn-l---loader").css("display", "");
        document.getElementById('restoreDbLocal').disabled = true;
        document.getElementById('restoreDbFile').disabled = true;
        var $input = $("#f-file");
        var fd = new FormData;

        fd.append('dbFile', $input.prop('files')[0]);

        $.ajax({
            url: 'updatecore/',
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function (data) {
                var msg = JSON.parse(data);
                if(msg["is_success"] == true)
                {
                    $("#r-l").text("");
                    $("#r-l").text("Восстановлено успешно!");
                    $("#btn-l---loader").css("display", "none");
                    document.getElementById('restoreDbLocal').disabled = false;
                    document.getElementById('restoreDbFile').disabled = false;
                    setTimeout(function(){
                        $("#r-l").text("");
                        $("#r-l").text("Восстановить из загруженого файла");
                    }, 5000)
                    $("#server-answers-db").text("");
                    $("#server-answers-db").text("Последняя версия бд была успешно восстановлена!");
                }else{
                    $("#r-l").text("");
                    $("#r-l").text("Ошибка восстановления!");
                    $("#btn-l---loader").css("display", "none");
                    document.getElementById('restoreDbLocal').disabled = false;
                    document.getElementById('restoreDbFile').disabled = false;
                    setTimeout(function(){
                        $("#r-l").text("");
                        $("#r-l").text("Восстановить из загруженого файла");
                    }, 5000)
                    $("#server-answers-db").text("");
                    $("#server-answers-db").text(msg["message"]);
                }
            }
        });
    }
}

function NewAdd(addtype)
{
    $("#btn---loader").css("display", "");
    $.ajax({
        url: "cabinet/",
        data: "type=newadd&algo="+addtype,
        success: function(data)
        {
            var msg = JSON.parse(data);
            $("#add_admin_modal_content").text("");
            $("#add_admin_modal_content").append(msg["content"]);
            if($(".editor-global").text() !== undefined)
            {
                $(".editor-global").Editor();
                $(".Editor-editor").append($(".editor-global").val());
            }
            _customModalService.show("#add_admin_modal");
            $("#btn---loader").css("display", "none");
            $("[data-toggle=tooltip]").tooltip();
        }

    });
}

function AddUser()
{
    var ngModel = {
        action: 'newuser',
        userName: '',
        userRole: '',
        userPass: ''
    };
    $("#server_user-name_messages").text("");
    $("#server_user-role_messages").text("");
    $("#server_user-pass_messages").text("");
    if($("#user-name").val() !== '')
    {
        ngModel.userName = $("#user-name").val();
    }else{
        $("#server_user-name_messages").text("");
        $("#server_user-name_messages").append("Вы пропустили это поле!");
        return;
    }
    if($("#user-role").val() !== 'null')
    {
        ngModel.userRole = $("#user-role").val();
    }else{
        $("#server_user-role_messages").text("");
        $("#server_user-role_messages").append("Вы пропустили это поле!");
        return;
    }
    if($("#user-pass-1").val() !== "" && $("#user-pass-2").val() !== "")
    {
        if($("#user-pass-1").val() == $("#user-pass-2").val())
        {
            ngModel.userPass = $("#user-pass-1").val();
        }else{
            $("#server_user-pass_messages").text("");
            $("#server_user-pass_messages").append("Проверьте правильность данных в полях ввода пароля!");
            return;
        }

    }else{
        $("#server_user-pass_messages").text("");
        $("#server_user-pass_messages").append("Проверьте правильность данных в полях ввода пароля!");
        return;
    }
    $.ajax({
        url: "cabinet/",
        data: "type=add&content="+JSON.stringify(ngModel)+'&auth_token='+$("#user_token").val(),
        success: function(data)
        {
            var msg = JSON.parse(data);
            $("#messages-server").text("");
            $("#messages-server").append(msg["content"]);
            $("#user_control_place").text("");
            $("#messages-server").text("");
            $("#messages-server").append('Обновление...');
            $("#user_control_place").text("");
            load_data('user_control');
            $("#messages-server").text("");
            $("#messages-server").append('Данные обновлены!');
        },
        error: function()
        {
            $("#messages-server").text("");
            $("#messages-server").append("Ошибка связи с сервером, перезагрузите страницу");
        }
    });
}

function AddMeta()
{
    var ngModel = {
        action: 'newmeta',
        description: '',
        content: '',
        isActive: ''
    };
    $("#server_meta-description_messages").text("");
    $("#server_meta-content_messages").text("");
    if($("#meta-description").val() !== "")
    {
        ngModel.description = $("#meta-description").val();
    }else{
        $("#server_meta-description_messages").text("");
        $("#server_meta-description_messages").text("Вы пропустили это поле!");
        return;
    }
    if($(".Editor-editor").html() !== "")
    {
        ngModel.content = $(".Editor-editor").html();
    }else{
        $("#server_meta-content_messages").text("");
        $("#server_meta-content_messages").text("Вы пропустили это поле!");
        return;
    }
    if(document.getElementById('meta-status').checked == true)
    {
        ngModel.isActive = '1';
    }else{
        ngModel.isActive = '0';
    }
    $.ajax({
        url: "cabinet/",
        data: "type=add&content="+JSON.stringify(ngModel)+'&auth_token='+$("#user_token").val(),
        success: function(data)
        {
            var msg = JSON.parse(data);
            $("#messages-server").text("");
            $("#messages-server").append(msg["content"]);
            $("#user_control_place").text("");
            $("#messages-server").text("");
            $("#messages-server").append('Обновление...');
            $("#metatdata_control_place").text("");
            load_data('metatdata_control');
            $("#messages-server").text("");
            $("#messages-server").append('Данные обновлены!');
        },
        error: function()
        {
            $("#messages-server").text("");
            $("#messages-server").append("Ошибка связи с сервером, перезагрузите страницу");
        }
    });
}

function AddPeriod()
{
    var ngModel = {
        action: 'newperiod',
        content: '',
        isActive: ''
    };
    $("#server_period-description_messages").text("");
    if($("#period-content").val() !== "")
    {
        ngModel.content = $("#period-content").val();
    }else{
        $("#server_period-description_messages").text("");
        $("#server_period-description_messages").text("Вы пропустили это поле!");
    }
    if(document.getElementById('period-status').checked == true)
    {
        ngModel.isActive = '1';
    }else{
        ngModel.isActive = '0';
    }
    $.ajax({
        url: "cabinet/",
        data: "type=add&content="+JSON.stringify(ngModel)+'&auth_token='+$("#user_token").val(),
        success: function(data)
        {
            var msg = JSON.parse(data);
            $("#messages-server").text("");
            $("#messages-server").append(msg["content"]);
            $("#total_periods_place").text("");
            $("#messages-server").text("");
            $("#messages-server").append('Обновление...');
            $("#metatdata_control_place").text("");
            load_data('total_periods');
            $("#messages-server").text("");
            $("#messages-server").append('Данные обновлены!');
        },
        error: function()
        {
            $("#messages-server").text("");
            $("#messages-server").append("Ошибка связи с сервером, перезагрузите страницу");
        }
    });
}

function AddTable()
{
    var ngModel = {
        action: 'newtable',
        description: '',
        content: '',
        isActive: ''
    };
    $("#server_table-description_messages").text("");
    $("#server_table-content_messages").text("");
    if($("#table-description").val() !== "")
    {
        ngModel.description = $("#table-description").val();
    }else{
        $("#server_table-description_messages").text("");
        $("#server_table-description_messages").text("Вы пропустили это поле!");
        return;
    }
    if($(document.getElementsByClassName('Editor-editor')).get()["0"]["innerHTML"] !== "")
    {
        ngModel.content = $(document.getElementsByClassName('Editor-editor')).get()["0"]["innerHTML"].replace('/"/g', "'");
    }else{
        $("#server_table-content_messages").text("");
        $("#server_table-content_messages").text("Вы пропустили это поле!");
        return;
    }
    if(document.getElementById('table-status').checked == true)
    {
        ngModel.isActive = '1';
    }else{
        ngModel.isActive = '0';
    }
    $.ajax({
        url: "cabinet/",
        dataType: 'html',
        data: "type=add&content="+JSON.stringify(ngModel)+'&auth_token='+$("#user_token").val(),
        success: function(data)
        {
            var msg = JSON.parse(data);
            $("#messages-server").text("");
            $("#messages-server").append(msg["content"]);
            $("#tables_control_place").text("");
            $("#messages-server").text("");
            $("#messages-server").append('Обновление...');
            $("#metatdata_control_place").text("");
            load_data('tables_control');
            $("#messages-server").text("");
            $("#messages-server").append('Данные обновлены!');
            $(".fade .in").css("display", "none");
        },
        error: function()
        {
            $("#messages-server").text("");
            $("#messages-server").append("Ошибка связи с сервером, перезагрузите страницу");
        }
    });
}

function AddCourse() {
    var ngModel = {
        action: "newcourse",
        coursename: "",
        periods: {},
        type: "",
        price: "",
        discount: "",
        total: "",
        minClass: 0,
        maxClass: 0,
        tableTemplate: "",
        metaText: ""
    };
    $("#course-name_errors").text("");
    $("#course-period_errors").text("");
    $("#course-group_errors").text("");
    $("#course-price_errors").text("");
    $("#course-discount_errors").text("");
    $("#course-total_errors").text("");
    $("#course-min_errors").text("");
    $("#course-max_errors").text("");
    $("#course-table_errors").text("");
    if($("#course-name").val() !== "")
    {
        ngModel.coursename = $("#course-name").val();
    }else{
        $("#course-name").focus();
        $("#course-name_errors").text("Вы пропустили это поле!");
        return;
    }
    if($("#injected-periods").val() !== "")
    {
        ngModel.periods = JSON.parse($("#injected-periods").val());
    }else{
        $("#course-period_errors").text("Вы не выбрали ни одного периода!");
        $("#periods-course").focus();
        return;
    }
    if($("#course-type").val() !== "null" && $("#course-type").val() !== "addnew")
    {
        ngModel.type = $("#course-type").val();
    }else{
        $("#course-group_errors").text("Вы не выбрали ни одного типа!");
        $("#course-type").focus();
        return;
    }
    if($("#course-price").val() !== "")
    {
        if($("#course-price").val() < 1)
        {
            $("#course-price_errors").text("Цена не может быть отрицательной, или быть равной нулю");
            $("#course-price").focus();
            return;
        }
        ngModel.price = $("#course-price").val();
    }else{
        $("#course-price_errors").text("Вы ничего не ввели в это поле");
        $("#course-price").focus();
        return;
    }
    if($("#course-discount").val() !== "")
    {
        if($("#course-discount").val() < 0)
        {
            $("#course-discount_errors").text("Скидка не может быть отрицательной!");
            $("#course-discount").focus();
            return;
        }
        ngModel.discount = $("#course-discount").val();
    }else{
        $("#course-discount_errors").text("Вы пропустили это поле!");
        $("#course-discount").focus();
        return;
    }
    if($("#course-lessons").val() !== "")
    {
        if($("#course-lessons").val() < 1)
        {
            $("#course-total_errors").text("Кол-во уроков не может быть равным или быть меньше чем 0");
            $("#course-lessons").focus();
            return;
        }
        ngModel.total = $("#course-lessons").val();
    }else{
        $("#course-total_errors").text("Вы пропусили это поле!");
        $("#course-lessons").focus();
        return;
    }
    if($("#course-min").val() !== "")
    {
        if($("#course-max").val() !== "")
        {
            if(Number($("#course-min").val()) > Number($("#course-max").val()))
            {
                $("#course-min_errors").text("Минимальный класс не может быть равен максимальному и не может превышать его!");
                $("#course-min").focus();
                return;
            }
        }
        if($("#course-min").val() < 1 || $("#course-min").val() > 11)
        {
            $("#course-min_errors").text("Минимальный класс не может быть больше 11 и меньше 1!");
            $("#course-min").focus();
            return;
        }
        ngModel.minClass = $("#course-min").val();
    }else{
        $("#course-min_errors").text("Вы пропустили это поле!");
        $("#course-min").focus();
        return;
    }
    if($("#course-max").val() !== "")
    {
        if(Number($("#course-max").val()) <= Number($("#course-min").val()))
        {
            $("#course-max_errors").text("Максимальный класс не может быть меньше или быть равен минимальному классу!");
            $("#course-max").focus();
            return;
        }
        if($("#course-max").val() > 11 || $("#course-max").val() < 1)
        {
            $("#course-max_errors").text("Максимальный класс не может быть меньше 1 или больше 11");
            $("#course-max").focus();
            return;
        }
        ngModel.maxClass = $("#course-max").val();
    }else{
        $("#course-max_errors").text("Вы пропустили это поле!");
        $("#course-max").focus();
        return;
    }
    if($("#course-table").val() !== "")
    {
        ngModel.tableTemplate = $("#course-table").val();
    }else{
        $("#course-table_error").text("вы не выбрали ни одной таблицы!");
        $("#course-table").focus();
        return;
    }
    if($("#course-meta").val() !== '')
    {
        ngModel.metaText = $("#course-meta").val();
    }else{
        ngModel.metaText = '0';
    }
    $.ajax({
        url: "cabinet/",
        dataType: 'html',
        data: "type=add&content="+JSON.stringify(ngModel)+'&auth_token='+$("#user_token").val(),
        success: function(data)
        {
            var msg = JSON.parse(data);
            $("#messages-server").text("");
            $("#messages-server").append(msg["content"]);
            if(msg["is_success"] == true)
            {
                $("#messages-server").text("");
                $("#messages-server").append('Обновление...');
                $("#courses_list_place").text("");
                load_data('courses_list');
                $("#messages-server").text("");
                $("#messages-server").append('Данные обновлены!');
                $(".fade .in").css("display", "none");
            }else{
                $("#messages-server").text("");
                $("#messages-server").append(msg["content"]);
            }

        },
        error: function()
        {
            $("#messages-server").text("");
            $("#messages-server").append("Ошибка связи с сервером, перезагрузите страницу");
        }
    });
}

function DeleteObject(type, id, e)
{
    $.ajax({
        url: "cabinet/",
        data: "type=deleteaction&object="+type+"&id="+id+"&tauth_token="+$("#user_token").val(),
        success: function(msg)
        {
            var data = JSON.parse(msg);
            if(data["is_success"] == 'true')
            {
                $("#"+e+"_place").text("");
                load_data(e);
            }else{
                alert("Ошибка удаления, повторите попытку удаления позже.");
            }

        }
    });
}

function UpdateUser()
{
    var ngModel = {
        action: "updateuser",
        login: "",
        pass: "",
        isNewPass: false,
        role: "",
        id: ""
    };
    $("#btn---loader").css("display", "");
    $("#btn---admin").prop('disabled', 'true');
    $("#user-login-error").text("");
    $("#user-login-pass").text("");
    if($("#user_id").val() == "")
    {
        $("#messages_place").text("");
        $("#messages_place").text("критическая ошибка сервера, выполнение операции невозможно.");
    }
    if($("#user-login").val() !== "")
    {
        ngModel.login = $("#user-login").val();
    }else{
        $("#user-login-error").text("Это поле не может быть пустым!");
        return;
    }
    ngModel.role = $("#user-role").val();
    if($("#user-pass-1").val() !== "" || $("#user-pass-2").val() !== "")
    {
        if($("#user-pass-1").val() == $("#user-pass-2").val())
        {
            ngModel.pass = $("#user-pass-1").val();
            ngModel.isNewPass = true;
        }else{
            $("#user-pass-error").text("Введённые пароли не совпадают!");
            return;
        }
    }else{
        ngModel.pass = $("#user_pass").val();
        ngModel.isNewPass = false;
    }
    ngModel.id = $("#user_id").val();
    $.ajax({
        url: "updatecore/",
        data: "type=UpdateCore&observer=UserUpdateCore&content="+JSON.stringify(ngModel),
        success: function(data)
        {
            var msg = JSON.parse(data);
            if(msg["is_success"] == true)
            {
                $("#user_control_place").text("");
                load_data('user_control');
                $("#btn---loader").css("display", "none");
                document.getElementById('btn---admin').disabled = false;
                $("#messages_place").text("");
                $("#messages_place").text("Данные успешно обновлены!");
            }else{
                $("#messages_place").text("");
                $("#messages_place").text(msg["message"]);
            }
        }
    });
}
function UpdatePeriod()
{
    var ngModel = {
        content: "",
        isActive: true,
        id: ""
    };
    $("#btn---loader").css("display", "");
    $("#btn---admin").prop('disabled', 'true');
    $("#period-content-errors").text("");
    if($("#period_id").val() == "")
    {
        $("#messages_place").text("");
        $("#messages_place").text("критическая ошибка сервера, выполнение операции невозможно.");
    }
    if($("#period-content").val() !== "")
    {
        ngModel.content = $("#period-content").val();
    }else{
        $("#period-content-errors").text("Вы пропустили это поле!");
        return;
    }
    ngModel.isActive = document.getElementById("period-status").checked;
    ngModel.id = $("#period_id").val();
    $.ajax({
        url: "updatecore/",
        data: "type=UpdateCore&observer=PeriodUpdateCore&content="+JSON.stringify(ngModel),
        success: function(data)
        {
            var msg = JSON.parse(data);
            if(msg["is_success"] == true)
            {
                $("#total_periods_place").text("");
                load_data('total_periods');
                $("#btn---loader").css("display", "none");
                document.getElementById('btn---admin').disabled = false;
                $("#messages_place").text("");
                $("#messages_place").text("Данные успешно обновлены!");
            }else{
                $("#messages_place").text("");
                $("#messages_place").text(msg["message"]);
            }
        }
    });
}

function UpdateMeta()
{
    var ngModel = {
        id: "",
        description: "",
        content: "",
        isActive: true
    };
    $("#btn---loader").css("display", "");
    $("#btn---admin").prop('disabled', 'true');
    $("#meta-description-errors").text("");
    $("#meta-content-errors").text("");
    if($("#meta_id").val() == "")
    {
        $("#messages_place").text("");
        $("#messages_place").text("критическая ошибка сервера, выполнение операции невозможно.");
    }
    if($("#meta-description").val() !== "")
    {
        ngModel.description = $("#meta-description").val();
    }else{
        $("#meta-description-errors").text("Вы пропустили это поле!");
        return;
    }
    if($(".Editor-editor").html() !== "")
    {
        ngModel.content = $(".Editor-editor").html();
    }else{
        $("#meta-content-errors").text("Вы пропустили это поле!");
        return;
    }
    ngModel.isActive = document.getElementById('meta-status').checked;
    ngModel.id = $("#meta_id").val();
    $.ajax({
        url: "updatecore/",
        data: "type=UpdateCore&observer=MetaUpdateCore&content="+JSON.stringify(ngModel),
        success: function(data)
        {
            var msg = JSON.parse(data);
            if(msg["is_success"] == true)
            {
                $("#metatdata_control_place").text("");
                load_data('metatdata_control');
                $("#btn---loader").css("display", "none");
                document.getElementById('btn---admin').disabled = false;
                $("#messages_place").text("");
                $("#messages_place").text("Данные успешно обновлены!");
            }else{
                $("#messages_place").text("");
                $("#messages_place").text(msg["message"]);
            }
        }
    });
}

function UpdateHoockedTable()
{
    var ngModel = {
        id: "",
        description: "",
        content: "",
        isActive: true
    };
    $("#btn---loader").css("display", "");
    $("#btn---admin").prop('disabled', 'true');
    $("#table-description-errors").text("");
    $("#table-template-errors").text("");
    if($("#table_id").val() == "")
    {
        $("#messages_place").text("");
        $("#messages_place").text("критическая ошибка сервера, выполнение операции невозможно.");
    }
    if($("#table-description").val() !== "")
    {
        ngModel.description = $("#table-description").val();
    }else{
        $("#table-description-errors").text("Вы пропустили это поле!");
        return;
    }
    if($(".Editor-editor").html() !== "")
    {
        ngModel.content = $(".Editor-editor").html();
    }else{
        $("#table-template-errors").text("Вы пропустили это поле!");
        return;
    }
    ngModel.isActive = document.getElementById('table-status').checked;
    ngModel.id = $("#table_id").val();
    $.ajax({
        url: "updatecore/",
        data: "type=UpdateCore&observer=TableUpdateCore&content="+JSON.stringify(ngModel),
        success: function(data)
        {
            var msg = JSON.parse(data);
            if(msg["is_success"] == true)
            {
                $("#tables_control_place").text("");
                load_data('tables_control');
                $("#btn---loader").css("display", "none");
                document.getElementById('btn---admin').disabled = false;
                $("#messages_place").text("");
                $("#messages_place").text("Данные успешно обновлены!");
            }else{
                $("#messages_place").text("");
                $("#messages_place").text(msg["message"]);
            }
        }
    });
}

function AnaliseSelectCoursesPeriod()
{
    $("#course-select-errors").text("");
    var injectVals;
    if($("#periods-course").val() == null)
    {
        return;
    }
    var selectVal = $("#periods-course").val();
    if($("#course-encodes").val() !== "")
    {
        var allVals = JSON.parse($("#course-encodes").val());
    }else{
        $("#course-select-errors").text("Ошибка, не удаётся получить списки доступных периодов, попробуйте переоткрыть курс");
        return;
    }
    if($("#injected-periods").val() !== "")
    {
        injectVals = JSON.parse($("#injected-periods").val());
    }else{
        injectVals = null;
    }
    $('#periods-course').find('option').remove();
    $('#periods-course').append('<option value="null">--Не выбран--</option>');
    var injectableVals = '';
    if(injectVals == null)
    {
        bind('add', allVals, selectVal, null);
        return;
    }else{
        bind('add', allVals, selectVal, injectVals);
    }
}

function bind(algo, allVals, selectedVal, injectableVals)
{
    if(algo == "add")
    {
        if(injectableVals == null)
        {
            $("#injected-periods").val(JSON.stringify({1: {id:selectedVal}}));
            draw('ui', null, selectedVal);
            draw('sel', null, selectedVal);
        }else{
            if(injectableVals !== "")
            {
                var injected = {};
                var c = 1;
                var turn = true;
                while(turn)
                {
                    if(injectableVals[c] !== undefined)
                    {
                        if(allVals[c]["id"] == selectedVal)
                        {
                            injected[c] = {id: allVals[c]["id"]};
                        }
                        if(injectableVals[c]["id"] == allVals[c]["id"])
                        {
                            injected[c] = {id: allVals[c]["id"]};
                        }
                    }else{
                        if(allVals[c]["id"] == selectedVal)
                        {
                            injected[c] = {id: allVals[c]["id"]};
                        }
                    }
                    if(allVals[c+1] == undefined)
                    {
                        turn = false;
                    }
                    c++;
                }
                draw('ui', injected, null);
                draw('sel', injected, null);
                $("#injected-periods").val(JSON.stringify(injected));
            }else{
                return;
            }
        }
    }
    if(algo == "del")
    {
        var c = 1;
        var turn = true;
        var injected = {};
        while(turn)
        {
            if(injectableVals[c] !== undefined)
            {
                if(selectedVal !== injectableVals[c]["id"])
                {
                    injected[c] = {id: allVals[c]["id"]};
                }
            }
            if(allVals[c+1] == undefined)
            {
                turn = false;
            }
            c++;
        }
        draw('ui', injected, null);
        draw('sel', injected, null);
        $("#injected-periods").val(JSON.stringify(injected));
    }
}

function draw(type, algo, itemId)
{
        if(type == 'ui')
        {
            if(algo == null)
            {
                var content = get_content_from_all_val(itemId);
                if(content == 'error')
                {
                    return;
                }
                $("#periods-list-for-user").append('<div class="period-item" id="period-item-'+itemId+'"><div class="period-content">'+content+'</div><div class="period-delete"><span class="glyphicon glyphicon-remove-sign custom-remove-sigh" onclick="DeletePeriod(\''+itemId+'\')"></span></div></div>');
                return;
            }else{
                $("#periods-list-for-user").text("");
                for(var i in algo)
                {
                    $("#periods-list-for-user").append('<div class="period-item" id="period-item-'+algo[i]["id"]+'"><div class="period-content">'+get_content_from_all_val(algo[i]["id"])+'</div><div class="period-delete"><span class="glyphicon glyphicon-remove-sign custom-remove-sigh" onclick="DeletePeriod(\''+algo[i]["id"]+'\')"></span></div></div>');
                }
            }
        }
        if(type == 'sel')
        {
            if(algo == null)
            {
                alert();
                var allVals = JSON.parse($("#course-encodes").val());
                for (var i in allVals)
                {
                    if(allVals[i]["id"] !== itemId)
                    {
                        $("#periods-course").append('<option value="'+allVals[i]["id"]+'">'+allVals[i]["content"]+'</option>');
                    }
                }
            }else{
                var allVals = JSON.parse($("#course-encodes").val());
                var c = 1;
                var turn =true;
                while(turn)
                {
                    if(algo[c] !== undefined)
                    {
                        if(algo[c]["id"] !== allVals[c]["id"])
                        {
                            $("#periods-course").append('<option value="'+allVals[c]["id"]+'">'+allVals[c]["content"]+'</option>');
                        }
                    }else{
                        $("#periods-course").append('<option value="'+allVals[c]["id"]+'">'+allVals[c]["content"]+'</option>');
                    }
                    if(allVals[c+1] == undefined)
                    {
                        turn = false;
                    }
                    c++;
                }
            }
        }
    $("#injected-periods").val(JSON.stringify(algo));
}

function get_content_from_all_val(id)
{
    var j = JSON.parse($("#course-encodes").val());
    for (var i in j)
    {
        if(j[i]["id"] == id)
        {
            return j[i]["content"];
        }
    }
    return 'error';
}

function DeletePeriod(id)
{
    $('#periods-course').find('option').remove();
    $('#periods-course').append('<option value="null">--Не выбран--</option>');
    bind('del', JSON.parse($("#course-encodes").val()), id, JSON.parse($("#injected-periods").val()));
}

function AnaliseCourseTypes()
{
    if($("#course-type").val() == 'addnew')
    {
        $("#course--types-opacity").css("display", "");
        $("#addplace").slideToggle('fast');
    }
}

function RollTypesSate()
{
    $("#course--types-opacity").css("display", "none");
    $("#addplace").slideToggle('fast');
}

function AddType()
{
    var ngModel = {
        action: "newtype",
        content: "",
        isActive: true
    };
    $("#server-type-messages").text("");
    if($("#type---global").val() !== "")
    {
        ngModel.content = $("#type---global").val();
    }else{
        $("#server-type-messages").text("Вы пропустили поле ввода типа");
        return;
    }
    $.ajax({
        url: "cabinet/",
        data: "type=add&content="+JSON.stringify(ngModel)+'&auth_token='+$("#user_token").val(),
        success: function(data)
        {
            var msg = JSON.parse(data);
            $("#server-type-messages").text("");
            $("#server-type-messages").append(msg["content"]);
            UpdateTypes();
        },
        error: function()
        {
            $("#server-type-messages").text("");
            $("#server-type-messages").append("Ошибка связи с сервером, перезагрузите страницу");
        }
    });
}

function UpdateTypes()
{
    $.ajax({
        url: "cabinet/",
        data: "type=get&item=types",
        success: function(data)
        {
            var msg = JSON.parse(data);
                $("#course-type").find("option").remove();
                $("#course-type").append(msg["content"]);
                $("#server-type-messages").text("");
                $("#server-type-messages").append('Данные обновлены');
        }
    });
}

function AnaliseSpecialSelectPeriods()
{
    if($("#special-command").val() == 'null')
    {
        if(document.getElementById('period-total-settings').style.display == 'none')
        {
            $("#period-total-settings").slideToggle("fast");
            if(document.getElementById("button-global-settings").style.display !== "none")
            {
                $("#button-global-settings").slideToggle("fast");
            }
            if(document.getElementById("button-total-settings").style.display == "none")
            {
                $("#button-total-settings").slideToggle("fast");
            }
            if(document.getElementById("course-select").style.display !== "none")
            {
                $("#course-select").slideToggle("fast");
            }
            return;
        }else{
            if(document.getElementById("button-global-settings").style.display == "none")
            {
                $("#button-global-settings").slideToggle("fast");
            }
            if(document.getElementById("course-select").style.display == "none")
            {
                $("#course-select").slideToggle("fast");
            }
            return;
        }
    }else if($("#special-command").val() == 'course')
    {
        if(document.getElementById("period-total-settings").style.display !== "none")
        {
            $("#period-total-settings").slideToggle("fast");
        }
        if(document.getElementById("course-select").style.display == "none")
        {
            $("#course-select").slideToggle("fast");
        }
        if(document.getElementById("button-global-settings").style.display == "none")
        {
            $("#button-global-settings").slideToggle("fast");
        }
        if(document.getElementById("button-total-settings").style.display !== "none")
        {
            $("#button-total-settings").slideToggle("fast");
        }
    }else{
        if(document.getElementById("button-global-settings").style.display == "none")
        {
            $("#button-global-settings").slideToggle("fast");
        }
        if(document.getElementById("button-total-settings").style.display !== "none")
        {
            $("#button-total-settings").slideToggle("fast");
        }
        if(document.getElementById("period-total-settings").style.display !== "none")
        {
            $("#period-total-settings").slideToggle("fast");
        }
        if(document.getElementById("course-select").style.display !== "none")
        {
            $("#course-select").slideToggle("fast");
        }
    }
}

function AnaliseSpecialSelectTables()
{
    if($("#special-command").val() == 'null')
    {
        if(document.getElementById('table-total-settings').style.display == 'none')
        {
            $("#table-total-settings").slideToggle("fast");
            if(document.getElementById("button-global-settings").style.display !== "none")
            {
                $("#button-global-settings").slideToggle("fast");
            }
            if(document.getElementById("button-total-settings").style.display == "none")
            {
                $("#button-total-settings").slideToggle("fast");
            }
            if(document.getElementById("course-select").style.display !== "none")
            {
                $("#course-select").slideToggle("fast");
            }
            if(document.getElementById('period-select-d').style.display !== "none")
            {
                $("#period-select-d").slideToggle("fast");
            }
            return;
        }else{
            if(document.getElementById("button-global-settings").style.display == "none")
            {
                $("#button-global-settings").slideToggle("fast");
            }
            if(document.getElementById("course-select").style.display == "none")
            {
                $("#course-select").slideToggle("fast");
            }
            if(document.getElementById('period-select-d').style.display !== "none")
            {
                $("#period-select-d").slideToggle("fast");
            }
            return;
        }
    }else if($("#special-command").val() == 'course')
    {
        if(document.getElementById("table-total-settings").style.display !== "none")
        {
            $("#table-total-settings").slideToggle("fast");
        }
        if(document.getElementById("course-select").style.display == "none")
        {
            $("#course-select").slideToggle("fast");
        }
        if(document.getElementById("button-global-settings").style.display == "none")
        {
            $("#button-global-settings").slideToggle("fast");
        }
        if(document.getElementById("button-total-settings").style.display !== "none")
        {
            $("#button-total-settings").slideToggle("fast");
        }
        if(document.getElementById('period-select-d').style.display == "none")
        {
            $("#period-select-d").slideToggle("fast");
        }
    }else{
        if(document.getElementById("button-global-settings").style.display == "none")
        {
            $("#button-global-settings").slideToggle("fast");
        }
        if(document.getElementById("button-total-settings").style.display !== "none")
        {
            $("#button-total-settings").slideToggle("fast");
        }
        if(document.getElementById("table-total-settings").style.display !== "none")
        {
            $("#table-total-settings").slideToggle("fast");
        }
        if(document.getElementById("course-select").style.display !== "none")
        {
            $("#course-select").slideToggle("fast");
        }
        if(document.getElementById('period-select-d').style.display == "none")
        {
            $("#period-select-d").slideToggle("fast");
        }
    }
}

function AnaliseSpecialSelectMeta()
{
    if($("#special-command").val() == 'null')
    {
        if(document.getElementById('meta-total-settings').style.display == 'none')
        {
            $("#meta-total-settings").slideToggle("fast");
            if(document.getElementById("button-global-settings").style.display !== "none")
            {
                $("#button-global-settings").slideToggle("fast");
            }
            if(document.getElementById("button-total-settings").style.display == "none")
            {
                $("#button-total-settings").slideToggle("fast");
            }
            if(document.getElementById("course-select").style.display !== "none")
            {
                $("#course-select").slideToggle("fast");
            }
            if(document.getElementById('period-select-d').style.display !== "none")
            {
                $("#period-select-d").slideToggle("fast");
            }
            return;
        }else{
            if(document.getElementById("button-global-settings").style.display == "none")
            {
                $("#button-global-settings").slideToggle("fast");
            }
            if(document.getElementById("course-select").style.display == "none")
            {
                $("#course-select").slideToggle("fast");
            }
            if(document.getElementById('period-select-d').style.display !== "none")
            {
                $("#period-select-d").slideToggle("fast");
            }
            return;
        }
    }else if($("#special-command").val() == 'course')
    {
        if(document.getElementById("meta-total-settings").style.display !== "none")
        {
            $("#meta-total-settings").slideToggle("fast");
        }
        if(document.getElementById("course-select").style.display == "none")
        {
            $("#course-select").slideToggle("fast");
        }
        if(document.getElementById("button-global-settings").style.display == "none")
        {
            $("#button-global-settings").slideToggle("fast");
        }
        if(document.getElementById("button-total-settings").style.display !== "none")
        {
            $("#button-total-settings").slideToggle("fast");
        }
        if(document.getElementById('period-select-d').style.display == "none")
        {
            $("#period-select-d").slideToggle("fast");
        }
    }else{
        if(document.getElementById("button-global-settings").style.display == "none")
        {
            $("#button-global-settings").slideToggle("fast");
        }
        if(document.getElementById("button-total-settings").style.display !== "none")
        {
            $("#button-total-settings").slideToggle("fast");
        }
        if(document.getElementById("meta-total-settings").style.display !== "none")
        {
            $("#meta-total-settings").slideToggle("fast");
        }
        if(document.getElementById("course-select").style.display !== "none")
        {
            $("#course-select").slideToggle("fast");
        }
        if(document.getElementById('period-select-d').style.display == "none")
        {
            $("#period-select-d").slideToggle("fast");
        }
    }
}

function UpdateGlobalPeriod()
{
    var periodModel = {
        periodId: 0,
        algo: null,
        metaContent: null
    };
    $("#messages_place").text("");
    if($("#special-command").val() !== "null")
    {
        if($("#special-command").val() == "course")
        {
            if($("#course-selecter").val() !== "none")
            {
                periodModel.metaContent = $("#course-selecter").val();
            }else{
                $("#messages_place").text("Этот период уже был применён ко всем объектам в бд! Операция невозможна.");
                return;
            }
        }else{
            periodModel.algo = $("#special-command").val();
        }
    }else{
        $("#messages_place").text("Ошибка парсинга поля, попробуйте переоткрыть окно редактирования периода.");
        return;
    }
    periodModel.periodId = $("#period_id").val();
    $("#btn---loader").css("display", "");
    document.getElementById('btn---admin-global').disabled = true;
    $.ajax({
        url: "updatecore/",
        data: "type=GlobalUpdateCore&observer=UpdatePeriodGlobalCore&content="+JSON.stringify(periodModel),
        success: function(data)
        {
            var msg = JSON.parse(data);
            if(msg["is_success"] == true)
            {
                $("#btn---loader").css("display", "none");
                document.getElementById('btn---admin-global').disabled = false;
                $("#messages_place").text("");
                $("#messages_place").text("Данные успешно обновлены!");
            }else{
                $("#btn---loader").css("display", "none");
                document.getElementById('btn---admin-global').disabled = false;
                $("#messages_place").text("");
                $("#messages_place").text(msg["content"]);
            }

        }
    });
}

function UpdateGlobalTable()
{
    var periodModel = {
        tableId: 0,
        algo: null,
        period: null,
        metaContent: null
    };
    $("#messages_place").text("");
    if($("#special-command").val() !== "null")
    {
        if($("#special-command").val() == "course")
        {
            if($("#course-selecter").val() !== "none")
            {
                periodModel.metaContent = $("#course-selecter").val();
            }else{
                $("#messages_place").text("Этот период уже был применён ко всем объектам в бд! Операция невозможна.");
                return;
            }
            periodModel.algo = $("#special-command").val();
        }else{
            periodModel.algo = $("#special-command").val();
        }
    }else{
        $("#messages_place").text("Ошибка парсинга поля, попробуйте переоткрыть окно редактирования периода.");
        return;
    }
    if($("#period-select").val() !== "none")
    {
        periodModel.period = $("#period-select").val();
    }else{
        $("#messages_place").text("Вы не выбрали период!");
        return;
    }
    periodModel.tableId = $("#table_id").val();
    $("#btn---loader").css("display", "");
    document.getElementById('btn---admin-global').disabled = true;
    $.ajax({
        url: "updatecore/",
        data: "type=GlobalUpdateCore&observer=UpdateTableGlobalCore&content="+JSON.stringify(periodModel),
        success: function(data)
        {
            var msg = JSON.parse(data);
            if(msg["is_success"] == true)
            {
                $("#btn---loader").css("display", "none");
                document.getElementById('btn---admin-global').disabled = false;
                $("#messages_place").text("");
                $("#messages_place").text("Данные успешно обновлены!");
            }else{
                $("#btn---loader").css("display", "none");
                document.getElementById('btn---admin-global').disabled = false;
                $("#messages_place").text("");
                $("#messages_place").text(msg["content"]);
            }

        }
    });
}

function UpdateGlobalMeta()
{
    var periodModel = {
        metaId: 0,
        algo: null,
        period: null,
        metaContent: null
    };
    $("#messages_place").text("");
    if($("#special-command").val() !== "null")
    {
        if($("#special-command").val() == "course")
        {
            if($("#course-selecter").val() !== "none")
            {
                periodModel.metaContent = $("#course-selecter").val();
            }else{
                $("#messages_place").text("Этот период уже был применён ко всем объектам в бд! Операция невозможна.");
                return;
            }
            periodModel.algo = $("#special-command").val();
        }else{
            periodModel.algo = $("#special-command").val();
        }
    }else{
        $("#messages_place").text("Ошибка парсинга поля, попробуйте переоткрыть окно редактирования периода.");
        return;
    }
    if($("#period-select").val() !== "none")
    {
        periodModel.period = $("#period-select").val();
    }else{
        $("#messages_place").text("Вы не выбрали период!");
        return;
    }
    periodModel.metaId = $("#meta_id").val();
    $("#btn---loader").css("display", "");
    document.getElementById('btn---admin-global').disabled = true;
    $.ajax({
        url: "updatecore/",
        data: "type=GlobalUpdateCore&observer=UpdateMetaGlobalCore&content="+JSON.stringify(periodModel),
        success: function(data)
        {
            var msg = JSON.parse(data);
            if(msg["is_success"] == true)
            {
                $("#btn---loader").css("display", "none");
                document.getElementById('btn---admin-global').disabled = false;
                $("#messages_place").text("");
                $("#messages_place").text("Данные успешно обновлены!");
            }else{
                $("#btn---loader").css("display", "none");
                document.getElementById('btn---admin-global').disabled = false;
                $("#messages_place").text("");
                $("#messages_place").text(msg["content"]);
            }

        }
    });
}

function loadAs(userdata)
{
    var load = confirm("Вы действительно хотите загрузиться как пользователь "+userdata+"?");
    if(load)
    {
        $.ajax({
            url: "cabinet/",
            data: "type=loadAsEditor&name="+userdata,
            success: function(data)
            {
                var msg = JSON.parse(data);
                if(msg["is_success"] == true)
                {
                    location.href = msg["content"];
                }else{
                    alert(msg["content"]);
                }
            }
        });
    }else{
        return;
    }
}

function LoadCourseTemplate()
{
    if($("#course-template-selector").val() !== "null")
    {
        $("#btn--loader").css("display", "");
        $.ajax({
            url: "cabinet/",
            data: "type=getCourseTemplate&course="+$("#course-template-selector").val(),
            success: function(data)
            {
                var msg = JSON.parse(data);
                if(msg["content"] !== undefined)
                {
                    $("#course-name").val(msg["content"]);
                }else{
                    $("#course-name").val("#client#parse#error");
                }
                if(msg["years"]["allperiods"] !== undefined && msg["years"]["injectedPeriods"] !== undefined)
                {
                    $("#course-encodes").val(msg["years"]["allperiods"]);
                    $("#injected-periods").val(JSON.stringify(msg["years"]["injectedPeriods"]));
                    $('#periods-course').find('option').remove();
                    $('#periods-course').append('<option value="null">--Не выбран--</option>');
                    draw('ui', msg["years"]["injectedPeriods"], null);
                    draw('sel', msg["years"]["injectedPeriods"], null);
                }else{
                    $("#course-encodes").val(JSON.stringify({"error":"error"}));
                    $("#injected-periods").val(JSON.stringify({"error":"error"}));
                }
                if(msg["types"]["selected_type"] !== undefined && msg["types"]["alltypes"]["types"] !== undefined)
                {
                    $("#course-type").find('option').remove();
                    $("#course-type").append('<option value="null">--Не выбран--</option>');
                    if(msg["types"]["alltypes"]["type_status"] == false)
                    {
                        $("#course-type").append('<opiton value="1">Курсы</opiton>');
                    }else{
                        var types = msg["types"]["alltypes"]["types"];
                        for (var i in types)
                        {
                            if(types[i]["attr"] == "selected")
                            {
                                $("#course-type").append('<option value="'+types[i]["id"]+'" selected>'+types[i]["description"]+'</option>');
                            }else{
                                $("#course-type").append('<option value="'+types[i]["id"]+'">'+types[i]["description"]+'</option>');
                            }
                        }
                        $("#course-type").append('<option value="addnew">Добавить новый тип</option>');
                    }
                }
                if(msg["price"] !== undefined)
                {
                    $("#course-price").val(msg["price"]);
                }else{
                    $("#course-price").val(-2);
                }
                if(msg["discount"] !== undefined)
                {
                    $("#course-discount").val(msg["discount"]);
                }else{
                    $("#course-discount").val(-2);
                }
                if(msg["total"] !== undefined)
                {
                    $("#course-lessons").val(msg["total"]);
                }else{
                    $("#course-lessons").val(-2);
                }
                if(msg["minimum"] !== undefined)
                {
                    $("#course-min").val(msg["minimum"]);
                }else{
                    $("#course-min").val(-2);
                }
                if(msg["maximum"] !== undefined)
                {
                    $("#course-max").val(msg["maximum"]);
                }else{
                    $("#course-max").val(-2);
                }
                if(msg["hoocked_table"]["alltables"] !== undefined && msg["hoocked_table"]["alltables"]["tables"] !== undefined)
                {
                    $("#course-table").find('option').remove();
                    if(msg["hoocked_table"]["alltables"]["table_status"] == false)
                    {
                        $("#course-table").append("<option value='null'>Ни одной таблицы не найдено</option>");
                    }else{
                        var tables = msg["hoocked_table"]["alltables"]["tables"];
                        for(var i in tables)
                        {
                            if(tables[i]["attr"] == "selected")
                            {
                                $("#course-table").append('<option value="'+tables[i]["id"]+'" selected>'+tables[i]["description"]+'</option>');
                            }else{
                                $("#course-table").append('<option value="'+tables[i]["id"]+'">'+tables[i]["description"]+'</option>');
                            }
                        }
                    }
                }
                if(msg["metatexts"]["allmetatexts"] !== undefined && msg["metatexts"]["allmetatexts"]["metatexts"] !== undefined)
                {
                    $("#course-meta").find('option').remove();
                    $("#course-meta").append('<option value="null">Не выбран</option>');
                    if(msg["metatexts"]["allmetatexts"]["metatext_status"] == false)
                    {
                        $("#course-meta").append("<option value='null'>Ни одного метатекста не найдено</option>");
                    }else{
                        var meta = msg["metatexts"]["allmetatexts"]["metatexts"];
                        for(var i in meta)
                        {
                            if(meta[i]["attr"] == "selected")
                            {
                                $("#course-meta").append('<option value="'+meta[i]["id"]+'" selected>'+meta[i]["description"]+'</option>');
                            }else{
                                $("#course-meta").append('<option value="'+meta[i]["id"]+'">'+meta[i]["description"]+'</option>');
                            }
                        }
                    }
                }
                if(document.getElementById('standart-place').style.display == 'none')
                {
                    $("#standart-place").slideToggle("fast");
                }
                $("#btn--loader").css("display", "none");
            },
            error: function()
            {
                $("#course-template-errors").text("");
                $("#course-template-errors").text("Cannot estabilish connection to the server, try your action again later.");
                $("#btn--loader").css("display", "none");
            }
        });
    }else{
        if(document.getElementById('course-append-settings').style.display !== 'none')
        {
            $("#course-append-settings").slideToggle("fast");
        }
    }
}

var _modalWindow = {

};