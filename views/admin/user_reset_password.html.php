<form method="post" action="<?php echo url_for('admin', 'users', 'reset-password'); ?>">
<ul>
<?php
foreach ($users as $u) {
?>
	<li>
		<label>
			<input type="checkbox" name="user_<?php echo h($u->uid); ?>" />
			<?php echo h($u->name); ?>
			[<?php echo h($u->uid); ?>]
		</label>
	</li>
<?php
}
?>
</ul>
<p>
	<input type="submit" value="Reset password for selected users" />
</p>
</form>
