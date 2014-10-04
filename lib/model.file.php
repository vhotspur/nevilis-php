<?php

function data_get_file_id($filename, $user, $assignment) {
	$info = db_find_object("get uploaded file id",
		"SELECT
			sfid
		FROM
			assignmentfile
			JOIN submittedfile ON assignmentfile.afid=submittedfile.file
		WHERE
			assignmentfile.filename = :filename
			AND submittedfile.user = :user
			AND assignmentfile.assignment = :assignment
		", array(
			"assignment" => $assignment,
			"filename" => $filename,
			"user" => $user
		));
	
	
	if ($info != null) {
		return $info->sfid;
	} else {
		return 0;
	}
}

function data_add_solution_file($file_id, $uploader) {
	db_create_object_from_array("new file uploaded",
		array("user" => $uploader, "file" => $file_id),
		"submittedfile");
}

function data_update_solution_file_timestamp($file_id, $uploader) {
	db_update_object_from_array("update file timestamp",
		array("user" => $uploader, "file" => $file_id,
			"upload_date" => date('Y-m-d H:i:s')),
		"submittedfile", array("user", "file"));
}
