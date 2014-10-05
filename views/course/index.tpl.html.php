<table border="1">
<thead>
	<tr>
		<th>%_Assignment name_%</th>
		<th>%_Grade_%</th>
		<th>%_Deadline_%</th>
		<th>%_Comment_%</th>
	</tr>
</thead>
%foreach $assignments $a%
<tr>
	<td><a href="%url($course_id, $a->aid)%">{$a->name/h}</a></td>
	<td>{$a->grade|&mdash;}</td>
	<td>{$a->deadline|&mdash;}</td>
	<td>{$a->comment/h|}</td>
</tr>
%before%
<tbody>
%after%
</tbody>
%endforeach%
</table>
