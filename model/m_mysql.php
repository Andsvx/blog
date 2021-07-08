<?php

class m_mysql
{
	private static $instance;

	public static function instance()
	{
		if (self::$instance == null)
		{
			self::$instance = new m_mysql();
		}
		return self::$instance;
	}

	public $mysqli;

	function __construct()
	{
		$this->mysqli = new mysqli(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
		$this->mysqli->set_charset('utf8');
	}

	//
	// Выборка строк
	// $query - полный текст SQL запроса
	// результат - массив выбранных объектов
	//

	public function select($query)
	{
		$result = $this->mysqli->query($query);

		if (!$result)
		{
			die($this->mysqli->error);
		}

		$r = mysqli_num_rows($result);
		$arr = array();

		for($i = 0; $i < $r; $i++)
		{
			$row = mysqli_fetch_assoc($result);
			$arr[] = $row;
		}

		return $arr;
	}

	//
	// Вставка строки
	// $table - название таблицы
	// $object - ассоциативный массив с парами вида "название столбца - значение"
	// результат - идентификатор новой строки
	//

	public function insert($table, $object)
	{
		$columns = array();
		$values = array();

		foreach ($object as $key => $value)
		{
			$key = $this->str_protect($key . '');
			$columns[] = $key;

			if ($value === null)
			{
				$values[] = 'NULL';
			}
			else
			{
				$value = $this->str_protect($value . '');
				$values[] = "'$value'";
			}
		}

		$columns_s = implode(',', $columns);
		$values_s = implode(',', $values);

		$query = "INSERT INTO $table ($columns_s) VALUES ($values_s)";
		$result = $this->mysqli->query($query);

		if (!$result)
		{
			die($this->mysqli->error);
		}

		return $this->mysqli->insert_id;
	}

	//
	// Изменение строк
	// $table - название таблицы
	// $object - ассоциативный массив с парами вида "название столбца - значение"
	// $where - условие (часть SQL запроса)
	// результат - число измененных строк
	//

	public function update($table, $object, $where)
	{
		$sets = array();

		foreach ($object as $key => $value)
		{
			$key = $this->str_protect($key . '');

			if ($value === null)
			{
				$sets[] = "$value=NULL";
			}
			else
			{
				$value = $this->str_protect($value . '');
				$sets[] = "$key='$value'";
			}
		}

		$sets_s = implode(',', $sets);
		$query = "UPDATE $table SET $sets_s WHERE $where";
		$result = $this->mysqli->query($query);

		if (!$result)
		{
			die($this->mysqli->error);
		}

		return $this->mysqli->affected_rows;
	}

	//
	// Удаление строк
	// $table - название таблицы
	// $where - условие (часть SQL запроса)
	// результат - число удаленных строк
	//

	public function delete($table, $where)
	{
		$query = "DELETE FROM $table WHERE $where";
		$result = $this->mysqli->query($query);

		if (!$result)
		{
			die($this->mysqli->error);
		}

		return $this->mysqli->affected_rows;
	}

	//
	// Подсчет количества записей в таблице
	// $table - название таблицы
	// результат - количество записей
	//

	public function count_all($table)
	{
		$query = "SELECT COUNT(*) FROM $table";	
		$result = $this->mysqli->query($query);

		if (!$result)
		{
			die($this->mysqli->error);
		}

		$count = mysqli_fetch_row($result);
		return $count[0];
	}

	//
	// Защита от SQL-инъекций и XSS-атак
	//

	public function str_protect($str)
	{
		return htmlspecialchars(addslashes($str));
	}
}

?>