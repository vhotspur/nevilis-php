<?php

function page_auth_login() {
	set('title', 'Log in');
	return html('login.html.php');
}

function page_auth_do_login() {
	$user = $_POST['user'];
	$password = $_POST['password'];
	
	$ok = auth_check_user_and_start_session($user, $password);
	
	if ($ok) {
		flash('info', 'You have been logged in.');
		redirect('/');
	} else {
		flash('error', 'Invalid username/password.');
		redirect('/login');
	}
}

function page_auth_logout() {
	auth_close_session();
	
	flash('info', 'You have been logged out.');
	
	redirect('/');
}
