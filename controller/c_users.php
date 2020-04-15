<?php

class c_users extends c_base
{
	private $m_users;

	protected function before()
	{
		parent::before();
		$this->m_users = m_users::instance();
	}

	public function action_registration()
	{
		$this->title .= '::Регистрация';
		$this->hide_new = true;
		$this->user_name = '---';
		$this->hide_admin = true;

		$this->m_users->logout();

		if($this->IsPost())
		{
			if($this->m_users->registration($_POST['login'], $_POST['password'], $_POST['password_confirm']))
			{
				header("Location: /");
				exit();
			}
		}

		$this->content = $this->template('view/v_users/v_registration.php');
	}

	public function action_login()
	{
		$this->title .= '::Авторизация';
		$this->hide_new = true;
		$this->user_name = '---';
		$this->hide_admin = true;

		$this->m_users->logout();

		if($this->IsPost())
		{
			if($this->m_users->login($_POST['login'], $_POST['password'], isset($_POST['remember'])))
			{
				header("Location: /");
				exit();
			}
		}

		$this->content = $this->template('view/v_users/v_login.php');
	}

	public function action_list()
	{
		$this->title .= '::Admin Panel';

		if ($this->user == null || $this->user['access_level'] < 3)
		{
			$this->error_403();
			return;
		}

		$quantity = 10;
		$page = isset($this->params[2]) ? (int)$this->params[2] : 1;
		$pages = ceil($this->m_users->count_all('users') / $quantity);
		$users = $this->m_users->get_all($page, $quantity);

		$this->content = $this->template('view/v_users/v_admin.php', array('users' => $users, 'pages' => $pages, 'page' => $page));
	}

	public function action_edit()
	{
		$this->title .= '::Редактирование учетной записи';

		if ($this->user == null || $this->user['access_level'] < 3)
		{
			$this->error_403();
			return;
		}

		$id_user = isset($this->params[2]) ? (int)$this->params[2] : null;

		if (isset($_POST['update']))
		{
			if ($this->m_users->edit($id_user, $_POST['login'], $_POST['access_level']))
			{
				header("Location: /users/list");
				exit();
			}
			else
			{
				$login = $_POST['login'];
				$access_level = $_POST['access_level'];
				$error = true;
			}
		}
		elseif (isset($_POST['delete']))
		{
			$this->m_users->delete($id_user);
			header("Location: /users/list");
			exit();
		}
		else
		{
			$user = $this->m_users->get($id_user);
			if ($user)
			{
				$login = $user['login'];
				$access_level = $user['access_level'];
				$error = false;
			}
			else
			{
				$this->error_404();
				return;
			}
		}

		$this->content = $this->template('view/v_users/v_edit.php', array('login' => $login, 'access_level' => $access_level, 'error' => $error));
	}
}

?>