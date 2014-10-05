<table border="1">
<tr>
	<th>%_User_%</th>
	%foreach $assignments $a%
		<th>{$a->name/h}</th>
	%endforeach%
</tr>
%foreach $users $u%
<tr>
	<th>{$u->name/h} [{$u->uid/h}]</th>
	%foreach $assignments $a%
	<td>
		{$u->assignments[$a->aid]->grade}<br />
		<small>{$u->assignments[$a->aid]->comment}</small>
	</td>
	%endforeach%
</tr>
%endforeach%
</table>
