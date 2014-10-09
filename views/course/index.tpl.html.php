<table class="assignments">
<thead>
	<tr>
		<th class="name">%_Assignment name_%</th>
		<th class="grade">%_Grade_%</th>
		<th class="deadline">%_Deadline_%</th>
		<th class="comment">%_Comment_%</th>
	</tr>
</thead>
%foreach $assignments $a%
<tr>
	<td class="name"><a href="%url($course_id, $a->aid)%">{$a->name/h}</a></td>
	<td class="grade">{$a->grade|&mdash;}</td>
	<td class="deadline">{$a->deadline|&mdash;}</td>
	<td class="comment">{$a->comment/h|}</td>
</tr>
%before%
<tbody>
%after%
</tbody>
%endforeach%
</table>
