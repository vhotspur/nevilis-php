<?php

function data_get_user_list() {
	return db_find_objects("get list of all users",
		"SELECT
			uid,
			name
		FROM
			user
		");
}

function data_get_user_details($uid) {
	return db_find_object("get information about a user",
		"SELECT
			uid,
			name
		FROM
			user
		WHERE
			uid = :uid
		", array("uid" => $uid));
}

function data_create_user($uid, $name) {
	$user = array(
		"uid" => $uid,
		"name" => $name,
		"password" => "",
		"roles" => ""
	);
	
	db_create_object_from_array("create new user", $user, "user");
}

function data_update_user($uid, $name) {
	$user = array(
		"uid" => $uid,
		"name" => $name
	);

	db_update_object_from_array("update existing user", $user, "user", 'uid');
}
