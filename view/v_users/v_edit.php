<div>
	<? if($error) :?>
		<div class="error">Заполните все поля!</div>
	<? endif ?>
	<form method="post">
		<div class="edit_user">
			<div>
				<div>Login:</div>
				<div><input type="text" name="login" value="<?=$login?>"></div>
			</div>
			<div>
				<span>Уровень доступа:</span>
				<span>
					<select name="access_level">
						<? for ($i = 1; $i <= 3; $i++): ?>
							<? if ($i == $access_level): ?>
								<option selected value="<?=$i?>"><?=$i?></option>
							<? else: ?>
								<option value="<?=$i?>"><?=$i?></option>
							<? endif; ?>
						<? endfor; ?>
					</select>
				</span>
			</div>
		</div>
		<div>
			<button name="update">Изменить</button>
			<button name="delete">Удалить</button>
		</div>
	</form>
	<div>
		<p>Пользователь с уровнем доступа 1 может: добавлять статьи и комментарии, редактировать свои статьи и свои комментарии.</p>
		<p>Пользователь с уровнем доступа 2 может: добавлять статьи и комментарии, редактировать все статьи и свои комментарии.</p>
		<p>Пользователь с уровнем доступа 3 может: добавлять статьи и комментарии, редактировать все статьи и все комментарии, редактировать учетные записи пользователей.</p>
	</div>
</div>