<form method="POST" action="<?php echo url_for("/login"); ?>">
	<fieldset>
		<legend>Log in</legend>
		<dl>
			<dt>User name</dt>
			<dd><input type="text" name="user" /></dd>
			
			<dt>Password</dt>
			<dd><input type="password" name="password" /></dd>
			
			<dt><input type="submit" value="Log in" /></dt>
		</dl>
	</fieldset>
</form>
