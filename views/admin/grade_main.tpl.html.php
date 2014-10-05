%foreach $courses $c%
<li>
	<a href="%url('admin', 'grade', $c->cid)%">{$c->name/h}</a>
	(<a href="%url('admin', 'grade', $c->cid, 'print')%">%_Printable_%</a>)
</li>
%before%
<ul>
%after%
</ul>
%endforeach%
