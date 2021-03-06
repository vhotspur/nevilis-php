<?php

function page_auth_login() {
	set('title', _('Assignment submission system'));
	return html('login.html.php');
}

function page_auth_do_login() {
	$user = $_POST['user'];
	$password = $_POST['password'];
	
	$ok = auth_check_user_and_start_session($user, $password);
	
	if ($ok) {
		redirect('/');
	} else {
		flash('error', _('Invalid username/password.'));
		redirect('/login');
	}
}

function page_auth_logout() {
	auth_close_session();
	
	redirect('/');
}

function page_auth_change_password() {
	set('title', _('Password change'));
	return html('password.html.php');
}

function page_auth_do_change_password() {
	$old = v_post('password0', '');
	$new1 = v_post('password1', '');
	$new2 = v_post('password2', '');
	
	$error = "";
	if (strlen($new1) < 3) {
		flash('error', _('Password too short.'));
		redirect('/change-password');
	}
	if ($new1 != $new2) {
		flash('error', _('Passwords do not match.'));
		redirect('/change-password');
	}
	
	$ok = auth_change_user_password(auth_get_current_user(), $old, $new1);
	if ($ok) {
		flash('info', _('Password updated.'));
		redirect('/');
	} else {
		flash('error', _('Passwords do not match.'));
		redirect('/change-password');
	}
}
