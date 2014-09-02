<ul>
	<li>
		<a href="<?php echo url_for('admin', 'users', 'add'); ?>">Add new</a>
	</li>
<?php
foreach ($users as $u) {
	printf("<li><a href=\"%s\">%s</a></li>\n",
		url_for('admin', 'users', 'edit', $u->uid),
		h($u->name));
}
?>
</ul>