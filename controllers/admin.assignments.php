<?php

function page_admin_assignement_list() {
	set('title', 'Assignment administration');
	set('assignments', data_get_assignment_list());
	return html('admin/assignment_list.html.php');
}

function page_admin_assignement_add() {
	set('title', 'Add assignment');
	set('aid', null);
	set('name', '');
	set('description', '');

	return html('admin/assignment_edit.html.php');
}

function page_admin_assignment_create() {
	$aid = v($_POST['aid'], '');
	$name = v($_POST['name'], '');
	$description = v($_POST['description'], '');
	
	$okay = ($aid != "") && ($name != "");
	
	if (!$okay) {
		flash('error', 'You need to fill-in all the values.');
		redirect_to('admin', 'assignments', 'add');
	}
	
	$files = array();
	$file_index = 0;
	while (isset($_POST[sprintf('file_%d_id', $file_index)])) {
		$prefix = 'file_' . $file_index . '_';
		$file_index++;
		$file_info = array(
			"id" => $_POST[$prefix . "id"],
			"name" => v($_POST[$prefix . "name"], ''),
			"filename" => v($_POST[$prefix . "filename"], ''),
			"description" => v($_POST[$prefix . "description"], ''),
			"maxsize" => v($_POST[$prefix . "maxsize"], '42'),
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
	
	flash('info', 'Assignment succesfully created.');
	redirect('/admin/assignments');
}

