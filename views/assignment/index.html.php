<p>
	<a href="<?php echo url_for('/', $course_id); ?>">Go back to the course...</a>
</p>

<?php
echo h($description);

if (count($files) > 0) {
?>
	<h2>Files</h2>
	<form method="post" action="<?php echo url_for($course_id, $assignment_id); ?>">
<?php
	foreach ($files as $f) {
		printf("<fieldset><legend>%s</legend>\n", h($f->name));
		printf("<p>%s</p>\n<dt>\n", h($f->description));
		// FIXME: offer download only when file exist
		printf("<p><a href=\"%s\">%s</a> %s\n",
			url_for($course_id, $assignment_id, $f->filename),
			"Download previously uploaded file",
			"or upload a new one: ");
		printf("<input type=\"file\" name=\"f%d\" />", $f->afid);
		printf("</fieldset>\n");
	} ?>
	<input type="submit" value="Upload files..." />
	</form>
<?php
}
