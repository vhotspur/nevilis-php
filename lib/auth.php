<?php

function auth_is_user_logged_in() {
    return isset($_SESSION['user']);
}

function auth_get_current_user() {
	assert(auth_is_user_logged_in());
	
	return $_SESSION['user'];
}

function auth_has_role($role, $rolelist) {
	$role = ',' . $role . ',';
	$rolelist = ',' . $rolelist . ',';
	return strstr($rolelist, $role) !== false;
}

function auth_is_user_admin() {
	assert(auth_is_user_logged_in());
		
	return auth_has_role("admin", $_SESSION['user_roles']);
}

function auth_check_user_and_start_session($user, $password) {
	$result = db_find_object('check user password',
		'SELECT password, name, roles FROM user WHERE uid=:uid',
		array("uid" => $user));
	
	if ($result != null) {
		if (password_verify($password, $result->password)) {
			$_SESSION['user'] = $user;
			$_SESSION['user_roles'] = $result->roles;
			
			return true;
		}
	}
	
	auth_close_session();
	
	return false;
}

function auth_check_user_and_continue_session() {
	assert(auth_is_user_logged_in());
	
	$result = db_find_object('check user exists, read his role',
		'SELECT name, roles FROM user WHERE uid=:uid',
		array("uid" => $_SESSION['user']));
	
	if ($result != null) {
		$_SESSION['user_roles'] = $result->roles;
			
		return true;
	}
	
	auth_close_session();
	
	return false;
}

function auth_close_session() {
	unset($_SESSION['user']);
	unset($_SESSION['user_roles']);
}
