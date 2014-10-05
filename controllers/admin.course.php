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
	
	return html('admin/course_edit.html.php');
}

function page_admin_course_create() {
	$cid = v($_POST['cid'], '');
	$name = v($_POST['name'], '');
	
	$okay = ($cid != "") && ($name != "");
	
	if (!$okay) {
		flash('error', _('You need to fill-in all the values.'));
		redirect_to('admin', 'courses');
	}
	
	data_create_course($cid, $name);
	flash('info', _('Course succesfully created.'));
	redirect_to('admin', 'courses');
}

function page_admin_course_update() {
	$cid = params('cid');
	$name = v($_POST['name'], '');
	
	$okay = ($cid != "") && ($name != "");
	
	if (!$okay) {
		flash('error', _('You need to fill-in all the values.'));
		redirect_to('admin', 'courses');
	}
	
	data_update_course($cid, $name);
	flash('info', _('Course succesfully updated.'));
	redirect_to('admin', 'courses');
}
