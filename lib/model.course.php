<?php

function data_get_course_list() {
	return db_find_objects("all existing courses",
		"SELECT
    			cid,
    			name
    		FROM
    			course");
}

function data_get_course_list_for_user($user) {
    return db_find_objects("courses user is enrolled to",
    	"SELECT
    		cid,
    		name
    	FROM
    		course
    		JOIN courseusers on course.cid=courseusers.course
    	WHERE
    		user=:user
    	", array("user" => $user));
}

function data_get_course_name_by_id($id) {
	$course = data_get_course_details($id);
	if ($course == null) {
		return null;
	} else {
		return $course->name;
	}
}

function data_get_course_details($id) {
	return db_find_object("get course name by its id",
		"SELECT
			cid,
			name
		FROM
			course
		WHERE
			cid=:id
		", array("id" => $id));
}

function data_get_enrolled_users_uids_only($course) {
	return db_find_objects("check user is enrolled to a course",
		"SELECT
			user AS uid
		FROM
			courseusers
		WHERE
			course = :course
		", array("course" => $course));
}

function data_get_enrolled_users($course) {
	return db_find_objects("check user is enrolled to a course",
		"SELECT
			user.uid AS uid,
			user.name AS name
		FROM
			courseusers
			JOIN user
		WHERE
			user.uid = courseusers.user
			AND course = :course
		", array("course" => $course));
}

function data_is_user_enrolled_to_course($user, $course) {
	$obj = db_find_objects("check user is enrolled to a course",
		"SELECT
			user, course
		FROM
			courseusers
		WHERE
			user = :user
			AND course = :course
		", array(
			"user" => $user,
			"course" => $course
	));
	
	return count($obj) == 1;
}

function data_enroll_users($course, $users) {
	db_delete_objects("destroy existing enrollment", "courseusers",
		array("course" => $course));
	foreach ($users as $u) {
		$obj = array(
			"user" => $u,
			"course" => $course
		);
		db_create_object_from_array("create enrollment", $obj, "courseusers");
	}
}

function data_assign_to_course($course, $assignments) {
	db_delete_objects("destroy existing assignments", "courseassignment",
		array("course" => $course));
	foreach ($assignments as $a) {
		$obj = array(
				"assignment" => $a,
				"course" => $course
		);
		db_create_object_from_array("assign assignment", $obj, "courseassignment");
	}
}

function data_create_course($cid, $name) {
	$course = array(
			"cid" => $cid,
			"name" => $name
	);

	db_create_object_from_array("create new course", $course, "course");
}

function data_update_course($cid, $name) {
	$course = array(
			"cid" => $cid,
			"name" => $name
	);

	db_update_object_from_array("update existing course", $course, "course", 'cid');
}
