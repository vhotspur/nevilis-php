%foreach $courses $c%
	<li><a href="%url('admin', 'download', $c->cid)%">{$c->name/h} ({$c->adminname/h})</a></li>
%before%
<ul>
%after%
</ul>
%endforeach%
