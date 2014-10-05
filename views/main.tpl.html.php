%foreach $courses $c%
	<li><a href="%url($c->cid)%">{$c->name/h}</a></li>
%before%
<ul>
%after%
</ul>
%else%
<p>%_No courses assigned yet._%</p>
%endforeach%
