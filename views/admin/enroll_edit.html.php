<form method="post" action="<?php echo url_for('admin', 'enroll', $cid); ?>">
<ul>
<?php
foreach ($users as $u) {
?>
	<li>
		<label>
			<input type="checkbox" name="user_<?php echo h($u->uid); ?>" <?php if ($u->enrolled) { echo 'checked="checked"'; } ?> />
			<?php echo h($u->name); ?>
		</label>
	</li>
<?php
}
?>
</ul>
<p>
	<input type="submit" value="Update" />
</p>
</form>
