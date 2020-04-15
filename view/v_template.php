<!-- Основной шаблон -->

<!DOCTYPE html>
<html>
<head>
	<base href="/">
	<title></title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="view/css/style.css">
</head>
<body>
	<div id="header">
		<h2><?=$title?></h2>
	</div>

	<div id="menu">
		<? if(!$hide_admin): ?>
			<span><a href="users/list">Admin Panel</a> | </span>
		<? endif; ?>
		<span><a href="">Главная</a> | </span>
		<? if(!$hide_new): ?>
			<span><a href="articles/new">Добавить новую статью</a> | </span>
		<? endif; ?>
		<span><a href="users/login">Вход&Выход</a></span>
		<span class="nickname"><?=$user_name?></span>
	</div>

	<div id="content"><?=$content?></div>

	<div id="footer">Все права защищены</div>
</body>
</html>