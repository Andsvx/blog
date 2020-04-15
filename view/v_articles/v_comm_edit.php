<div>
	<? if($error) :?>
		<p class="error">Заполните все поля!</p>
	<? endif ?>
	<form method="post">
		<p>Комментарий:</p>
		<textarea name="text" class="comment_textarea"><?=$text?></textarea><br>
		<button name="update">Изменить</button>
		<button name="delete">Удалить</button>
	</form>
</div>