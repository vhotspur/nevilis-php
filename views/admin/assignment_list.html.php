<ul>
	<li>
		<a href="<?php echo url_for('admin', 'assignments', 'add'); ?>">Add new</a>
	</li>
<?php
foreach ($assignments as $a) {
	printf("<li><a href=\"%s\">%s</a></li>\n",
		url_for('admin', 'assignments', 'edit', $a->aid),
		h($a->name));
}
?>
</ul>