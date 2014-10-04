<form method="post" action="<?php echo url_for('admin', 'assign', $cid); ?>">
<table border="1">
<tr>
	<th>Active</th>
	<th>Assignment title</th>
	<th>Deadline</th>
	<th>Deadline (disable upload)</th>
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
	<td>
		<input type="text" name="assignment_<?php echo h($a->aid); ?>_deadline" value="<?php echo h($a->deadline); ?>" /> 
	</td>
	<td>
		<input type="text" name="assignment_<?php echo h($a->aid); ?>_deadline_noupload" value="<?php echo h($a->deadline_noupload); ?>" />
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
