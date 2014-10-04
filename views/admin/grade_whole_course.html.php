<form method="post" action="<?php echo url_for('admin', 'grade', $cid); ?>">

<table border="1">
<tr>
	<th>User</th>
	<?php
	foreach ($assignments as $a) {
		?>
		<th>
			<?php echo h($a->name); ?>
			<br />
			<?php echo h($a->deadline); ?>
		</th>
		<?php
	} 
	?>
</tr>
<?php
foreach ($users as $u) {
?>
<tr>
	<th>
		<?php echo h($u->name); ?><br />[<?php echo h($u->uid); ?>]
	</th>
	<?php
	foreach ($assignments as $a) {
		$info = $u->assignments[$a->aid];
		$grade_field = sprintf('grade_%s_%s', $a->aid, $u->uid);
		$comment_field = sprintf('comment_%s_%s', $a->aid, $u->uid);
		$locked_field = sprintf('locked_%s_%s', $a->aid, $u->uid);
		$locked_checked = $info->locked ? 'checked="checked"' : '';
		?>
		<td>
			Last upload: <?php echo h($info->last_upload); ?>
			<br />
			<input type="text" name="<?php echo h($grade_field); ?>" value="<?php echo h($info->grade); ?>" size="3" />
			<input type="text" name="<?php echo h($comment_field); ?>" value="<?php echo h($info->comment); ?>" size="10" />
			<input type="checkbox" name="<?php echo h($locked_field); ?>" <?php echo $locked_checked; ?> />
		</td>
		<?php
	} 
	?>
</tr>
<?php 
}
?>
</table>

<p>
	<input type="submit" value="Update" />
</p>
</form>