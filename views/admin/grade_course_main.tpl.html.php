<ul>
	<li><a href="%url('admin', 'grade', $cid, 'all')%">%_Edit whole course_%</a></li>
	<li style="margin-bottom: 1em;"><a href="%url('admin', 'grade', $cid, 'print')%">%_Print all grades_%</a></li>
	%foreach $assignments $a%
		<li><a href="%url('admin', 'grade', $cid, 'assignment', $a->aid)%">{$a->name/h}</a></li>
	%endforeach%
</ul>