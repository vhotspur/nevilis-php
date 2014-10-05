<form method="post" action="%url('admin', 'users', 'reset-password')%">
%foreach $users $u%
<li>
	<label>
		<input type="checkbox" name="user_{$u->uid/h}" />
		{$u->name/h} [{$u->uid/h}]
	</label>
</li>
%before%
<ul>
%after%
</ul>
%endforeach%
<p>
	<input type="submit" value="%_Reset password for selected users_%" />
</p>
</form>
