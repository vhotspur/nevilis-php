<?php

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
	$obj = db_find_object("get course name by its id",
		"SELECT
			name
		FROM
			course
		WHERE
			cid=:id
		", array("id" => $id));
	if ($obj == null) {
		return null;
	} else {
		return $obj->name;
	}
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
