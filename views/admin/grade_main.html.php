<ul>
<?php
foreach ($courses as $c) {
	?>
	<li>
		<a href="<?php echo url_for('admin', 'grade', $c->cid); ?>"><?php echo h($c->name); ?></a>
		&nbsp;(<a href="<?php echo url_for('admin', 'grade', $c->cid, 'print'); ?>">Printable</a>)
	<?php
}
?>
</ul>
