<?php

function page_admin_assignement_list() {
	set('title', _('Assignment administration'));
	set('assignments', data_get_assignment_list());
	return html('admin/assignment_list.html.php');
}

function page_admin_assignment_edit_form($id, $info, $new_file_count) {
	if (!isset($info->files)) {
		$info->files = array();
	}
	set('aid', $id);
	set('name', $info->name);
	set('description', $info->description);
	
	$new_file = array(
		"afid" => 0,
		"name" => "",
		"filename" => "",
		"validation" => array(),
		"maxsize" => "",
		"description" => ""
	);
	for ($i = 0; $i < $new_file_count; $i++) {
		$info->files[] = make_object($new_file);
	}
	
	set('assignment_files', $info->files);
}

function page_admin_assignement_add() {
	set('title', _('Add assignment'));
	
	page_admin_assignment_edit_form(null, make_object(array(
		'name' => '',
		'description' => ''
	)), 3);
	
	return html('admin/assignment_edit.html.php');
}

function page_admin_assignment_create() {
	$aid = v($_POST['aid'], '');
	$name = v($_POST['name'], '');
	$description = v($_POST['description'], '');
	
	$okay = ($aid != "") && ($name != "");
	
	if (!$okay) {
		flash('error', _('You need to fill-in all the values.'));
		redirect_to('admin', 'assignments', 'add');
	}
	
	$files = array();
	$file_index = 0;
	$file_validation_ids = file_validation_get_ids();
	while (isset($_POST[sprintf('file_%d_id', $file_index)])) {
		$prefix = 'file_' . $file_index . '_';
		$file_index++;
		$file_validation = array();
		foreach ($file_validation_ids as $i) {
			if (isset($_POST[$prefix . "validator_" . $i])) {
				$file_validation[] = $i;
			}
		}
		$file_info = array(
			"id" => $_POST[$prefix . "id"],
			"name" => v($_POST[$prefix . "name"], ''),
			"filename" => v($_POST[$prefix . "filename"], ''),
			"description" => v($_POST[$prefix . "description"], ''),
			"maxsize" => v($_POST[$prefix . "maxsize"], '42'),
			"validation" => $file_validation,
		);
		if (($file_info["name"] == "") || ($file_info["filename"] == "")) {
			continue;
		}
		if ($file_info["id"] != 0) {
			continue;
		}
		$files[] = $file_info;
	}
	
	data_create_assignment($aid, $name, $files, $description);
	
	flash('info', _('Assignment succesfully created.'));
	redirect('/admin/assignments');
}

function page_admin_assignment_edit() {
	$aid = params('aid');
	
	$info = data_get_assignment_details($aid);
	if ($info == null) {
		flash('error', _('Assignment not found.'));
		redirect('/admin/assignments');
	}
	
	set('title', sprintf(_('Edit assignment %s'), $info->name));
	page_admin_assignment_edit_form($aid, $info, 2);
	
	return html('admin/assignment_edit.html.php');
}

function page_admin_assignment_update() {
	$aid = params('aid');
	$name = v($_POST['name'], '');
	$description = v($_POST['description'], '');

	$okay = ($aid != "") && ($name != "");

	if (!$okay) {
		flash('error', _('You need to fill-in all the values.'));
		redirect_to('admin', 'assignments');
	}

	$files = array();
	$file_index = 0;
	$file_validation_ids = file_validation_get_ids();
	while (isset($_POST[sprintf('file_%d_id', $file_index)])) {
		$prefix = 'file_' . $file_index . '_';
		$file_index++;
		$file_validation = array();
		foreach ($file_validation_ids as $i) {
			if (isset($_POST[$prefix . "validator_" . $i])) {
				$file_validation[] = $i;
			}
		}
		$file_info = array(
			"id" => $_POST[$prefix . "id"],
			"name" => v($_POST[$prefix . "name"], ''),
			"filename" => v($_POST[$prefix . "filename"], ''),
			"description" => v($_POST[$prefix . "description"], ''),
			"maxsize" => v($_POST[$prefix . "maxsize"], '42'),
			"validation" => $file_validation,
		);
		// TODO: file removal
		if (($file_info["name"] == "") || ($file_info["filename"] == "")) {
			continue;
		}
		$files[] = $file_info;
	}

	data_update_assignment($aid, $name, $files, $description);

	flash('info', _('Assignment succesfully updated.'));
	redirect('/admin/assignments');
}

