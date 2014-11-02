<?php

function page_file_download() {
	$course = params('course');
	$assignment = params('assignment');
	$filename = params('filename');
	$user = auth_get_current_user();

	check_user_can_view_course($course);
	check_assignment_belongs_to_course($assignment, $course);
	
	$details = data_get_file_details($filename, $user, $assignment);
	if ($details == null) {
		flash('error', _('File not uploaded.'));
		redirect_to($course, $assignment);
	}
	
	$path = sprintf('%s/%s/%s/%s', option('file_dir'), $user, $assignment, $filename);
	// copied from lib/limonade.php
	if (file_exists($path)) {
		if (empty($details->mime)) {
			$content_type = @mime_type(file_extension($filename));
		} else {
			$content_type = $details->mime;
		}
		$header = 'Content-type: '.$content_type;
		if (@file_is_text($path)) {
			$header .= '; charset='.strtolower(option('encoding'));
		}
		if (!headers_sent()) {
			header($header);
		}
		return file_read($path, 0);
	} else {
		flash('error', sprintf(_('File %s not found, contact administrator.'), $filename));
		redirect_to($course, $assignment);
	}
}

