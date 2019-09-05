<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html class=""><!--<![endif]-->
<head>
<?php if(!function_exist("is_availible")){header("Location: 404");} ?>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<title>404 - Учебный центр компьютерных технологий "Кит"</title>
	
	<!-- Library - Bootstrap v3.3.5 -->
    <link rel="stylesheet" type="text/css" href="<?php get_parent_dir() ?>css/lib.css">
	
	<!-- Custom - Theme CSS -->
	<link rel="stylesheet" type="text/css" href="<?php get_parent_dir() ?>css/style.css">
	<link rel="stylesheet" type="text/css" href="<?php get_parent_dir() ?>css/shortcode.css">
	<!--[if lt IE 9]>		
		Вы используете слишком старый браузер, скачайте/обновите браузер до последней версии.
    <![endif]-->
	
</head>

<body data-offset="200" data-spy="scroll" data-target=".ow-navigation">
	
	<div class="container-fulid no-padding error-page">
		<div class="section-padding"></div>
			<div class="container">
				<div class="row">
					<div class="col-md-6 col-sm-6 error-msg">
						<h3>404</h3>
						<p><span>Извините,</span>доступ к этому файлу запрещён</p>
					</div>
					<div class="col-md-6 col-sm-6">
						<div class="error-content">
							<div class="input-group">
							</div>
							<p>К сожалению, мы не можем вам предоставить доступ к этому файлу, но вы можете вернуться на: </p>
							<a onclick="window.history.back()" title="Previous Page">Предыдущую страницу</a>
							<a href="/" title="Back To Home">Домашнюю страницу</a>				
						</div>
					</div>
				</div>
			</div>
		<div class="section-padding"></div>
	</div>
</body>
</html>