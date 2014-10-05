%foreach $courses $c%
<li><a href="%url('admin', 'assign', $c->cid)%">{$c->name/h}</a></li>
%before%
<ul>
%after%
</ul>
%endforeach%
