<form method="POST" action="<?php echo url_for("/change-password"); ?>">
	<dl>
		<dt>Current password</dt>
		<dd><input type="password" name="password0" /></dd>
		
		<dt>New password</dt>
		<dd><input type="password" name="password1" /></dd>
		
		<dt>Verify new password</dt>
		<dd><input type="password" name="password2" /></dd>
		
		<dt><input type="submit" value="Change the password" /></dt>
	</dl>
</form>
