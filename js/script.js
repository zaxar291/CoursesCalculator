var timer;
var clicked = false;
var sessid;
$(document).ready(function () {

    if($("#SESSID").val() !== undefined)
    {
        sessid = $("#SESSID").val();
    }else{
        sessid = "#error"
    }

    $(".openable").click(function () {
        var item_id = $(this).get()[0]["id"];
        $("#" + item_id + "_list").slideToggle("slow");
    });
    Call("year");
    $("#year").change(function () {
        if($("#classe").val() !== null && $("#classe").val() !== "non_selected" && $("#course").val() !== null && $("#course").val() !== "non_selected")
        {
            GetResultAboutCourses()
        }else{
            Call("classe");
        }
    });
    $("#classe").change(function () {
        Call("course", $("#classe").val());
    });

    $("#course").change(function () {
        GetResultAboutCourses();
    });

    $("#confirm").click(function () {
        if(clicked == true)
        {
            return;
        }
        var ngModel = {
            "name" : "",
            "email" : "",
            "message" : ""
        };
        if($("#feedback_name").val() !== "")
        {
            ngModel.name = JSON.stringify($("#feedback_name").val());
            $("#feedback_name_errors").text("");
        }else{
            $("#feedback_name_errors").text("");
            $("#feedback_name_errors").text("Вы пропустили это поле!");
            return;
        }
        if($("#feedback_email").val() !== "")
        {
            ngModel.email = JSON.stringify($("#feedback_email").val());
            $("#feedback_email_errors").text("");
        }else{
            $("#feedback_email_errors").text("");
            $("#feedback_email_errors").text("Вы пропустили это поле!");
            return;
        }
        if($("#feedback_message").val() !== "")
        {
            ngModel.message = JSON.stringify($("#feedback_message").val());
            $("#feedback_message_errors").text("");
        }else{
            $("#feedback_message_errors").text("");
            $("#feedback_message_errors").text("Вы пропустили это поле!");
            return;
        }
        clicked = true;
        $.ajax({
            url: "calculator/",
            data: "support_request=support&content="+JSON.stringify(ngModel),
            success: function (data)
            {
                var msg = JSON.parse(data);
                $("#messages_success").append(msg["response_content"]);
                $("#messages_success").css("color", msg["response_color"]);
            }
        });
    })

    $("a[href^=\"#\"]").click(function () {
        window.open($(this).attr('href').replace(/#/, ""));
        return false;
    });
    $("#footer").on('mouseover', function () {
        $("#footer").animate({backgroundColor: 'red'}, 1000);
    });
});

function Call(query, meta) {
    $("#loader").css("display", "");
    $("#outputresult").text("");
    if (meta == undefined) {
        var data = "action_type=" + query + "&sess="+sessid;
    } else {
        var data = "action_type=" + query + "&meta=" + meta + "&year=" + $("#year").val() + "&sess="+sessid
    }
    $.ajax({
        url: "calculator/",
        data: data,
        success: function (answer) {
            var msg = JSON.parse(answer);
            $("#errors").text("");
            var result = '';
            if(msg["message_type"] === 'error')
            {
                $("#errors").text("");
                $("#errors").append(msg["message_content"]);
                $("#loader").css("display", "none");
                var parametrs = msg["parammetrs"];
                if(parametrs["showYears"])
                {
                    if(document.getElementById('years').style.display === 'none')
                    {
                        $("#years").slideToggle("fast");
                    }
                }else{
                    if(document.getElementById('years').style.display !== 'none')
                    {
                        $("#years").slideToggle("fast");
                    }
                }
                if(parametrs["showClasses"])
                {
                    if(document.getElementById('classes').style.display === 'none')
                    {
                        $("#classes").slideToggle("fast");
                    }
                }else{
                    if(document.getElementById('classes').style.display !== 'none')
                    {
                        $("#classes").slideToggle("fast");
                    }
                }
                if(parametrs["showCourses"])
                {
                    if(document.getElementById('courses').style.display === 'none')
                    {
                        $("#courses").slideToggle("fast");
                    }
                }else{
                    if(document.getElementById('courses').style.display !== 'none')
                    {
                        $("#courses").slideToggle("fast");
                    }
                }
                if(parametrs["showResult"])
                {
                    if(document.getElementById('outputresult').style.display === 'none')
                    {
                        $("#outputresult").slideToggle("fast");
                    }
                }else{
                    if(document.getElementById('outputresult').style.display !== 'none')
                    {
                        $("#outputresult").slideToggle("fast");
                    }
                }
                return;
            }
            if(msg["message_type"] === "success")
            {
                var parametrs = msg["parammetrs"];
                if(msg["object_type"] === "years")
                {
                    result += '<option value="non_selected" selected>'+msg["on:null"]+'</option>';
                    for(var i in msg)
                    {
                        if(typeof(msg[i]) === 'object')
                        {
                            for(var r in msg[i])
                            {
                                var parametr = msg[i][r];
                                if(parametr["id"] !== undefined && parametr["content"] !== undefined)
                                {
                                    result += '<option value="'+parametr["id"]+'">'+parametr["content"]+'</option>';
                                }
                            }
                        }
                    }
                }
                if(msg["object_type"] === "classes")
                {
                    result += '<option value="non_selected" selected>'+msg["on:null"]+'</option>';
                    for(var i in msg)
                    {
                        if(typeof(msg[i]) === 'object')
                        {
                            for(var r in msg[i])
                            {
                                var parametr = msg[i][r];
                                if(parametr["id"] !== undefined && parametr["content"] !== undefined)
                                {
                                    result += '<option value="'+parametr["id"]+'">'+parametr["content"]+'</option>';
                                }
                            }
                        }
                    }
                }
                if(msg["object_type"] === "courses")
                {
                    result += '<option value="non_selected" selected>'+msg["on:null"]+'</option>';
                    var labels = GetLabels(msg);
                    for (var i in labels)
                    {
                        result += CreateCourseListFromArray(msg, labels[i][0]);
                    }
                }
                if(parametrs["showYears"])
                {
                    if(document.getElementById('years').style.display === 'none')
                    {
                        $("#years").slideToggle("fast");
                    }
                }else{
                    if(document.getElementById('years').style.display !== 'none')
                    {
                        $("#years").slideToggle("fast");
                    }
                }
                if(parametrs["showClasses"])
                {
                    if(document.getElementById('classes').style.display === 'none')
                    {
                        $("#classes").slideToggle("fast");
                    }
                }else{
                    if(document.getElementById('classes').style.display !== 'none')
                    {
                        $("#classes").slideToggle("fast");
                    }
                }
                if(parametrs["showCourses"])
                {
                    if(document.getElementById('courses').style.display === 'none')
                    {
                        $("#courses").slideToggle("fast");
                    }
                }else{
                    if(document.getElementById('courses').style.display !== 'none')
                    {
                        $("#courses").slideToggle("fast");
                    }
                }
                if(parametrs["showResult"])
                {
                    if(document.getElementById('outputresult').style.display === 'none')
                    {
                        $("#outputresult").slideToggle("fast");
                    }
                }else{
                    if(document.getElementById('outputresult').style.display !== 'none')
                    {
                        $("#outputresult").slideToggle("fast");
                    }
                }
            }
            $("#" + query).empty();
            $("#" + query).append(result);
            if (document.getElementById(query + "s").style.display == 'none') {
                $("#" + query + "s").slideToggle("medium");
            }
            $("#loader").css("display", "none");

        },
        error: function (msg) {
            $("#outputresult").append("Ошибка связи с сервером, попробуйте перезагрузить страницу и попробовать снова, если проблема не исчезнет обратитесь к админисстратору сайта." + msg);
            $("#serveranswers").val(0);
            $("#loader").css("display", "none");
        }
    });
}

function GetLabels(array)
{
    var output = [];
    for (var i in array)
    {
        if(typeof(array[i]) == 'object')
        {
            output[array[i]["label"]] = [array[i]["label"]];
        }
    }
    return output;
}

function CreateCourseListFromArray(array, label)
{
    var output;
    if(label !== undefined)
    {
        output = '<optgroup label="'+label+'">';
    }
    for (var i in array)
    {
        if(typeof (array[i]) == 'object')
        {
            for (var r in array[i])
            {
                for (var c in array[i][r])
                {
                    var course = array[i][r][c];
                    if(typeof(course) == 'object' && course["label"] == label)
                    {
                        output += '<option value="'+course["course_id"]+'">'+course["course_content"]+'</option>';
                    }
                }
            }
        }
    }
    return output+'</optgroup>';
}

function GetResultAboutCourses() {
    $("#loader").css("display", "");
    $.ajax({
        url: "calculator/",
        data: "action_type=countcourses&course=" + encodeURIComponent($("#course").val()) + "&year=" + $("#year").val() + "&sess="+sessid,
        success: function (msg) {
            $("#outputresult").text("");
            $("#outputresult").append(JSON.parse(msg));
            if(document.getElementById('outputresult').style.display === 'none')
            {
                $("#outputresult").slideToggle("fast");
            }
            $("#loader").css("display", "none");
        },
        error: function () {
            $("#outputresult").append("Ошибка связи с сервером, повторите попытку позже");
            $("#loader").css("display", "none");
        }
    });
}

function ShowModal(id)
{
    $("#"+id).modal();
}
