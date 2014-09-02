<?php

function page_main() {
	set('title', 'Assignment submission system');
	set('courses', data_get_course_list_for_user(auth_get_current_user()));
	
    return html('main.html.php');
}
