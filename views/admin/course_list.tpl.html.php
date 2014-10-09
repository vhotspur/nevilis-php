<ul>
	<li><a href="%url('admin', 'courses', 'add')%">Add new</a></li>
%foreach $courses $c%
	<li><a href="%url('admin', 'courses', 'edit', $c->cid)%">{$c->name/h} ({$c->adminname/h})</a></li>
%endforeach%
</ul>