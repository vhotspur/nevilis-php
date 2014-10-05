<form method="post" action="%if $cid === null%%url('admin', 'courses', 'add')%%else%%url('admin', 'courses', 'edit', $cid)%%endif%">
<dl>
<dt>%_Course id (part of URL)_%</dt>
	<dd>
	%if $cid === null%
		<input type="text" name="cid" />
	%else%
		<b>{$cid/h}</b> (%_cannot be changed_%)
	%endif%
<dt>%_Course name_%</dt>
	<dd><input type="text" name="name" value="{$name/h}" /></dd>
<dt><input type="submit" value="%_Update_%" /></dt>
</dl>
</form>
