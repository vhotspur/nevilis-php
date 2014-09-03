<?php

function page_assignment_main() {
	$course_id = params('course');
	$assignment_id = params('assignment');
	
	check_user_can_view_course($course_id);
	
	$course_name = data_get_course_name_by_id($course_id);
	if ($course_name == null) {
		flash('error', 'Uknown course selected.');
		redirect('/');
	}
	
	check_assignment_belongs_to_course($assignment_id, $course_id);
	
	$info = data_get_assignment_details_for_user($assignment_id, auth_get_current_user());
	if ($info == null) {
		flash('error', 'Uknown assignment selected.');
		redirect('/', $course_id);
	}
	
	set('title', $info->name);
	set('course_id', $course_id);
	set('assignment_id', $assignment_id);
	set('name', $info->name);
	set('description', $info->description);
	set('files', $info->files);
	set('grade', $info->grade);
	set('locked', $info->locked);
	
    return html('assignment/index.html.php');
}

function page_assignment_do_upload() {
	
	$course_id = params('course');
	$assignment_id = params('assignment');
	
	check_user_can_view_course($course_id);
	check_assignment_belongs_to_course($assignment_id, $course_id);
	
	$info = data_get_assignment_details_for_user($assignment_id, auth_get_current_user());
	if ($info == null) {
		flash('error', 'Uknown assignment selected.');
		redirect_to($course_id);
	}
	
	if ($info->locked) {
		flash('error', 'File uploading is not possible at the moment.');
		redirect_to($course_id, $assignment_id);
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) &&
			empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0 ) {
		flash('error', 'The files you are uploading are way too big.');
		redirect_to($course_id, $assignment_id);
	}
	
	/* First, prepare the directory where to store them. */
	$target_directory = sprintf("%s/%s/%s", option('file_dir'), auth_get_current_user(), $assignment_id);
	$ok = @mkdir($target_directory, 0777, true);
	if (!$ok && !is_dir($target_directory)) {
		flash('error', 'Failed to prepare directory structure. Contact administrator.');
		redirect_to($course_id, $assignment_id);
	}
	
	$okay_files = array();
	$failed_files = array();
	foreach ($info->files as $f) {
		$field = "f" . $f->afid;
		if (!isset($_FILES[$field])) {
			continue;
		}
		
		if ($_FILES[$field]['error'] != UPLOAD_ERR_OK) {
			if ($_FILES[$field]['error'] == UPLOAD_ERR_NO_FILE) {
				continue;
			}
			$failed_files[] = array($f->name, $_FILES[$field]['error']);
			continue;
		}
		
		$validated = file_validate($_FILES[$field]['tmp_name'], $f->validation);
		if ($validated !== true) {
			$failed_files[] = array($f->name, implode(', ', $validated));
			continue;
		}
		
		$moved = @move_uploaded_file($_FILES[$field]['tmp_name'], $target_directory . '/' . $f->filename);
		if ($moved) {
			if (!$f->submitted) {
				data_add_solution_file($f->afid, auth_get_current_user());
			}
			$okay_files[] = $f->name;
		} else {
			$failed_files[] = array($f->name, "failed to move to permanent storage");
		}
	}
	
	if (count($okay_files) + count($failed_files) == 0) {
		redirect_to($course_id, $assignment_id);
	}
	
	if (count($okay_files) > 0) {
		$message = "Following files were uploaded (to verify they are not damaged, try to download them):";
		$message .= "<ul>";
		foreach ($okay_files as $f) {
			$message .= sprintf("<li>%s</li>\n", h($f));
		}
		$message .= "</ul>\n";
		flash('info', $message);
	}
	
	if (count($failed_files) > 0) {
		$message = "Upload of following files failed";
		$message .= "<ul>";
		foreach ($failed_files as $f) {
			$message .= sprintf("<li>%s (%s)</li>\n", h($f[0]), h($f[1]));
		}
		$message .= "</ul>\n";
		flash('error', $message);
	}
	
	redirect_to($course_id, $assignment_id);
}
