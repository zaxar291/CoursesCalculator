<div id="top_menu">
    <!-- Классы navbar и navbar-default (базовые классы меню) -->
    <nav class="navbar navbar-default">
        <!-- Контейнер (определяет ширину Navbar) -->
        <div class="container-fluid">
            <!-- Заголовок -->
            <div class="navbar-header" style="display:flex;align-items: center;justify-content: center;">
                <!-- Кнопка «Гамбургер» отображается только в мобильном виде (предназначена для открытия основного содержимого Navbar) -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-main">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!-- Бренд или название сайта (отображается в левой части меню) -->
                <span id="logo" style="padding-right: 10px;">
                    <a class="navbar-brand" href="http://kit.kh.ua">Учебный центр "Кит"</a>
                </span>
                <!--<span style="padding: 10px;background: red;color:white;height: 100%">BETA</span>-->
            </div>
            <!-- Основная часть меню (может содержать ссылки, формы и другие элементы) -->
            <div class="collapse navbar-collapse" id="navbar-main">
                <ul class="nav navbar-nav" style="text-align: center">
                    <li class="active"><a href="calculator<?php if($_SESSION['user_token']){echo '?token='.$_SESSION['user_token'];} ?>">Калькулятор<span class="sr-only">(current)</span></a></li>
                    <li><a href="#http://kit.kh.ua">Главная</a></li>
                    <li><a href="#http://kit.kh.ua/about.html">О центре</a></li>
                    <li><a href="#http://kit.kh.ua/reception.html">Условия приёма</a></li>
                    <li><a href="#http://kit.kh.ua/main.html">Виды обучения</a></li>
                    <?php if(isset($_SESSION['user_token'])){echo '<li><a href="cabinet?token='.$_SESSION['user_token'].'">Панель управления</a></li>';} ?>
                </ul>
            </div>
        </div>
    </nav>
</div>