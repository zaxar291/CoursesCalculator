<div class="auth-ob">
    <div class="auth--content">
        <p>Логин</p>
        <input type="text" placeholder="Логин" class="form-control" id="login">
        <p>Пароль</p>
        <input type="password" placeholder="Пароль" class="form-control" id="pass">
        <div id="enter" class="modalbtn">Авторизация</div>
        <p class="server-messages">

        </p>
    </div>
</div>
<div class="steps-ob">
    <div class="steps--content">
        <div class="steps" id="step-1" style="display: none">
            <?php $settings = get_database_file()->parametrs; ?>
            <p>Хост БД</p>
            <input type="text" class="form-control" id="dbHost" value="<?php echo $settings->db_host ?>">
            <p id="dbHostErrors" style="color: red; font-size: 12px"></p>
            <p>Логин БД</p>
            <input type="text" class="form-control" id="dbLogin" value="<?php echo $settings->db_login ?>">
            <p id="dbLoginErrors" style="color: red; font-size: 12px"></p>
            <p>Пароль БД</p>
            <input type="text" class="form-control" id="dbPass" value="<?php echo $settings->db_pass?>">
            <p id="dbPassErrors" style="color: red; font-size: 12px"></p>
            <p>Имя БД</p>
            <input type="text" class="form-control" id="dbName" value="<?php echo $settings->db_name?>">
            <p id="dbNameErrors" style="color: red; font-size: 12px"></p>

            <div class="modalbtn step-switch" id="1" data-slide="1">Проверить и продолжить</div>
            <p id="status-bd" style="color: red; font-size: 12px"></p>
        </div>
        <div class="steps" id="step-2" style="display: none">
            <p>Восстановить бд из локальных файлов</p>
            <button class="modalbtn" id="repeat--local"><span id="local--db--repeat-button_text">Восстановить</span><span id="btn---loader-local" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
            <p>Загрузить файл бд</p>
            <input type="file" id="db--back-file">
            <button class="modalbtn" id="repeat--file"><span id="file--db--repeat-button_text">Восстановить</span><span id="btn---loader-file" style="text-align: center; display: none;"><img style="height: 30px" src="includes/images/loader_admin.gif"></span></button>
            <p id="local-repeat-msg" style="color: red; font-size: 12px"></p>
        </div>
    </div>
</div>
<link href="css/auth.styles.css" rel="stylesheet">
<script src="js/auth.scripts.js"></script>