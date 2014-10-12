<table class="assignments">
<thead>
	<tr>
		<th class="name">%_Assignment name_%</th>
		<th class="grade">%_Grade_%</th>
		<th class="deadline">%_Deadline_%</th>
		<th class="comment">%_Comment_%</th>
	</tr>
</thead>
<tbody>
%foreach $assignments $a%
<tr>
	<td class="name"><a href="%url($course_id, $a->aid)%">{$a->name/h}</a></td>
	<td class="grade">{$a->grade|&mdash;}</td>
	<td class="deadline"><?php
	if (!@empty($a->deadline)) {
		$unix_time = strtotime($a->deadline);
		echo strftime('%c', $unix_time);
	} else {
		echo "&mdash;";
	}
	?></td>
	<td class="comment">{$a->comment/h|}</td>
</tr>
%else%
<tr>
	<td colspan="4">%_No assignments yet._%</td>
</tr>
%endforeach%
</tbody>
</table>
