<?php

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
		", array("course" => $course));
	
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

function data_get_assignment_details_for_user($assignment, $user) {
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
	
	$files = db_find_objects("get assignment files",
		"SELECT
			afid,
			name,
			filename,
			description
		FROM
			assignmentfile
		WHERE
			assignment = :assignment
		", array("assignment" => $assignment));
	
	$info->files = $files;
	
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

