<div>
	<? foreach ($users as $user): ?>
		<div class="user">
			<div class="user_edit"><a href="users/edit/<?=$user['id_user']?>"><img src="view/img/edit_1.png"></a></div>
			<div class="user_login"><?=$user['login']?></div>
		</div>
	<? endforeach; ?>
</div>

<div class="pagination">
	<? for ($i = 1; $i <= $pages; $i++): ?>
		<? if ($i == $page): ?>
			<span class="current">
				<a href="users/list/<?=$i?>"><?=$i?></a>
			</span>
		<? else: ?>
			<span class="ordinary">
				<a href="users/list/<?=$i?>"><?=$i?></a>
			</span>
		<? endif; ?>
	<? endfor; ?>
</div>