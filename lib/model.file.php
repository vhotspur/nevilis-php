<?php

function data_get_file_details($filename, $user, $assignment) {
	return db_find_object("get uploaded file details",
		"SELECT
			sfid,
			extension,
			mime,
			assignmentfile.filename filename
		FROM
			assignmentfile
			JOIN submittedfile ON assignmentfile.afid=submittedfile.file
		WHERE
			assignmentfile.filename || submittedfile.extension = :filename
			AND submittedfile.user = :user
			AND assignmentfile.assignment = :assignment
		", array(
				"assignment" => $assignment,
				"filename" => $filename,
				"user" => $user
		));
}

function data_add_solution_file($file_id, $uploader) {
	db_create_object_from_array("new file uploaded",
		array("user" => $uploader, "file" => $file_id),
		"submittedfile");
}

function data_update_solution_file_details($file_id, $uploader, $extension,
		$mime, $update_timestamp = true) {
	$data = array(
		"user" => $uploader,
		"file" => $file_id,
		"extension" => $extension,
		"mime" => $mime
	);
	if ($update_timestamp) {
		$data["upload_date"] = date('Y-m-d H:i:s');
	}
	
	db_update_object_from_array("update solution file details",
		$data, 'submittedfile', array("user", "file"));
}

