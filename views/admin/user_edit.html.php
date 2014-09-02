<p>
	<a href="<?php echo url_for('admin', 'users'); ?>">Back to list of users</a>
</p>

<form method="post" action="<?php
 	if ($uid === null) {
		echo url_for('admin', 'users', 'add');
	} else {
 		echo url_for('admin', 'users', 'edit', $uid);
	}
?>">
	<dl>
		<dt>User login name</dt>
		<dd><?php
	if ($uid === null) {
		echo "<input type=\"text\" name=\"uid\" />\n";
	} else {
		echo "<b>" . h($uid) . "</b> (cannot be changed)";
	}
		?></dd>
		
		<dt>Real name</dt>
		<dd>
			<input type="text" name="name" value="<?php echo h($name); ?>" />
		</dd>
		
		<dt>
			<input type="submit" value="Update" />
		</dt>
	</dl>
</form>
