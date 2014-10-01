<form method="post" action="<?php echo url_for('admin', 'assign', $cid); ?>">
<table border="1">
<tr>
	<th>Active</th>
	<th>Assignment title</th>
</tr>
<?php
foreach ($assignments as $a) {
?>
<tr>
	<td>
		<input type="checkbox" name="assignment_<?php echo h($a->aid); ?>_active" <?php if ($a->active) { echo 'checked="checked"'; } ?> />
	</td>
	<td>
		<?php echo h($a->name); ?>
	</td>
</tr>
<?php
} 
?>
</table>
<p>
	<input type="submit" value="Update" />
</p>
</form>
