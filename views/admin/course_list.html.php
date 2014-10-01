<ul>
	<li>
		<a href="<?php echo url_for('admin', 'courses', 'add'); ?>">Add new</a>
	</li>
<?php
foreach ($courses as $c) {
	printf("<li><a href=\"%s\">%s</a></li>\n",
		url_for('admin', 'courses', 'edit', $c->cid),
		h($c->name));
}
?>
</ul>