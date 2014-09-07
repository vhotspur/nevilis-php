<?php

function page_admin_user_list() {
	set('title', 'User administration');
	set('users', data_get_user_list());
	return html('admin/user_list.html.php');
}

function page_admin_user_add() {
	set('title', 'Add user');
	set('uid', null);
	set('name', '');
	
	return html('admin/user_edit.html.php');
}

function page_admin_user_edit() {
	set('title', 'Edit existing user');
	
	$uid = params('uid');
	$info = data_get_user_details($uid);
	if ($info == null) {
		flash('error', 'User not found');
		redirect('/admin/users');
	}
	
	set('uid', $uid);
	set('name',$info->name);
	
	return html('admin/user_edit.html.php');
}

function page_admin_user_create() {
	$uid = v($_POST['uid'], '');
	$name = v($_POST['name'], '');
	
	$okay = ($uid != "") && ($name != "");
	
	if (!$okay) {
		flash('error', 'You need to fill-in all the values.');
		redirect('/admin/users/add');
	}
	
	data_create_user($uid, $name);
	flash('info', 'User succesfully created.');
	redirect('/admin/users');
}

function page_admin_user_update() {
	$uid = params('uid');
	$name = v($_POST['name'], '');
	
	$okay = ($uid != "") && ($name != "");
	
	if (!$okay) {
		flash('error', 'You need to fill-in all the values.');
		redirect('/admin/users/add');
	}
	
	data_update_user($uid, $name);
	flash('info', 'User succesfully updated.');
	redirect('/admin/users');
}
