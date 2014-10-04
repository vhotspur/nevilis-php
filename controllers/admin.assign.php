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
	$assigned_ids = array();
	$course_assignments_map = array();
	foreach ($course_assignments as $ca) {
		$assigned_ids[] = $ca->aid;
		$course_assignments_map[ $ca->aid ] = $ca;
	}
	
	foreach ($all_assignments as &$a) {
		$a->active = array_search($a->aid, $assigned_ids) !== FALSE;
		$a->deadline = $a->active ? $course_assignments_map[$a->aid]->deadline : NULL;
		$a->deadline_noupload = $a->active ? $course_assignments_map[$a->aid]->deadline_noupload : NULL;
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
	
	$active_assignments = array();
	
	foreach ($all_assignments as $a) {
		$prefix = 'assignment_' . $a->aid . '_';
		$active = isset($_POST[$prefix . 'active']);
		if ($active) {
			$active_assignments[] = array(
				"assignment" => $a->aid,
				"deadline" => v_post($prefix . 'deadline', NULL),
				"deadline_noupload" => v_post($prefix . 'deadline_noupload', NULL),
			);
		}
	}
	
	data_assign_to_course($cid, $active_assignments);
	
	flash('info', 'Assignments succesfully assigned.');
	redirect_to('admin', 'assign');
}
