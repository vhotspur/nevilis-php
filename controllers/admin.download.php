<?php

// FIXME: check that the assignment/user belongs to the actual course
// (though that is not critical)


function page_admin_download_main() {
	set('title', _('Download solutions'));
	set('courses', data_get_course_list());
	return html('admin/download_main.html.php');
}

function page_admin_download_from_course() {
	$cid = params('cid');
	$info = data_get_course_details($cid);
	if ($info == null) {
		flash('error', _('Course not found'));
		redirect_to('admin', 'download');
	}
	
	$assignments = data_get_assignments_for_course($cid);
	foreach ($assignments as &$a) {
		$a->files = data_get_assignment_files($a->aid);
	}
	unset($a);
	$users = data_get_enrolled_users($cid);
	
	foreach ($users as &$u) {
		$u->assignments = array();
		foreach ($assignments as $a) {
			$u->assignments[$a->aid] = make_object(array(
				"uploaded" => true
			));
		}
	}
	
	set('cid', $cid);
	set('title', sprintf(_('Download solutions for %s (%s)'), $info->name, $info->adminname));
	set('assignments', $assignments);
	set('users', $users);
	
	return html('admin/download_whole_course.html.php');
}

function make_zip_on_the_fly_as_attachment($filename) {
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename="' . $filename . '.zip"');
	header('Content-Transfer-Encoding: binary');
	
	$zip = new ZipFile();
	$zip->setDoWrite();
	
	return $zip;
}

function add_solution_file_to_zip($zip, $dir, $uid, $aid, $filename) {
	$path = sprintf('%s/%s/%s/%s', option('file_dir'), $uid, $aid, $filename);
	$contents = file_get_contents($path);
	if ($contents === false) {
		return;
	}
	$zip->addFile($contents, $dir . "/" . $filename);
}

function page_admin_download_single_solution() {
	$cid = params('cid');
	$aid = params('aid');
	$uid = params('uid');
	$info = data_get_course_details($cid);
	if ($info == null) {
		flash('error', _('Course not found'));
		redirect_to('admin', 'download');
	}
	
	$details = data_get_assignment_details_for_user($aid, $uid);
		
	$base_name = sprintf("%s-%s-%s", $cid, $aid, $uid);
	
	$zip = make_zip_on_the_fly_as_attachment($base_name);
	foreach ($details->files as $f) {
		if ($f->submitted) {
			add_solution_file_to_zip($zip, $base_name, $uid, $aid, $f->full_filename);
		}
	}
	$zip->file();
	
	exit;
}

function page_admin_download_all_user_solutions() {
	$cid = params('cid');
	$uid = params('uid');
	$info = data_get_course_details($cid);
	if ($info == null) {
		flash('error', _('Course not found'));
		redirect_to('admin', 'download');
	}
	
	$assignments = data_get_assignments_for_course($cid);
	
	$base_name = sprintf("%s-%s", $cid, $uid);
	$zip = make_zip_on_the_fly_as_attachment($base_name);
	
	foreach ($assignments as $a) {
		$details = data_get_assignment_details_for_user($a->aid, $uid);
		$dir = $base_name . "/" . $a->aid;
		foreach ($details->files as $f) {
			if ($f->submitted) {
				add_solution_file_to_zip($zip, $dir, $uid, $a->aid, $f->full_filename);
			}
		}
	}
	$zip->file();
	
	exit;
}

function page_admin_download_all_assignment_solutions() {
	$cid = params('cid');
	$aid = params('aid');
	$info = data_get_course_details($cid);
	if ($info == null) {
		flash('error', _('Course not found'));
		redirect_to('admin', 'download');
	}
	
	$base_name = sprintf("%s-%s", $cid, $aid);
	$zip = make_zip_on_the_fly_as_attachment($base_name);
	
	$users = data_get_enrolled_users($cid);
	
	foreach ($users as $u) {
		$details = data_get_assignment_details_for_user($aid, $u->uid);
		$dir = $base_name . "/" . $u->uid;
		foreach ($details->files as $f) {
			if ($f->submitted) {
				add_solution_file_to_zip($zip, $dir, $u->uid, $aid, $f->full_filename);
			}
		}
	}
	$zip->file();
	
	exit;
}

function page_admin_download_all_solutions() {
	$cid = params('cid');
	$info = data_get_course_details($cid);
	if ($info == null) {
		flash('error', _('Course not found'));
		redirect_to('admin', 'download');
	}

	$assignments = data_get_assignments_for_course($cid);
	$users = data_get_enrolled_users($cid);

	$base_name =  $cid;
	$zip = make_zip_on_the_fly_as_attachment($base_name);

	foreach ($assignments as $a) {
		foreach ($users as $u) {
			$details = data_get_assignment_details_for_user($a->aid, $u->uid);
			$dir = $base_name . "/" . $a->aid . "/" . $u->uid;
			foreach ($details->files as $f) {
				if ($f->submitted) {
					add_solution_file_to_zip($zip, $dir, $u->uid, $a->aid, $f->full_filename);
				}
			}
		}
	}
	$zip->file();

	exit;
}
