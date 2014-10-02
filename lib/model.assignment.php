<?php

function data_get_assignment_list() {
	return db_find_objects("list of all assignments",
		"SELECT
			aid,
			name,
			description
		FROM
			assignment
		");
}

function data_get_assignments_for_course($course) {
	return db_find_objects("active assignments in a course",
		"SELECT
			aid,
			name,
			description
		FROM
			assignment
			JOIN courseassignment
		WHERE
			assignment.aid = courseassignment.assignment
			AND course = :course
		", array("course" => $course));
}

function data_get_assigments_and_grades_for_course($user, $course) {
	assert(data_is_user_enrolled_to_course($user, $course));
	
	$assignments = db_find_objects("list of assignments in given course",
		"SELECT
			aid,
			assignment.name AS name,
			assignment.description AS description,
			COUNT(afid) AS filecount
		FROM
			assignment
			JOIN courseassignment ON aid=courseassignment.assignment,
			assignmentfile
		WHERE
			course=:course
			AND assignmentfile.assignment=aid
		GROUP BY
			aid
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

function data_get_assignment_details_for_user($assignment, $user) {
	$info = data_get_assignment_details($assignment);
	
	if ($info == null) {
		return null;
	}
	
	$submitted = db_find_objects("get actually submitted files",
		"SELECT
			sfid,
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
