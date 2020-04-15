<?php

abstract class c_base extends c_main
{
	protected $user;

	function __construct()
	{
		$this->user = m_users::instance()->get();
	}

	protected $title;
	protected $content;
	protected $hide_new;
	protected $user_name;
	protected $hide_admin;

	protected function before()
	{
		$this->title = 'Blog';
		$this->content = '';
		$this->hide_new = $this->user == null ? true : false;
		$this->user_name = $this->user == null ? '---' : $this->user['login'];
		$this->hide_admin = ($this->user == null) || ($this->user['access_level'] < 3) ? true : false;
	}

	public function render()
	{
		$vars = array('user_name' => $this->user_name, 'title' => $this->title, 'content' => $this->content, 'hide_new' => $this->hide_new, 'hide_admin' => $this->hide_admin);
		$page = $this->template('view/v_template.php', $vars);
		echo $page;
	}

	public function error_403()
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
		$this->content = $this->template('view/v_403.php');
	}

	public function error_404()
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		$this->content = $this->template('view/v_404.php');
	}

	public function __call($name, $params)
	{
		$this->error_404();
	}
}

?>