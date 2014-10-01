<?php

function page_admin_assign_main() {
	set('title', 'Course assignments administration');
	set('courses', data_get_course_list());
	return html('admin/assign_main.html.php');
}

function page_admin_assign_edit() {
	$cid = params('cid');
	$info = data_get_course_details($cid);
	if ($info == null) {
		flash('error', 'Course not found');
		redirect_to('admin', 'assign');
	}
	
	$all_assignments = data_get_assignment_list();
	$course_assignments = data_get_assignments_for_course($cid);
	$assigned_ids = array_map(function($a) { return $a->aid; }, $course_assignments);
	
	foreach ($all_assignments as &$a) {
		$a->active = array_search($a->aid, $assigned_ids) !== FALSE;
	}
	
	set('title', 'Set assignments for ' . $info->name);
	set('cid', $cid);
	set('assignments', $all_assignments);

	return html('admin/assign_edit.html.php');
}

function page_admin_assign_to_course() {
	$cid = params('cid');
	$info = data_get_course_details($cid);
	if ($info == null) {
		flash('error', 'Course not found');
		redirect_to('admin', 'assign');
	}
	
	$all_assignments = data_get_assignment_list();
	$active_assignment_ids = array();
	
	foreach ($all_assignments as $a) {
		$checkbox = sprintf("assignment_%s_active", $a->aid);
		$active = isset($_POST[$checkbox]);
		if ($active) {
			$active_assignment_ids[] = $a->aid;
		}
	}
	
	data_assign_to_course($cid, $active_assignment_ids);
	
	flash('info', 'Assignments succesfully assigned.');
	redirect_to('admin', 'assign');
}
