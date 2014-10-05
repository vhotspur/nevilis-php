<form method="post" action="%if $uid === null%%url('admin', 'users', 'add')%%else%%url('admin', 'users', 'edit', $uid)%%endif%">
<dl>
<dt>%_User login name_%</dt>
	<dd>
	%if $uid === null%
		<input type="text" name="uid" />
	%else%
		<b>{$uid/h}</b> (%_cannot be changed_%)
	%endif%
	</dd>
<dt>%_Real name_%</dt>
	<dd><input type="text" name="name" value="{$name/h}" /></dd>
<dt><input type="submit" value="%_Update_%" /></dt>
</dl>
</form>
