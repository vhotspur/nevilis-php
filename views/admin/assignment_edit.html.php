<p>
	<a href="<?php echo url_for('admin', 'assignments'); ?>">Back to list of assignments</a>
</p>

<form method="post" action="<?php
 	if ($aid === null) {
		echo url_for('admin', 'assignments', 'add');
	} else {
 		echo url_for('admin', 'assignments', 'edit', $aid);
	}
?>">
	<dl>
		<dt>Assignment id (part or URL)</dt>
		<dd><?php
	if ($aid === null) {
		echo "<input type=\"text\" name=\"aid\" />\n";
	} else {
		echo "<b>" . h($aid) . "</b> (cannot be changed)";
	}
		?></dd>
		
		<dt>Name</dt>
		<dd>
			<input type="text" name="name" value="<?php echo h($name); ?>" />
		</dd>
		
		<dt>Description</dt>
		<dd>
			<textarea name="description"><?php echo h($description); ?></textarea>
		
<?php
// TODO: Iterate through existing files.
$file_index = 0; 

for ($i = 0; $i < 3; $i++, $file_index++) {
?>
		<dt>File #<?php echo ( $file_index + 1); ?><input type="hidden" name="file_<?php echo $file_index; ?>_id" value="0" /></dt>
		<dd>
			<dl>
				<dt>Filename</dt>
				<dd><input type="text" name="file_<?php echo $file_index; ?>_filename" /></dd>
				<dt>Name</dt>
				<dd><input type="text" name="file_<?php echo $file_index; ?>_name" /></dd>
				<dt>Description</dt>
				<dd><input type="text" name="file_<?php echo $file_index; ?>_description" /></dd>
				<dt>Maximum file size (KB)</dt>
				<dd><input type="text" name="file_<?php echo $file_index; ?>_maxsize" /></dd>
				<dt>Content validator</dt>
				<dd>TODO</dd>
			</dl>
		</dd>
<?php
}
?>
		
		<dt>
			<input type="submit" value="Update" />
		</dt>
	</dl>
</form>
