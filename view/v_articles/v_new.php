<div>
	<? if($error) :?>
		<p class="error">Заполните все поля!</p>
	<? endif ?>
	<form method="post">
		<p>Название:</p>
		<input type="text" name="title" value="<?=$title?>">
		<p>Содержание:</p>
		<textarea name="content" class="article_textarea"><?=$content?></textarea><br>
		<button>Добавить</button>
	</form>
</div>