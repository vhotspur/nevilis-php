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

