<?php

class c_articles extends c_base
{
	private $m_articles;
	private $m_comments;

	protected function before()
	{
		parent::before();
		$this->m_articles = m_articles::instance();
		$this->m_comments = m_comments::instance();
	}

	public function action_list()
	{
		$this->title .= '::Главная страница';

		$quantity = 5;
		$page = isset($this->params[2]) ? (int)$this->params[2] : 1;
		$pages = ceil($this->m_articles->count_all('articles') / $quantity);
		$articles = $this->m_articles->get_all($page, $quantity);

		$this->content = $this->template('view/v_articles/v_articles.php', array('articles' => $articles, 'pages' => $pages, 'page' => $page));
	}

	public function action_article()
	{
		$this->title .= '::Статья';

		if ($this->user == null)
		{
			$hide_form = true;
		}

		$id_article = isset($this->params[2]) ? (int)$this->params[2] : null;

		if ($this->IsPost())
		{
			if ($this->m_comments->add($this->user['id_user'], $id_article, $_POST['text']))
			{
				header("Location: /articles/article/$id_article");
				exit();
			}
			else
			{
				$text = $_POST['text'];
				$error = true;
			}
		}
		else
		{
			$text = '';
			$error = false;
		}

		$article = $this->m_articles->get_one($id_article);
		$comments = $this->m_comments->get_all($id_article);

		$v_comments = $this->template('view/v_articles/v_comments.php', array('comments' => $comments, 'error' => $error, 'hide_form' => (isset($hide_form)) ? $hide_form : false));
		$this->content = $this->template('view/v_articles/v_article.php', array('article' => $article, 'comments' => $v_comments));

		if ($article == null)
		{
			$this->error_404();
			return;
		}
	}

	public function action_edit()
	{
		$this->title .= '::Редактирование статьи';

		$id_article = isset($this->params[2]) ? (int)$this->params[2] : null;
		$article = $this->m_articles->get_one($id_article);

		if ($this->user == null || $this->user['access_level'] < 2)
		{
			if ($this->user == null || $this->user['id_user'] != $article['id_user'])
			{
				$this->error_403();
				return;
			}
		}

		if (isset($_POST['update']))
		{
			if ($this->m_articles->edit($id_article, $_POST['title'], $_POST['content']))
			{
				header("Location: /articles/article/$id_article");
				exit();
			}
			else
			{
				$title = $_POST['title'];
				$content = $_POST['content'];
				$error = true;
			}
		}
		elseif (isset($_POST['delete']))
		{
			$this->m_articles->delete($id_article);
			header("Location: /");
			exit();
		}
		else
		{
			if ($article)
			{
				$title = $article['title'];
				$content = $article['content'];
				$error = false;
			}
			else
			{
				$this->error_404();
				return;
			}
		}

		$this->content = $this->template('view/v_articles/v_edit.php', array('title' => $title, 'content' => $content, 'error' => $error));
	}

	public function action_new()
	{
		$this->title .= '::Новая статья';

		if ($this->user == null)
		{
			$this->error_403();
			return;
		}

		if ($this->IsPost())
		{
			if ($this->m_articles->add($this->user['id_user'], $_POST['title'], $_POST['content']))
			{
				header("Location: /");
				exit();
			}
			else
			{
				$title = $_POST['title'];
				$content = $_POST['content'];
				$error = true;
			}
		}
		else
		{
			$title = '';
			$content = '';
			$error = false;
		}

		$this->content = $this->template('view/v_articles/v_new.php', array('title' => $title, 'content' => $content, 'error' => $error));
	}

	public function action_comm_edit()
	{
		$this->title .= '::Редактирование комментария';

		$id_comment = isset($this->params[2]) ? (int)$this->params[2] : null;
		$comment = $this->m_comments->get_one($id_comment);

		if ($this->user == null || $this->user['access_level'] < 3)
		{
			if ($this->user == null || $this->user['id_user'] != $comment['id_user'])
			{
				$this->error_403();
				return;
			}
		}

		if (isset($_POST['update']))
		{
			if ($this->m_comments->edit($id_comment, $_POST['text']))
			{
				$id_article = $_SESSION['id_article'];
				unset($_SESSION['id_article']);

				header("Location: /articles/article/$id_article");
				exit();
			}
			else
			{
				$text = $_POST['text'];
				$error = true;
			}
		}
		elseif (isset($_POST['delete']))
		{
			$this->m_comments->delete($id_comment);

			$id_article = $_SESSION['id_article'];
			unset($_SESSION['id_article']);

			header("Location: /articles/article/$id_article");
			exit();
		}
		else
		{
			if ($comment)
			{
				$_SESSION['id_article'] = $comment['id_article'];

				$text = $comment['text'];
				$error = false;
			}
			else
			{
				$this->error_404();
				return;
			}
		}

		$this->content = $this->template('view/v_articles/v_comm_edit.php', array('text' => $text, 'error' => $error));
	}
}

?>