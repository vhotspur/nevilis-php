<table border="1">
	<thead>
		<tr>
			<th>Assignment name</th>
			<th>Grade</th>
			<th>Comment</th>
			<th>Deadline</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($assignments as $a) {
	$grade = $a->grade == null ? "-" : $a->grade;
	$comment = $a->comment == null ? "" : $a->comment;
	$deadline = $a->deadline == null ? "-" : $a->deadline;
	printf("<tr><td><a href=\"%s\">%s</a> (%d)</td><td>%s</td><td>%s</td><td>%s</td></tr>\n",
		url_for($course_id, $a->aid),
		h($a->name), $a->filecount, $grade, h($comment), h($deadline));
}
?>
	</tbody>
</table>
