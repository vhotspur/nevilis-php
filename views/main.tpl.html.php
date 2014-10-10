<table class="assignments">
<thead>
	<tr>
		<th>%_Course_%</th>
		<th>%_Assignment_%</th>
		<th>%_Deadline_%</th>
		<th>%_Remaining time_%</th>
	</tr>
</thead>
<tbody>
%foreach $assignments $a%
<tr>
	<td>{$a->coursename/h}</td>
	<td><a href="%url($a->cid, $a->aid)%">{$a->assignmentname/h}</a></td>
	<td><?php
	if (!@empty($a->deadline)) {
		$unix_time = strtotime($a->deadline);
		echo strftime('%c', $unix_time);
	} else {
		echo "&mdash";
	}
	?></td>
	<td><?php $a->remaininghours += 0.; if ($a->remaininghours > 49) {
		printf(_("%d days"), $a->remaininghours / 24);
	} else if ($a->remaininghours > 1) {
		printf(_("%d hours"), $a->remaininghours);
	} else {
		printf(_("%d minutes"), $a->remaininghours * 60);
	}
	?></td>
</tr>
%else%
<tr>
	<td colspan="4">%_No active assignments._%</td>
</tr>
%endforeach%
</tbody>
</table>
