<p>
	<a href="<?php echo url_for('admin', 'courses'); ?>">Back to list of courses</a>
</p>

<form method="post" action="<?php
 	if ($cid === null) {
		echo url_for('admin', 'courses', 'add');
	} else {
 		echo url_for('admin', 'courses', 'edit', $cid);
	}
?>">
	<dl>
		<dt>Course id (part of URL)</dt>
		<dd><?php
	if ($cid === null) {
		echo "<input type=\"text\" name=\"cid\" />\n";
	} else {
		echo "<b>" . h($cid) . "</b> (cannot be changed)";
	}
		?></dd>
		
		<dt>Course name</dt>
		<dd>
			<input type="text" name="name" value="<?php echo h($name); ?>" />
		</dd>
		
		<dt>
			<input type="submit" value="Update" />
		</dt>
	</dl>
</form>
