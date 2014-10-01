<ul>
<?php
foreach ($courses as $c) {
	printf("<li><a href=\"%s\">%s</a></li>\n",
		url_for('admin', 'grade', $c->cid),
		h($c->name));
}
?>
</ul>
