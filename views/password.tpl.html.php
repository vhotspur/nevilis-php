<form method="POST" action="%url('change-password')%">
	<dl>
		<dt>%_Current password_%</dt>
		<dd><input type="password" name="password0" /></dd>
		
		<dt>%_New password_%</dt>
		<dd><input type="password" name="password1" /></dd>
		
		<dt>%_Verify new password_%</dt>
		<dd><input type="password" name="password2" /></dd>
		
		<dt><input type="submit" value="%_Change the password_%" /></dt>
	</dl>
</form>
