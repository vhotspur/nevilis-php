<ul>
	<li><a href="%url('admin', 'assignments', 'add')%">Add new</a></li>
%foreach $assignments $a%
	<li><a href="%url('admin', 'assignments', 'edit', $a->aid)%">{$a->name/h}</a></li>
%endforeach%
</ul>