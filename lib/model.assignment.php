<?php

function data_get_assignment_list() {
	return db_find_objects("list of all assignments",
		"SELECT
			aid,
			name,
			description
		FROM
			assignment
		ORDER BY
			name ASC
		");
}

function data_get_active_assignments_for_user($user) {
	return db_find_objects("list of active assignments",
		"SELECT
			aid,
			assignment.name AS assignmentname,
			cid,
			course.name AS coursename,
			assignment.description AS description,
			courseassignment.deadline AS deadline,
			(strftime('%s', deadline) - strftime('%s', 'now')) / 3600 AS remaininghours
		FROM
			assignment
			JOIN courseassignment ON aid=courseassignment.assignment
			JOIN course ON cid=courseassignment.course
			JOIN courseusers ON cid=courseusers.course
		WHERE
			datetime(deadline) > datetime('now')
			AND courseusers.user = :user
		ORDER BY
			deadline ASC,
			assignment.name ASC
		", array("user" => $user));
}

function data_get_assignments_for_course($course) {
	return db_find_objects("active assignments in a course",
		"SELECT
			aid,
			name,
			deadline,
			deadline_noupload,
			description
		FROM
			assignment
			JOIN courseassignment
		WHERE
			assignment.aid = courseassignment.assignment
			AND course = :course
		ORDER BY
			courseassignment.deadline
		", array("course" => $course));
}

function data_get_assigments_and_grades_for_course($user, $course) {
	assert(data_is_user_enrolled_to_course($user, $course));
	
	$assignments = db_find_objects("list of assignments in given course",
		"SELECT
			aid,
			assignment.name AS name,
			assignment.description AS description,
			courseassignment.deadline AS deadline
		FROM
			assignment
			JOIN courseassignment ON aid=courseassignment.assignment
		WHERE
			course=:course
		ORDER BY
			courseassignment.deadline,
			assignment.name
		", array("course" => $course));

	if ($assignments == null) {
		return null;
	}
	
	$grades = db_find_objects("user grades for given course",
		"SELECT
			grade.assignment AS aid,
			grade.grade AS grade,
			comment
		FROM
			grade
			JOIN courseassignment ON courseassignment.assignment=grade.assignment
		WHERE
			courseassignment.course=:course
			AND grade.user=:user
		", array("course" => $course, "user" => $user));
	
	foreach ($assignments as $a) {
		$a->grade = null;
		$a->comment = null;
		foreach ($grades as $g) {
			if ($a->aid == $g->aid) {
				$a->grade = $g->grade;
				$a->comment = $g->comment;
				break;
			}
		}
	}
	
	return $assignments;
}

function data_get_assignment_files($assignment) {
	$files = db_find_objects("get assignment files",
		"SELECT
			afid,
			name,
			filename,
			validation,
			maxsize,
			description
		FROM
			assignmentfile
		WHERE
			assignment = :assignment
		ORDER BY
			name
		", array("assignment" => $assignment));
	foreach ($files as &$f) {
		$f->validation = explode(",", $f->validation);
	}
	return $files;
}

function data_get_assignment_details($assignment) {
	$info = db_find_object("get assignment details",
		"SELECT
			assignment.name AS name,
			assignment.description AS description
		FROM
			assignment
		WHERE
			aid = :assignment
		", array("assignment" => $assignment));
	
	if ($info == null) {
		return null;
	}
	
	$info->files = data_get_assignment_files($assignment);
	
	return $info;
}

function data_get_assignment_details_for_user_in_course($assignment, $course, $user) {
	$info = data_get_assignment_details_for_user($assignment, $user);
	
	if ($info == null) {
		return null;
	}
	
	$deadlines = db_find_object("get assignment deadlines",
		"SELECT
			deadline,
			deadline_noupload
		FROM
			courseassignment
		WHERE
			assignment = :assignment
			AND course = :course
		", array("assignment" => $assignment, "course" => $course));
	
	if ($deadlines == null) {
		return null;
	}
	
	$info->deadline = $deadlines->deadline;
	$info->deadline_noupload = $deadlines->deadline_noupload;
	
	return $info;
}

function data_get_assignment_details_for_user($assignment, $user) {
	$info = data_get_assignment_details($assignment);
	
	if ($info == null) {
		return null;
	}
	
	$submitted = db_find_objects("get actually submitted files",
		"SELECT
			sfid,
			upload_date,
			extension,
			afid
		FROM
			submittedfile
			JOIN assignmentfile ON assignmentfile.afid=submittedfile.file
		WHERE
			assignmentfile.assignment = :assignment
			AND submittedfile.user = :user",
		array("assignment" => $assignment, "user" => $user)
	);
	
	foreach ($info->files as $f) {
		$f->submitted = false;
		foreach ($submitted as $s) {
			if ($s->afid == $f->afid) {
				$f->submitted = true;
				$f->submitted_id = $s->sfid;
				$f->upload_date = $s->upload_date;
				$f->full_filename = $f->filename . $s->extension;
				break;
			}
		}
	}
	
	$info->grade = db_find_object("get grade of the assignment",
		"SELECT
			grade,
			locked,
			comment
		FROM
			grade
		WHERE
			user = :user
			AND assignment = :assignment",
		array("assignment" => $assignment, "user" => $user)
	);
	
	if ($info->grade == null) {
		$info->locked = false;
	} else {
		$info->locked = $info->grade->locked > 0;
	}
	
	$usercomment = db_find_object("get user comment",
		"SELECT
			comment
		FROM
			assignmentcomment
		WHERE
			user = :user
			AND assignment = :assignment",
		array("assignment" => $assignment, "user" => $user)
	);
	if ($usercomment == null) {
		$info->usercomment = "";
	} else {
		$info->usercomment = $usercomment->comment;
	}
	
	return $info;
}

function data_create_assignment($id, $name, $files, $description) {
	$assignment = array(
		"aid" => $id,
		"name" => $name,
		"description" => $description
	);
	db_create_object_from_array("create new assignment", $assignment, 'assignment');
	
	foreach ($files as $f) {
		$file = array(
			"filename" => $f["filename"],
			"assignment" => $id,
			"name" => $f["name"],
			"description" => $f["description"],
			"maxsize" => $f["maxsize"],
			"validation" => implode(',', $f["validation"]),
		);
		if ($f["id"] == 0) {
			db_create_object_from_array("create file", $file, 'assignmentfile');	
		} else {
			$file["afid"] = $f["id"];
			db_update_object_from_array("update file", $file, 'assignmentfile', 'afid');
		}
	}
}

function data_update_assignment($id, $name, $files, $description) {
	$assignment = array(
			"aid" => $id,
			"name" => $name,
			"description" => $description
	);
	db_update_object_from_array("update assignment", $assignment, "assignment", "aid");
	
	foreach ($files as $f) {
		$file = array(
				"filename" => $f["filename"],
				"assignment" => $id,
				"name" => $f["name"],
				"description" => $f["description"],
				"maxsize" => $f["maxsize"],
				"validation" => implode(',', $f["validation"]),
		);
		if ($f["id"] == 0) {
			db_create_object_from_array("create file", $file, 'assignmentfile');
		} else {
			$file["afid"] = $f["id"];
			db_update_object_from_array("update file", $file, 'assignmentfile', 'afid');
		}
	}
}

function data_update_grades($grades) {
	foreach ($grades as $grade) {
		db_delete_objects("delete previous grade", 'grade', array(
			"user" => $grade["user"],
			"assignment" => $grade["assignment"]));
		
		$empty = !$grade["locked"]
			&& ($grade["comment"] == "")
			&& ($grade["grade"] == "");
		
		if ($empty) {
			continue;
		}
		
		db_create_object_from_array("add a grade", $grade, 'grade');
	}
}

function data_update_user_comment_for_assignment($user, $assignment, $comment) {
	db_delete_objects("delete previous user comment", 'assignmentcomment',
		array(
			"user" => $user,
			"assignment" => $assignment
	));
	if ($comment != "") {
		db_create_object_from_array("insert new comment",
			array(
				"user" => $user,
				"assignment" => $assignment,
				"comment" => $comment
		), 'assignmentcomment');
	}
}
