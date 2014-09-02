<p>
	<a href="<?php echo url_for('/', $course_id); ?>">Go back to the course...</a>
</p>

<?php
echo h($description);

if ($grade != null) {
	printf("<h2>Grade: %d</h2>", $grade->grade);
	if ($grade->comment != "") {
		printf("<p>%s</p>", h($grade->comment));
	}
}

if (count($files) > 0) {
?>
	<h2>Files</h2>
	<form method="post" action="<?php echo url_for($course_id, $assignment_id, 'upload'); ?>" enctype="multipart/form-data">
<?php
	foreach ($files as $f) {
		printf("<fieldset><legend>%s [%d]</legend>\n", h($f->name), $f->afid);
		printf("<p>%s</p>\n", h($f->description));
		
		printf("<p>");
		if ($f->submitted) {
			printf("<a href=\"%s\">%s</a>",
				url_for($course_id, $assignment_id, $f->filename),
				"Download your file");
		} else {
			printf("<i>%s</i>", "File not yet uploaded.");
		}
		printf("</p>\n");
		
		printf("<p>");
		if ($locked) {
			printf("<i>%s</i>", "File uploading not possible.");
		} else {
			printf("<input type=\"file\" name=\"f%d\" />", $f->afid);
		}
		printf("</p>\n");
		printf("</fieldset>\n");
	} ?>
	<?php if (!$locked) { ?>
	<input type="submit" value="Upload files..." />
	<?php } ?>
	</form>
<?php
}
