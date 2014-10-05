<form method="post" action="%url('admin', 'grade', $cid)%">

<table border="1">
<tr>
	<th>%_User_%</th>
	%foreach $assignments $a%
		<th>{$a->name/h}<br />{$a->deadline/h}</th>
	%endforeach%
</tr>
%foreach $users $u%
<tr>
	<th>{$u->name/h} [{$u->uid/h}]</th>
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
%endforeach%
</table>
<p>
	<input type="submit" value="%_Update_%" />
</p>
</form>