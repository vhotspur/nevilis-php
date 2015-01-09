<a href="%url('admin', 'grade', $cid)%">%_Back to course grading menu_%</a>

<form method="post" action="%url('admin', 'grade', $cid, 'assignment', $aid)%">

<table class="grading">
<thead>
<tr>
	<th>%_User_%</th>
	<th>%_Comment_%</th>
	<th>%_Last upload_%</th>
	<th>%_Grade_%</th>
	<th>%_Comment_%</th>
	<th>%_Locked_%</th>
</tr>
</thead>
%foreach $users $u%
%before%
<tbody>
%after%
</tbody>
%body%
<tr>
	<th>{$u->name/h} [{$u->uid/h}]</th>
	<?php
		$a = $u->assignment;
		$locked_checked = $a->locked ? 'checked="checked"' : '';
	?>
	<td>{$a->usercomment/h}</td>
	<td>{$a->last_upload/h}</td>
	<td class="grading grade"><input type="text" name="grade_{$u->uid}" value="{$a->grade}" size="5" /></td>
	<td class="grading"><input type="text" name="comment_{$u->uid}" value="{$a->comment}" /></td>
	<td class="grading locked">
		<label>
			&nbsp;&nbsp;&nbsp;
			<input type="checkbox" name="locked_{$u->uid}" {$locked_checked} />
			&nbsp;&nbsp;&nbsp;
		</label>
	</td>
</tr>
%endforeach%
</table>
<p>
	<input type="submit" value="%_Update_%" />
</p>
</form>