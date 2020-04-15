<?php

class m_articles
{
	private static $instance;

	public static function instance()
	{
		if (self::$instance == null)
		{
			self::$instance = new m_articles();
		}
		return self::$instance;
	}

	private $m_mysql;

	function __construct()
	{
		$this->m_mysql = m_mysql::instance();
	}

	public function get_all($page, $quantity)
	{
		$skip = $page > 0 ? ($page - 1) * $quantity : 0;

		$query = "SELECT * FROM articles ORDER BY id_article DESC LIMIT $skip, $quantity";
		$result = $this->m_mysql->select($query);

		foreach($result as $key => $article)
		{
			$result[$key]['intro'] = $this->intro($article);
		}

		return $result;
	}

	public function get_one($id_article)
	{
		$query = "SELECT articles.*, users.login FROM articles LEFT JOIN users ON articles.id_user = users.id_user WHERE id_article = $id_article";
		$result = $this->m_mysql->select($query);

		if (isset($result[0]))
		{
			return $result[0];
		}
	}

	public function add($id_user, $title, $content)
	{
		$object = array('id_user' => $id_user, 'title' => trim($title), 'content' => trim($content));

		if (in_array('', $object))
		{
			return false;
		}

		return $this->m_mysql->insert('articles', $object);
	}

	public function edit($id_article, $title, $content)
	{
		$id_article = (int)$id_article;
		$where = "id_article = '$id_article'";
		$object = array('title' => trim($title), 'content' => trim($content));

		if (in_array('', $object))
		{
			return false;
		}

		$this->m_mysql->update('articles', $object, $where);
		return true;
	}

	public function delete($id_article)
	{
		$id_article = (int)$id_article;
		$where = "id_article = '$id_article'";

		$this->m_mysql->delete('articles', $where);
		return true;
	}

	public function intro($article)
	{
		$res = $article['content'];

		if(strlen($res) > 100)
		{
			$res = substr($article['content'], 0, 100);
			$temp = explode(' ', $res);
			unset($temp[count($temp) - 1]);
			$res = implode(' ', $temp) . " ...";
		}
		return $res;
	}

	public function count_all()
	{
		return $this->m_mysql->count_all('articles');
	}
}

?>