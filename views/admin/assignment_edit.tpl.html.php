<form method="post" action="%if $aid === null%%url('admin', 'assignments', 'add')%%else%%url('admin', 'assignments', 'edit', $aid)%%endif%">
<dl>
<dt>%_Assignment id (part or URL)_%</dt>
	<dd>
	%if $aid === null%
		<input type="text" name="aid" />
	%else%
		<b>{$aid/h}</b> (%_cannot be changed_%)
	%endif%
	</dd>
<dt>%_Name_%</dt>
	<dd><input type="text" name="name" value="{$name/h}" /></dd>
<dt>Description</dt>
	<dd><textarea name="description">{$description/h}</textarea>

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
				<dd><ul>
				<?php
				foreach ($GLOBALS['FILE_VALIDATORS'] as $validator_id => $validator) {
					$is_used = array_search($validator_id, $ff->validation);
					$checked = $is_used === FALSE ? "" : 'checked="checked"';
					$field = sprintf("file_%d_validator_%s",
						$file_index, $validator_id);
					?>
					<li>
						<label>
							<input type="checkbox" name="<?php echo $field; ?>" <?php echo $checked; ?> />
							<?php echo h($validator["name"]); ?>
						</label>
					</li>
					<?php
				}
				?>
				</ul></dd>
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
