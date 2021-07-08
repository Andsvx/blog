<?php

abstract class c_main
{
	protected $params;
	protected abstract function before();
	protected abstract function render();

	public function request($action, $params)
	{
		$this->params = $params;
		$this->before();
		$this->$action();
		$this->render();
	}

	protected function IsGet()
	{
		return $_SERVER['REQUEST_METHOD'] == 'GET';
	}

	protected function IsPost()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	protected function template($file_name, $vars = array())
	{
		// Установка переменных для шаблона
		foreach ($vars as $k => $v)
		{
			$$k = $v;
		}

		// Генерация HTML в строку
		ob_start();
		include "$file_name";
		return ob_get_clean();
	}
}

?>