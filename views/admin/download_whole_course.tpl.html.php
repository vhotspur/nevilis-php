<table border="1">
<tr>
	<th>%_User_%</th>
	%foreach $assignments $a%
		<th>{$a->name/h}</th>
	%endforeach%
	<th>%_All_%</th>
</tr>
%foreach $users $u%
<tr>
	<th>{$u->name/h} [{$u->uid/h}]</th>
	%foreach $assignments $a%
	<td>%if $u->assignments[$a->aid]->uploaded%
		<a href="%url('admin', 'download', $cid, 'single', $u->uid, $a->aid)%">%_Download_%</a>
	%else%
		%_Not uploaded._%
	%endif%</td>
	%endforeach%
	<td><a href="%url('admin', 'download', $cid, 'user', $u->uid)%">%_Download_%</a></td>
</tr>
%endforeach%
<tr>
	<th>%_All_%</th>
	%foreach $assignments $a%
	<td><a href="%url('admin', 'download', $cid, 'assignment', $a->aid)%">%_Download_%</a></td>
	%endforeach%
	<td><a href="%url('admin', 'download', $cid, 'all')%">%_Download_%</a></td>
</tr>
</table>
