<form method="post" action="%url('admin', 'enroll', $cid)%">
%foreach $users $u%
<li>
	<label>
		<input type="checkbox" name="user_{$u->uid/h}" %checked $u->enrolled%/>
		{$u->name/h} [{$u->uid/h}]
	</label>
</li>
%before%
<ul>
%after%
</ul>
%endforeach%
<p>
	<input type="submit" value="%_Update_%" />
</p>
</form>
