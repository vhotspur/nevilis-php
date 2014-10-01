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
$files_count = count($assignment_files);
for ($file_index = 0; $file_index < $files_count; $file_index++) {
	$ff = $assignment_files[$file_index];
?>
		<dt>
			File #<?php echo ( $file_index + 1); ?>
			<input type="hidden" name="file_<?php echo $file_index; ?>_id" value="<?php echo h($ff->afid); ?>" />
			<?php
				if ($ff->afid == 0) {
					echo(" [new file]");
				} else {
					printf(" [id=%d]", $ff->afid);
				}
			?>
		</dt>
		<dd>
			<dl>
				<dt>Filename</dt>
				<dd><input type="text" name="file_<?php echo $file_index; ?>_filename" value="<?php echo h($ff->filename); ?>" /></dd>
				<dt>Name</dt>
				<dd><input type="text" name="file_<?php echo $file_index; ?>_name" value="<?php echo h($ff->name); ?>" /></dd>
				<dt>Description</dt>
				<dd><input type="text" name="file_<?php echo $file_index; ?>_description" value="<?php echo h($ff->description); ?>" /></dd>
				<dt>Maximum file size (KB)</dt>
				<dd><input type="text" name="file_<?php echo $file_index; ?>_maxsize" value="<?php echo h($ff->maxsize); ?>" /></dd>
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
