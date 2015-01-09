%foreach $courses $c%
<li>
	<a href="%url('admin', 'grade', $c->cid)%">{$c->name/h} ({$c->adminname/h})</a>
	(<a href="%url('admin', 'grade', $c->cid, 'all')%">%_Edit whole course_%</a>)
	(<a href="%url('admin', 'grade', $c->cid, 'print')%">%_Printable_%</a>)
</li>
%before%
<ul>
%after%
</ul>
%endforeach%
