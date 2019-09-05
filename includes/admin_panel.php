<div id="mobile_navigation_place">
    <div id="mobile_content">
        <div id="navigation_menu_title" class="adm-mobile-menu"><p>Элементы упраления панелью</p></div>
        <div id="menu-button--open" class="arrow down adm-mobile-menu"></div>
        <div id="menu-button--close" class="arrow up adm-mobile-menu"></div>
    </div>
</div>
<div id="admin_panel_menu">
    <ul id="main_admin_menu">
        <li class="menu element_admin_panel active" id="courses_list" data-action="courses_list">Управление курсами</li>
        <li class="menu element_admin_panel" id="total_periods" data-action="total_periods">Периоды обучения</li>
        <li class="menu element_admin_panel" id="tables_control" data-action="tables_control">Управление таблицами</li>
        <li class="menu element_admin_panel" id="metatdata_control" data-action="metatdata_control">Управление метаданными</li>
        <?php if($_SESSION["role"] == "administrator") { ?>
        <li class="menu element_admin_panel" id="user_control" data-action="user_control">Управление пользователями</li>
        <?php } ?>
        <li class="menu element_admin_panel" id="db_control" data-action="db_control">Управление подключениями</li>
        <?php
        if(isset($_GET["debug"]))
        {
            if($_GET["debug"] == true)
            {
                echo '<li class="menu element_admin_panel" data-action="rollbackProfile">Вернутся в профиль</li>';
            }
        }else{
            echo '<li class="menu element_admin_panel" data-action="logout">Выйти из профиля</li>';
        }
        ?>
    </ul>
</div>
<div id="admin_panel_content">
    <div id="loader_body">
            <div id="loader_text">
                Загрузка...
            </div>
            <div id="loader">
                <img src="includes/images/loader.gif" class="loader_img">
            </div>
    </div>
    <div id="courses_list_place"  class="admin_content"></div>
    <div id="total_periods_place" style="display:none" class="admin_content"></div>
    <div id="tables_control_place" style="display:none" class="admin_content"></div>
    <div id="metatdata_control_place" style="display:none" class="admin_content"></div>
    <div id="user_control_place" style="display:none" class="admin_content"></div>
    <div id="db_control_place" style="display:none" class="admin_content"></div>
</div>
<?php if(!isset($_SESSION["user_token"]) || $_SESSION["user_token"] == '')
{
    unset($_SESSION);
    header("../403");
}
include(get_modal_place_for_admin_panel());
?>
<input type="hidden" id="user_token" value="<?php echo $_SESSION["user_token"]; ?>">
<script src="js/services/custom.modal.service.js"></script>
<script src="js/admin_panel_scripts.dex.js"></script>
<link href="css/admin_panel_styles.css" rel="stylesheet">
<link rel="stylesheet" href="css/editor.css">
<link rel="stylesheet" href="css/animations.css">
