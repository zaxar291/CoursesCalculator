<?php if(!function_exists("get_header")){header("Location: 404");}

if(isset($_GET))
{
    if(isset($_GET["action_type"]))
    {
        $Calc = new calculator_controller();
        echo json_encode($Calc->make_result());
        return;
    }
    if(isset($_GET["support_request"]))
    {
        get_template_part("/services/feedback.services.php");
        $feedbacks = new feedbacks();
        echo json_encode($feedbacks->AddNewResponse(json_decode($_GET["content"])));
        return;
    }
}

get_header(); ?>
<body>
<?php get_nagivation_menu(); ?>
<?php
get_template_part("/services/visit.state.service.php");
$AnaliseUser = new VisitState($_SERVER["REMOTE_ADDR"], $_SERVER);
    $AnaliseUser->AnaliseInputIp();
    $AnaliseUser->CreateServerLog();
?>
<div id="content">
<form>
    <div id="years" class="option-text"  style="display:none;">
        <p><span class="dinamyc_text">Шаг 1: выберите период за который вы хотите оплатить</span><br><br>
            <select id="year">
            </select>
        </p>
    </div>
    <div id="classes" class="option-text" style="display:none;">
        <p class="option-p-text">Шаг 2: выберите класс в котором учится ваш ребёнок<br><br>
            <select id="classe">

            </select>
        <br>
        </p>
    </div>
    <div id="courses" class="option-text" style="display:none;">
        <p class="option-p-text">Шаг 3: выберите курс на котором обучается ваш ребёнок<br><br>
            <select id="course">

            </select></p>
    </div>

    <p class="option-p-text" id="errors"></p>
    <div id="outputresult"></div>
	<div id="loader" style="text-align:center;display:none"><img src="includes/images/loader_c.svg"></div>
    <input type="hidden" value="" id="serveranswers">
</form>
    <?php echo '<input type="hidden" id="SESSID" value="'.get_sess_id().'">'; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</div>
<?php get_template_part("/includes/feedBackModal.php"); ?>
<?php get_footer(); ?>