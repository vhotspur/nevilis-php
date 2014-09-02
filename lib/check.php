<?php


function check_user_can_view_course($course_id) {
	if (data_is_user_enrolled_to_course(auth_get_current_user(), $course_id)) {
		return;
	}
	
	flash('error', 'Unknown course or you are not member of that group.');
	redirect_to('/');
}

function check_assignment_belongs_to_course($assignment, $course) {
	// TODO: implement
}
