<?php

function page_course_main() {
	$course_id = params('course');
	
	check_user_can_view_course($course_id);
	
	$course_name = data_get_course_name_by_id($course_id);
	if ($course_name == null) {
		flash('error', 'Uknown course selected.');
		redirect('/');
	}
	
	set('title', $course_name);
	set('course_id', $course_id);
	
	$assignments = data_get_assigments_and_grades_for_course(auth_get_current_user(), $course_id);
	set('assignments', $assignments);
	
    return html('course/index.html.php');
}
