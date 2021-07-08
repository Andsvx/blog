<?php

class m_comments
{
	private static $instance;

	public static function instance()
	{
		if (self::$instance == null)
		{
			self::$instance = new m_comments();
		}
		return self::$instance;
	}

	private $m_mysql;

	function __construct()
	{
		$this->m_mysql = m_mysql::instance();
	}

	public function get_all($id_article)
	{
		$id_article = (int)$id_article;

		$query = "SELECT comments.*, users.login FROM comments LEFT JOIN users ON comments.id_user = users.id_user WHERE id_article = $id_article ORDER BY id_comment ASC";
		$result = $this->m_mysql->select($query);

		return $result;
	}

	public function get_one($id_comment)
	{
		$id_comment = (int)$id_comment;

		$query = "SELECT * FROM comments WHERE id_comment = $id_comment";
		$result = $this->m_mysql->select($query);

		if (isset($result[0]))
		{
			return $result[0];
		}
	}

	public function add($id_user, $id_article, $text)
	{
		$object = array('id_user' => $id_user, 'id_article' => (int)$id_article, 'text' => trim($text));

		if (in_array('', $object))
		{
			return false;
		}

		return $this->m_mysql->insert('comments', $object);
	}

	public function edit($id_comment, $text)
	{
		$id_comment = (int)$id_comment;
		$where = "id_comment = '$id_comment'";
		$object = array('text' => trim($text));

		if (in_array('', $object))
		{
			return false;
		}

		$this->m_mysql->update('comments', $object, $where);
		return true;
	}

	public function delete($id_comment)
	{
		$id_comment = (int)$id_comment;
		$where = "id_comment = '$id_comment'";

		$this->m_mysql->delete('comments', $where);
		return true;
	}
}

?>