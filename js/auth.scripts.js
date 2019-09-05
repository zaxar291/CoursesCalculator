$("#enter").click(function(){
    $.ajax({
        url: "cabinet/",
        data: "type=authmethod&login="+$("#login").val()+"&pass="+$("#pass").val()+"&dbState="+$("#dbStatus").val(),
        success: function(data)
        {
            var msg = JSON.parse(data);
            if(msg["message_type"] == "server_response")
            {
                if(msg["action_type"] == "validate_user")
                {
                    if(msg["local"] == true)
                    {
                        if(msg["access"] == true)
                        {
                            $(".auth-ob").slideToggle("fast");
                            $("#step-1").slideToggle("fast");
                        }else{
                            $(".server-messages").text("");
                            $(".server-messages").append("Введённые данные не валидны");
                        }
                    }else{
                        if(msg["access"] == true)
                        {
                            location.href = msg["url"];
                        }else{
                            $(".server-messages").text("");
                            $(".server-messages").append("Введённые данные не валидны");
                        }
                    }
                }
            }
        }
    });
});

$(".step-switch").click(function(){
    var step = $(".step-switch").data('slide');
    if(step == "1")
    {
        $("#status-bd").text("");
        $("#status-bd").text("Сбор данных..");
        $("#dbHostErrors").text("");
        $("#dbLoginErrors").text("");
        $("#dbPassErrors").text("");
        $("#dbNameErrors").text("");
        var ngModel = {
            host: '',
            login: '',
            pass: '',
            name: ''
        };
        if($("#dbHost").val() !== "")
        {
            ngModel.host = $("#dbHost").val();
        }else{
            $("#dbHostErrors").text("");
            $("#status-bd").text("");
            $("#status-bd").text("Операция провалена!");
            $("#dbHostErrors").text("Вы пропустили это поле");
            return;
        }
        if($("#dbLogin").val() !== "")
        {
            ngModel.login = $("#dbLogin").val();
        }else{
            $("#dbLoginErrors").text("");
            $("#status-bd").text("");
            $("#status-bd").text("Операция провалена!");
            $("#dbLoginErrors").text("Вы пропустили это поле");
            return;
        }
        if($("#dbPass").val() !== "")
        {
            ngModel.pass = $("#dbPass").val();
        }else{
            $("#dbPassErrors").text("");
            $("#status-bd").text("");
            $("#status-bd").text("Операция провалена!");
            $("#dbPassErrors").text("Вы пропустили это поле");
            return;
        }
        if($("#dbName").val() !== "")
        {
            ngModel.name = $("#dbName").val();
        }else{
            $("#dbNameErrors").text("");
            $("#status-bd").text("");
            $("#status-bd").text("Операция провалена!");
            $("#dbNameErrors").text("Вы пропустили это поле");
            return;
        }
        $("#status-bd").text("");
        $("#status-bd").text("Обработка данных...");
        $.ajax({
            url: "cabinet/",
            data: "type=updatedbfile&content="+JSON.stringify(ngModel),
            success: function(data)
            {
                var msg = JSON.parse(data);
                if(msg["message_type"] == "server_response")
                {
                    if(msg["response_type"] == "database_response")
                    {
                        if(msg["id_success"] == true)
                        {
                            $("#step-1").slideToggle("fast");
                            $("#step-2").slideToggle("fast");
                        }else{
                            $("#status-bd").text("");
                            $("#status-bd").text("Проверьте корректность введённых даныых, не удаётся подключится к бд");
                        }
                    }
                }
            }
        });
    }
});

$("#repeat--local").click(function(){
    $("#local--db--repeat-button_text").text("");
    $("#local--db--repeat-button_text").text("Идёт восстановление...   ");
    $("#btn---loader-local").css("display", "");
    document.getElementById('repeat--local').disabled = true;
    $.ajax({
        url: 'cabinet/',
        data: 'type=restorebd&algo=local',
        success: function (data)
        {
            var msg = JSON.parse(data);
            if(msg["is_success"] == true)
            {
                $("#local--db--repeat-button_text").text("");
                $("#local--db--repeat-button_text").text("Восстановление завершено! ");
                $("#btn---loader-local").css("display", "none");
                document.getElementById('repeat--local').disabled = false;
                $("#local-repeat-msg").text("");
                $("#local-repeat-msg").text("База данных успешно восстановлена из локальных копий, не забудте проверить работоспособность сайта.");
            }else{
                $("#local--db--repeat-button_text").text("");
                $("#local--db--repeat-button_text").text("Ошибка!");
                $("#btn---loader-local").css("display", "none");
                document.getElementById('repeat--local').disabled = false;
                $("#local-repeat-msg").text("");
                $("#local-repeat-msg").text(msg["message"]);
            }
        }
    });
});

$("#repeat--file").click(function(){
    $("#file--db--repeat-button_text").text("");
    $("#file--db--repeat-button_text").text("Идёт восстановление...   ");
    $("#btn---loader-file").css("display", "");
    document.getElementById('repeat--file').disabled = true;
    var $input = $("#db--back-file");
    var fd = new FormData;

    fd.append('dbFile', $input.prop('files')[0]);

    $.ajax({
        url: 'cabinet/',
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (data) {
            var msg = JSON.parse(data);
            if(msg["is_success"] == true)
            {
                $("#file--db--repeat-button_text").text("");
                $("#file--db--repeat-button_text").text("Завершено!");
                $("#btn---loader-file").css("display", "none");
                document.getElementById('repeat--file').disabled = false;
                $("#local-repeat-msg").text("");
                $("#local-repeat-msg").text("База данных успешно восстановлена, не забудте проверить работоспособность сайта.");
            }else{
                $("#file--db--repeat-button_text").text("");
                $("#file--db--repeat-button_text").text("Ошибка!");
                $("#btn---loader-file").css("display", "none");
                document.getElementById('repeat--file').disabled = false;
                $("#local-repeat-msg").text("");
                $("#local-repeat-msg").text(msg["message"]);
            }
        }
    });
});

$(function(){
    if($("#dbStatus").val() == "0")
    {
        $(".server-messages").text("Вы будете залогинены, как локальный администратор, бд недосутпна!");
        $(".server-messages").css("color", "red");
    }
});
