<div class="article">
	<div class="article_header">
		<div class="article_edit"><a href="articles/edit/<?=$article['id_article']?>"><img src="view/img/edit_2.png"></a></div>
		<div class="article_title"><?=$article['title']?></div>
	</div>
	<div class="article_body">
		<div class="article_content">
			<div><?=nl2br($article['content'])?></div>
			<div class="author">Автор: <?=$author = isset($article['login']) ? $article['login'] : '---';?></div>
		</div>
	</div>
</div>
<?=$comments?>