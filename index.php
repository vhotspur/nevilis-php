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
		flash('error', _('Please, log in again.'));
		redirect('/');
	}
	
	set('glob_user_courses', data_get_course_list_for_user(auth_get_current_user()));
}

function configure() {
	$db = new PDO('sqlite:db/dev.db');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	option('db_conn', $db);
	option('file_dir', 'dev_files/');
	// TODO: per user selection
	option('l10n', 'cs_CZ.utf8');
	
	$lang = option('l10n');
	putenv("LC_ALL=$lang");
	setlocale(LC_ALL, $lang);
	bindtextdomain('nevilis', './locale/');
	bind_textdomain_codeset('nevilis', 'UTF-8');
	textdomain('nevilis');
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
	dispatch('/admin/users/reset-password', 'page_admin_user_reset_password_select');
	dispatch_post('/admin/users/reset-password', 'page_admin_user_reset_password');
	dispatch('/admin/users/add', 'page_admin_user_add');
	dispatch_post('/admin/users/add', 'page_admin_user_create');
	dispatch('/admin/users/edit/:uid', 'page_admin_user_edit');
	dispatch_post('/admin/users/edit/:uid', 'page_admin_user_update');
	
	dispatch('/admin/courses', 'page_admin_course_list');
	dispatch('/admin/courses/add', 'page_admin_course_add');
	dispatch_post('/admin/courses/add', 'page_admin_course_create');
	dispatch('/admin/courses/edit/:cid', 'page_admin_course_edit');
	dispatch_post('/admin/courses/edit/:cid', 'page_admin_course_update');
	
	dispatch('/admin/enroll', 'page_admin_enroll_main');
	dispatch('/admin/enroll/:cid', 'page_admin_enroll_edit');
	dispatch_post('/admin/enroll/:cid', 'page_admin_enroll_to_course');
	
	dispatch('/admin/assignments', 'page_admin_assignement_list');
	dispatch('/admin/assignments/add', 'page_admin_assignement_add');
	dispatch_post('/admin/assignments/add', 'page_admin_assignment_create');
	dispatch('/admin/assignments/edit/:aid', 'page_admin_assignment_edit');
	dispatch_post('/admin/assignments/edit/:aid', 'page_admin_assignment_update');
	
	dispatch('/admin/assign', 'page_admin_assign_main');
	dispatch('/admin/assign/:cid', 'page_admin_assign_edit');
	dispatch_post('/admin/assign/:cid', 'page_admin_assign_to_course');
	
	dispatch('/admin/grade', 'page_admin_grade_main');
	dispatch('/admin/grade/:cid/print', 'page_admin_printable_grades_whole_course');
	dispatch('/admin/grade/:cid', 'page_admin_grade_edit_whole_course');
	dispatch_post('/admin/grade/:cid', 'page_admin_grade_whole_course');
	
	dispatch('/admin/download', 'page_admin_download_main');
	dispatch('/admin/download/:cid', 'page_admin_download_from_course');
	dispatch('/admin/download/:cid/single/:uid/:aid', 'page_admin_download_single_solution');
	dispatch('/admin/download/:cid/user/:uid', 'page_admin_download_all_user_solutions');
	dispatch('/admin/download/:cid/assignment/:aid', 'page_admin_download_all_assignment_solutions');
	dispatch('/admin/download/:cid/all', 'page_admin_download_all_solutions');
	
	dispatch('/:course', 'page_course_main');
	dispatch('/:course/:assignment', 'page_assignment_main');
	dispatch_post('/:course/:assignment/upload', 'page_assignment_do_upload');
	dispatch('/:course/:assignment/:filename', 'page_file_download');	
} else {
	dispatch_post('/login', 'page_auth_do_login');
	dispatch_post('/**', 'page_auth_login');
	dispatch('/**', 'page_auth_login');
}

run();
