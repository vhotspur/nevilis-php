<?php
require_once 'lib/limonade.php';
require_once 'lib/db.php';
require_once 'lib/auth.php';

session_start();
assert_options(ASSERT_ACTIVE, 1);

function before_exit($exiting) {
	if (! $exiting) {
		// Probably redirecting
		if (option('debug')) {
			$_SESSION['PREV_SQL_QUERIES'] = @v($GLOBALS['SQL_QUERIES'], null);
		}
	}
}

function before($route) {
	$handler = $route["callback"];
	
	$auth_check_skip_pages = array("page_auth_login", "page_auth_do_login", "page_auth_logout");
	if (array_search($handler, $auth_check_skip_pages) !== false) {
		return;
	}	
	
	$ok = auth_check_user_and_continue_session();
	if (!$ok) {
		flash('error', 'Please, log in again.');
		redirect('/');
	}
}

function configure() {
	$db = new PDO('sqlite:db/dev.db');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	option('db_conn', $db);
	option('file_dir', 'dev_files/');
}

function not_found($errno, $errstr, $errfile = null, $errline = null) {
	set('errno', $errno);
	set('errstr', $errstr);
	set('errfile', $errfile);
	set('errline', $errline);
	return html('404.html.php');
}

layout('layout/default.html.php');

if (auth_is_user_logged_in()) {
	dispatch('/', 'page_main');
	
	dispatch('/logout', 'page_auth_logout');
	
	dispatch('/change-password', 'page_auth_change_password');
	dispatch_post('/change-password', 'page_auth_do_change_password');

	dispatch('/admin', 'page_admin_main');
	dispatch('/admin/users', 'page_admin_user_list');
	dispatch('/admin/users/add', 'page_admin_user_add');
	dispatch_post('/admin/users/add', 'page_admin_user_create');
	dispatch('/admin/users/edit/:uid', 'page_admin_user_edit');
	dispatch_post('/admin/users/edit/:uid', 'page_admin_user_update');
	
	dispatch('/:course', 'page_course_main');
	dispatch('/:course/:assignment', 'page_assignment_main');
	dispatch_post('/:course/:assignment/upload', 'page_assignment_do_upload');
	dispatch('/:course/:assignment/:filename', 'page_file_download');	
} else {
	dispatch_post('/login', 'page_auth_do_login');
	dispatch_post('/*', 'page_auth_login');
	dispatch('/*', 'page_auth_login');
}

run();
