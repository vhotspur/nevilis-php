<?php

function page_admin_assignements_list() {
	set('title', 'Assignment administration');
	set('assignments', data_get_assignment_list());
	return html('admin/assignment_list.html.php');
}
