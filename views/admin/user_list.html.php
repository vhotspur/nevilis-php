<p>
	<a href="<?php echo url_for('admin', 'users', 'reset-password'); ?>">Reset passwords</a>
</p>

<?php
if (flash_now('reset_passwords') != null) {
	$passwords = flash_now('reset_passwords');
	?><textarea cols="60" rows="5"><?php
	foreach ($passwords as $p) {
		printf("%s (%s) %s\n", $p["name"], $p["uid"], $p["password"]);
	}
	?></textarea>
	<?php
} 
?>

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