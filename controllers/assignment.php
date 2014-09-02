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
	
	$info = data_get_assignment_details_for_user($assignment_id, get_current_user());
	if ($info == null) {
		flash('error', 'Uknown assignment selected.');
		redirect('/', $course_id);
	}
	
	// TODO: display grade + comment
	
	set('title', $info->name);
	set('course_id', $course_id);
	set('assignment_id', $assignment_id);
	set('name', $info->name);
	set('description', $info->description);
	set('files', $info->files);
	
    return html('assignment/index.html.php');
}


