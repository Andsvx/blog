<br><hr><br>

<div>
	<div>Комментарии (<?=count($comments);?>) </div>
	<? foreach ($comments as $comment): ?>
		<div class="comment">
			<div class="comment_header">
				<div class="comment_edit"><a href="articles/comm_edit/<?=$comment['id_comment']?>"><img src="view/img/edit_2.png"></a></div>
				<div class="comment_nickname"><?=$author = isset($comment['login']) ? $comment['login'] : '---';?></div>
			</div>
			<div class="comment_body">
				<div class="comment_text"><?=nl2br($comment['text'])?></div>
			</div>
		</div>
	<? endforeach; ?>
</div>

<br><hr><br>

<? if($hide_form): ?>
	<div>Авторизуйтесь чтобы оставлять комментарии...</div>
<? else: ?>
	<div>
		<? if($error): ?>
			<p class="error">Заполните все поля!</p>
		<? endif; ?>
		<form method="post">
			<p>Здесь вы можете добавить свой комментарий:</p>
			<textarea name="text" class="comment_textarea"></textarea><br>
			<button>Добавить</button>
		</form>
	</div>
<? endif; ?>