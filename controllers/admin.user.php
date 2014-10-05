<?php

function page_admin_user_list() {
	set('title', _('User administration'));
	set('users', data_get_user_list());
	return html('admin/user_list.html.php');
}

function page_admin_user_add() {
	set('title', _('Add user'));
	set('uid', null);
	set('name', '');
	
	return html('admin/user_edit.html.php');
}

function page_admin_user_edit() {
	set('title', _('Edit existing user'));
	
	$uid = params('uid');
	$info = data_get_user_details($uid);
	if ($info == null) {
		flash('error', _('User not found'));
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
		flash('error', _('You need to fill-in all the values.'));
		redirect('/admin/users/add');
	}
	
	data_create_user($uid, $name);
	flash('info', _('User succesfully created.'));
	redirect('/admin/users');
}

function page_admin_user_update() {
	$uid = params('uid');
	$name = v($_POST['name'], '');
	
	$okay = ($uid != "") && ($name != "");
	
	if (!$okay) {
		flash('error', _('You need to fill-in all the values.'));
		redirect('/admin/users/add');
	}
	
	data_update_user($uid, $name);
	flash('info', _('User succesfully updated.'));
	redirect('/admin/users');
}

function page_admin_user_reset_password_select() {
	$all_users = data_get_user_list();
	
	set('title', _('Password reset'));
	set('users', $all_users);

	return html('admin/user_reset_password.html.php');
}

function page_admin_user_reset_password() {
	$all_users = data_get_user_list();
	
	$reset_password_for = array();
	foreach ($all_users as $u) {
		if (isset($_POST['user_' . $u->uid])) {
			$password = auth_generate_password();
			$reset_password_for[] = array(
				"uid" => $u->uid,
				"name" => $u->name,
				"password" => $password
			);
			auth_reset_user_password($u->uid, $password);
		}
	}
	
	if (count($reset_password_for) > 0) {
		flash('reset_passwords', $reset_password_for);
		flash('info', _('Passwords were reset.'));
	}
	redirect_to('admin', 'users');
}
