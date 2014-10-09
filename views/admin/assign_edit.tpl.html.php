<form method="post" action="%url('admin', 'assign', $cid)%">
<table border="1">
<tr>
	<th>%_Active_%</th>
	<th>%_Assignment title_%</th>
	<th>%_Deadline_%</th>
	<th>%_Deadline (disable upload)_%</th>
</tr>
%foreach $assignments $a%
<tr>
	<td><input type="checkbox" name="assignment_{$a->aid/h}_active" %checked $a->active%/></td>
	<td>{$a->name/h}</td>
	<td><input type="text" name="assignment_{$a->aid/h}_deadline" value="{$a->deadline/h}" /></td>
	<td><input type="text" name="assignment_{$a->aid/h}_deadline_noupload" value="{$a->deadline_noupload/h}" />
	</td>
</tr>
%endforeach%
</table>
<p>
	<input type="submit" value="%_Update_%" />
</p>
</form>
