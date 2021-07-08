<?php

class m_users
{
	private static $instance;

	public static function instance()
	{
		if (self::$instance == null)
		{
			self::$instance = new m_users();
		}
		return self::$instance;
	}

	private $m_mysql; // Экземпляр класса m_mysql
	private $sid; // Идентификатор текущей сессии
	private $uid; // Идентификатор текущего пользователя

	function __construct()
	{
		$this->m_mysql = m_mysql::instance();
		$this->sid = null;
		$this->uid = null;
	}

	//
	// Очистка неиспользуемых сессий
	//

	public function clear_sessions()
	{
		$min = date('Y-m-d H:i:s', time() - 60 * 10);
		$v = "time_last < '%s'";
		$where = sprintf($v, $min);
		$this->m_mysql->delete('sessions', $where);
	}

	//
	// Регистрация
	//

	public function registration($login, $password, $password_confirm)
	{
		$arr = array();
		$arr['login'] = trim($login);
		$arr['password'] = md5($password);

		if ($password != $password_confirm)
		{
			return false;
		}
		elseif (!empty($login) && !empty($password))
		{
			$this->m_mysql->insert('users', $arr);
			return true;
		}
		else
		{
			return false;
		}
	}

	//
	// Авторизация
	//

	public function login($login, $password, $remember = true)
	{
		// Вытаскиваем пользователя из базы данных
		$user = $this->get_by_login($login);

		if ($user == null)
		{
			return false;
		}

		$id_user = $user['id_user'];

		// Проверяем пароль
		if ($user['password'] != md5($password))
		{
			return false;
		}

		// Запоминаем login и md5(password)
		if ($remember)
		{
			$expire = time() + 3600 * 24 * 7;
			setcookie('login', $login, $expire, '/');
			setcookie('password', md5($password), $expire, '/');
		}

		// Открываем сессию и запоминаем SID
		$this->sid = $this->open_session($id_user);

		return true;
	}

	//
	// Выход
	//

	public function logout()
	{
		setcookie('login', '', time() - 1, '/');
		setcookie('password', '', time() - 1, '/');
		unset($_COOKIE['login']);
		unset($_COOKIE['password']);
		unset($_SESSION['sid']);
		$this->sid = null;
		$this->uid = null;
	}

	//
	// Получение пользователя
	// $id_user - если не указан, брать текущего
	// результат - объект пользователя
	//

	public function get($id_user = null)
	{
		// Если id_user не указан, берем его по текущей сессии
		if ($id_user == null)
		{
			$id_user = $this->get_uid();
		}

		if ($id_user == null)
		{
			return null;
		}

		// А теперь просто возвращаем пользователя по id_user
		$v = "SELECT * FROM users WHERE id_user = '%d'";
		$query = sprintf($v, $id_user);
		$result = $this->m_mysql->select($query);

		if (isset($result[0]))
		{
			return $result[0];
		}
	}

	//
	// Получение пользователя
	//

	public function get_by_login($login)
	{
		$v = "SELECT * FROM users WHERE login = '%s'";
		$query = sprintf($v, $this->m_mysql->str_protect($login));
		$result = $this->m_mysql->select($query);

		if (isset($result[0]))
		{
			return $result[0];
		}
	}

	//
	// Проверка активности пользователя
	// $id_user - идентификатор
	// результат - true если online
	//

	public function is_online($id_user)
	{
		// code...
	}

	//
	// Получение id текущего пользователя
	// результат - UID
	//

	public function get_uid()
	{
		// Проверка кэша
		if ($this->uid != null)
		{
			return $this->uid;
		}

		// Берем по текущей сессии
		$sid = $this->get_sid();

		if ($sid == null)
		{
			return null;
		}

		$v = "SELECT id_user FROM sessions WHERE sid = '%s'";
		$query = sprintf($v, $this->m_mysql->str_protect($sid));
		$result = $this->m_mysql->select($query);

		// Если сессию не нашли - значит пользователь не авторизован
		if (count($result) == 0)
		{
			return null;
		}

		// Если нашли - запомним ее
		$this->uid = $result[0]['id_user'];
		return $this->uid;
	}

	//
	// Функция возвращает идентификатор текущей сессии (SID)
	//

	private function get_sid()
	{
		// Проверка кэша
		if ($this->sid != null)
		{
			return $this->sid;
		}

		// Ищем SID в сессии
		$sid = isset($_SESSION['sid']) ? $_SESSION['sid'] : null;

		// Если нашли, попробуем обновить time_last в базе данных, заодно и проверим, есть ли там сессия
		if ($sid != null)
		{
			$session = array();
			$session['time_last'] = date('Y-m-d H:i:s');
			$v = "sid = '%s'";
			$where = sprintf($v, $this->m_mysql->str_protect($sid));
			$affected_rows = $this->m_mysql->update('sessions', $session, $where);

			if ($affected_rows == 0)
			{
				$v = "SELECT count(*) FROM sessions WHERE sid = '%s' ";
				$query = sprintf($v, $this->m_mysql->str_protect($sid));
				$result = $this->m_mysql->select($query);

				if ($result[0]['count(*)'] == 0)
				{
					$sid = null;
				}
			}
		}

		// Если не нашли, ищем login и md5(password) в cookie
		if ($sid == null && isset($_COOKIE['login']))
		{
			$user = $this->get_by_login($_COOKIE['login']);

			if ($user != null && $user['password'] == $_COOKIE['password'])
			{
				$sid = $this->open_session($user['id_user']);
			}
		}

		// Запоминаем в кэш
		if ($sid != null)
		{
			$this->sid = $sid;
		}

		// Возвращаем SID
		return $sid;
	}

	//
	// Открытие новой сессии
	// результат - SID
	//

	private function open_session($id_user)
	{
		// Генерируем SID
		$sid = $this->generate_str(10);

		// Вставляем SID в базу данных
		$now = date('Y-m-d H:i:s');
		$session = array();
		$session['id_user'] = $id_user;
		$session['sid'] = $sid;
		$session['time_start'] = $now;
		$session['time_last'] = $now;
		$this->m_mysql->insert('sessions', $session);

		// Регистрируем сессию в PHP сессии
		$_SESSION['sid'] = $sid;

		// Возвращаем SID
		return $sid;
	}

	//
	// Генерация случайной последовательности
	//

	private function generate_str($length = 10)
	{
		$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		$code = "";
		$clen = strlen($chars) - 1;

		while (strlen($code) < $length)
		{
			$code .= $chars[mt_rand(0, $clen)];
		}

		return $code;
	}

	//
	// Список пользователей
	//

	public function get_all($page, $quantity)
	{
		$skip = $page > 0 ? ($page - 1) * $quantity : 0;

		$query = "SELECT * FROM users ORDER BY id_user ASC LIMIT $skip, $quantity";
		$result = $this->m_mysql->select($query);

		return $result;
	}

	//
	// Изменение учетной записи
	//

	public function edit($id_user, $login, $access_level)
	{
		// Подготовка
		$where = "id_user = '$id_user'";
		$object = array('login' => trim($login), 'access_level' => (int)$access_level);

		// Проверка
		if (in_array('', $object))
		{
			return false;
		}

		// Запрос
		$this->m_mysql->update('users', $object, $where);
		return true;
	}

	//
	// Удаление учетной записи
	//

	public function delete($id_user)
	{
		$where = "id_user = '$id_user'";
		$this->m_mysql->delete('users', $where);
		return true;
	}

	//
	// Количество пользователей
	//

	public function count_all()
	{
		return $this->m_mysql->count_all('users');
	}
}

?>