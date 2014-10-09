<?php

function page_main() {
	set('title', _('Assignment submission system'));
	set('courses', data_get_course_list_for_user(auth_get_current_user()));
	
	set('assignments', data_get_active_assignments_for_user(auth_get_current_user()));
	
	return html('main.html.php');
}
