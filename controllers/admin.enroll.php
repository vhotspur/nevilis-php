<?php

function page_admin_enroll_main() {
	set('title', 'User enrollment administration');
	set('courses', data_get_course_list());
	return html('admin/enroll_main.html.php');
}

function page_admin_enroll_edit() {
	$cid = params('cid');
	$info = data_get_course_details($cid);
	if ($info == null) {
		flash('error', 'Course not found');
		redirect_to('admin', 'enroll');
	}
	
	$all_users = data_get_user_list();
	$enrolled_users_tmp = data_get_enrolled_users_uids_only($cid);
	$enrolled_users = array();
	foreach ($enrolled_users_tmp as $u) {
		$enrolled_users[] = $u->uid;
	}
	
	foreach ($all_users as &$u) {
		$u->enrolled = array_search($u->uid, $enrolled_users) !== FALSE;
	}

	set('title', 'Enroll users for ' . $info->name);
	set('cid', $cid);
	set('users', $all_users);

	return html('admin/enroll_edit.html.php');
}

function page_admin_enroll_to_course() {
	$cid = params('cid');
	$info = data_get_course_details($cid);
	if ($info == null) {
		flash('error', 'Course not found');
		redirect_to('admin', 'enroll');
	}
	
	$all_users = data_get_user_list();
	$enrolled_users = array();
	foreach ($all_users as $u) {
		if (isset($_POST['user_' . $u->uid])) {
			$enrolled_users[] = $u->uid;
		}
	}
	
	data_enroll_users($cid, $enrolled_users);
	flash('info', 'Users succesfully enrolled.');
	redirect_to('admin', 'enroll');
}