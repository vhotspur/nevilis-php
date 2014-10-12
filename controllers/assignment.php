<?php

function can_upload($locked, $deadline_noupload) {
	if ($locked) {
		return false;
	}
	
	if ($deadline_noupload == NULL) {
		return true;
	}
	
	$deadline_unix = strtotime($deadline_noupload);
	return $deadline_unix > time(); 
}

function page_assignment_main() {
	$course_id = params('course');
	$assignment_id = params('assignment');
	
	check_user_can_view_course($course_id);
	
	$course_name = data_get_course_name_by_id($course_id);
	if ($course_name == null) {
		flash('error', _('Uknown course selected.'));
		redirect('/');
	}
	
	check_assignment_belongs_to_course($assignment_id, $course_id);
	
	$info = data_get_assignment_details_for_user_in_course($assignment_id, $course_id, auth_get_current_user());
	if ($info == null) {
		flash('error', _('Uknown assignment selected.'));
		redirect('/', $course_id);
	}
	
	set('title', $info->name);
	set('course_id', $course_id);
	set('assignment_id', $assignment_id);
	set('name', $info->name);
	set('description', $info->description);
	set('files', $info->files);
	set('grade', $info->grade);
	set('usercomment', $info->usercomment);
	set('can_upload', can_upload($info->locked, $info->deadline_noupload));
	
	return html('assignment/index.html.php');
}

function page_assignment_do_upload() {
	
	$course_id = params('course');
	$assignment_id = params('assignment');
	
	check_user_can_view_course($course_id);
	check_assignment_belongs_to_course($assignment_id, $course_id);
	
	$info = data_get_assignment_details_for_user_in_course($assignment_id, $course_id, auth_get_current_user());
	if ($info == null) {
		flash('error', 'Uknown assignment selected.');
		redirect_to($course_id);
	}
	
	if (!can_upload($info->locked, $info->deadline_noupload)) {
		flash('error', _('File uploading is prohibited at the moment.'));
		redirect_to($course_id, $assignment_id);
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) &&
			empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0 ) {
		flash('error', _('The files you are uploading are way too big.'));
		redirect_to($course_id, $assignment_id);
	}
	
	$usercomment = v_post('comment', '');
	data_update_user_comment_for_assignment(auth_get_current_user(),
		$assignment_id, $usercomment);
	
	/* First, prepare the directory where to store them. */
	$target_directory = sprintf("%s/%s/%s", option('file_dir'), auth_get_current_user(), $assignment_id);
	$ok = @mkdir($target_directory, 0777, true);
	if (!$ok && !is_dir($target_directory)) {
		flash('error', _('Failed to prepare directory structure. Contact administrator.'));
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
		
		if ($_FILES[$field]['size'] > 1024 * $f->maxsize) {
			$msg = sprintf(_('file is too big, maximum allowed size is %dKB'), $f->maxsize);
			$failed_files[] = array($f->name, $msg);
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
			data_update_solution_file_timestamp($f->afid, auth_get_current_user());
			$okay_files[] = $f->name;
		} else {
			$failed_files[] = array($f->name, _('failed to move to permanent storage'));
		}
	}
	
	if (count($okay_files) + count($failed_files) == 0) {
		redirect_to($course_id, $assignment_id);
	}
	
	if (count($okay_files) > 0) {
		$message = _('Following files were uploaded (to verify they are not damaged, try to download them):');
		$message .= "<ul>";
		foreach ($okay_files as $f) {
			$message .= sprintf("<li>%s</li>\n", h($f));
		}
		$message .= "</ul>\n";
		flash('info', $message);
	}
	
	if (count($failed_files) > 0) {
		$message = _('Upload of following files failed:');
		$message .= "<ul>";
		foreach ($failed_files as $f) {
			$message .= sprintf("<li>%s (%s)</li>\n", h($f[0]), h($f[1]));
		}
		$message .= "</ul>\n";
		flash('error', $message);
	}
	
	redirect_to($course_id, $assignment_id);
}
