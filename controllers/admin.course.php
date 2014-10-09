<?php

function page_admin_course_list() {
	set('title', _('Course administration'));
	set('courses', data_get_course_list());
	return html('admin/course_list.html.php');
}

function page_admin_course_add() {
	set('title', _('Add course'));
	set('cid', null);
	set('name', '');
	set('adminname', '');
	
	return html('admin/course_edit.html.php');
}

function page_admin_course_edit() {
	set('title', _('Edit existing course'));
	
	$cid = params('cid');
	$info = data_get_course_details($cid);
	if ($info == null) {
		flash('error', _('Course not found'));
		redirect_to('admin', 'courses');
	}
	
	set('cid', $cid);
	set('name',$info->name);
	set('adminname', $info->adminname);
	
	return html('admin/course_edit.html.php');
}

function page_admin_course_create() {
	$cid = v_post('cid', '');
	$details = array(
		"name" => v_post('name', ''),
		"adminname" => v_post('adminname', '')
	);
	
	$okay = ($cid != "") && ($details['name'] != "");
	
	if (!$okay) {
		flash('error', _('You need to fill-in all the values.'));
		redirect_to('admin', 'courses');
	}
	
	data_create_course($cid, $details);
	flash('info', _('Course succesfully created.'));
	redirect_to('admin', 'courses');
}

function page_admin_course_update() {
	$cid = params('cid');
	$details = array(
		"name" => v_post('name', ''),
		"adminname" => v_post('adminname', '')
	);
	
	$okay = ($cid != "") && ($details['name'] != "");
	
	if (!$okay) {
		flash('error', _('You need to fill-in all the values.'));
		redirect_to('admin', 'courses');
	}
	
	data_update_course($cid, $details);
	flash('info', _('Course succesfully updated.'));
	redirect_to('admin', 'courses');
}
