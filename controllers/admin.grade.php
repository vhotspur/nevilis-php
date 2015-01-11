<?php

function page_admin_grade_get_course_details_or_die($cid) {
	$info = data_get_course_details($cid);
	if ($info == null) {
		flash('error', _('Course not found'));
		redirect_to('admin', 'grade');
	}
	return $info;
}

function page_admin_grade_main() {
	set('title', _('Grading administration'));
	set('courses', data_get_course_list());
	return html('admin/grade_main.html.php');
}

function page_admin_grade_course_main() {
	$cid = params('cid');
	$info = page_admin_grade_get_course_details_or_die($cid);
	
	$assignments = data_get_assignments_for_course($cid);
	
	set('cid', $cid);
	set('title', sprintf(_('Grading of %s (%s)'), $info->name, $info->adminname));
	set('assignments', $assignments);
	
	return html('admin/grade_course_main.html.php');
}

function get_grade_details($aid, $uid) {
	$grade_info = data_get_assignment_details_for_user($aid, $uid);
	if ($grade_info->grade == null) {
		$grade_info->grade = make_object(array(
				"grade" => "",
				"locked" => 0,
				"comment" => ""
		));
	}
		
	// find the timestamp of last uploaded file
	$last_upload = null;
	$highest_timestamp = 0;
	foreach ($grade_info->files as $f) {
		if ($f->submitted) {
			$uploaded = strtotime($f->upload_date);
			if ($uploaded > $highest_timestamp) {
				$highest_timestamp = $uploaded;
			}
		}
	}
	if ($highest_timestamp > 0) {
		$last_upload = date('Y-m-d H:i:s', $highest_timestamp);
	}
	$grade_info->grade->last_upload = $last_upload;
	$grade_info->grade->usercomment = $grade_info->usercomment;
	
	return $grade_info->grade;
}

function prepare_grades_for_course() {
	$cid = params('cid');
	$info = page_admin_grade_get_course_details_or_die($cid);
	
	$assignments = data_get_assignments_for_course($cid);
	$users = data_get_enrolled_users($cid);
	foreach ($users as &$u) {
		$u->assignments = array();
		foreach ($assignments as $a) {
			$u->assignments[ $a->aid ] = get_grade_details($a->aid, $u->uid);
		}
	}
	
	set('cid', $cid);
	set('title', sprintf(_('Grades for %s (%s)'), $info->name, $info->adminname));
	set('assignments', $assignments);
	set('users', $users);
}

function page_admin_grade_edit_whole_course() {
	prepare_grades_for_course();
	
	return html('admin/grade_whole_course.html.php');
}

function page_admin_printable_grades_whole_course() {
	layout('layout/empty.html.php');
	prepare_grades_for_course();
	
	return html('admin/grade_whole_course_print.html.php');
}

function page_admin_grade_whole_course() {
	$cid = params('cid');
	$info = page_admin_grade_get_course_details_or_die($cid);
	
	$assignments = data_get_assignments_for_course($cid);
	$users = data_get_enrolled_users($cid);
	
	$data = array();
	foreach ($users as &$u) {
		foreach ($assignments as $a) {
			$var_suffix = '_' . $a->aid . '_' . $u->uid;
			$grade = v_post('grade' . $var_suffix, '');
			$comment = v_post('comment' . $var_suffix, '');
			$locked = isset($_POST['locked' . $var_suffix]);
			
			$data[] = array(
				"user" => $u->uid,
				"assignment" => $a->aid,
				"grade" => $grade,
				"comment" => $comment,
				"locked" => $locked
			);
		}
	}
	
	data_update_grades($data);
	
	flash('info', _('Grades updated.'));
	redirect_to('admin', 'grade');
}

function page_admin_grade_edit_assignment_in_course() {
	$cid = params('cid');
	$course = page_admin_grade_get_course_details_or_die($cid);
	
	$aid = params('aid');
	$assignment = data_get_assignment_details($aid);
	if ($assignment == null) {
		flash('error', _('Assignment not found'));
		redirect_to('admin', 'grade', $cid);
	}
	// FIXME: check assignment is in course
	
	$users = data_get_enrolled_users($cid);
	// FIXME: refactor
	foreach ($users as &$u) {
		$u->assignment = get_grade_details($aid, $u->uid);
	}
	
	set('cid', $cid);
	set('aid', $aid);
	set('title', sprintf(_('Grading %s in %s (%s)'), $assignment->name, $course->name, $course->adminname));
	set('users', $users);
	
	return html('admin/grade_assignment.html.php');
}

function page_admin_grade_assignment_in_course() {
	$cid = params('cid');
	$course = page_admin_grade_get_course_details_or_die($cid);
	
	$aid = params('aid');
	$assignment = data_get_assignment_details($aid);
	if ($assignment == null) {
		flash('error', _('Assignment not found'));
		redirect_to('admin', 'grade', $cid);
	}
	// FIXME: check assignment is in course
	
	$users = data_get_enrolled_users($cid);
	
	$data = array();
	foreach ($users as &$u) {
		$var_suffix = '_' . $u->uid;
		$grade = v_post('grade' . $var_suffix, '');
		$comment = v_post('comment' . $var_suffix, '');
		$locked = isset($_POST['locked' . $var_suffix]);
			
		$data[] = array(
			"user" => $u->uid,
			"assignment" => $aid,
			"grade" => $grade,
			"comment" => $comment,
			"locked" => $locked
		);
	}
	
	data_update_grades($data);
	
	flash('info', _('Grades updated.'));
	redirect_to('admin', 'grade', $cid);
}