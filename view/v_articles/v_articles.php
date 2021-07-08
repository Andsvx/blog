<div>
	<? foreach ($articles as $article): ?>
		<a href="articles/article/<?=$article['id_article']?>">
			<div class="article_preview">
				<div><?=$article['title']?></div>
				<div><?=$article['intro']?></div>
			</div>
		</a>
	<? endforeach; ?>
</div>

<div class="pagination">
	<? for ($i = 1; $i <= $pages; $i++): ?>
		<? if ($i == $page): ?>
			<span class="current">
				<a href="articles/list/<?=$i?>"><?=$i?></a>
			</span>
		<? else: ?>
			<span class="ordinary">
				<a href="articles/list/<?=$i?>"><?=$i?></a>
			</span>
		<? endif; ?>
	<? endfor; ?>
</div>