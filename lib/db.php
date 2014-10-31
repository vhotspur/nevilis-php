<?php

function db_log_query($description, $sql, $params, $result = null) {
	if (! option('debug')) {
		return;
	}
	if (! isset($GLOBALS['SQL_QUERIES'])) {
		$GLOBALS['SQL_QUERIES'] = array ();
	}
	$GLOBALS['SQL_QUERIES'][] = array (
		"description" => $description,
		"query" => $sql,
		"params" => $params 
	);
}

function db_init($connection_string) {
	$db = new PDO($connection_string);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	option('db_conn', $db);
}

function db_find_objects($description, $sql, $params = array()) {
	$conn = option('db_conn');
	
	$result = array();
	
	$stmt = $conn->prepare($sql);
	if ($stmt->execute($params)) {
		while ($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
			$result[] = $obj;
		}
	}
	
	db_log_query($description, $sql, $params, $result);
	
	return $result;
}

function db_find_object($description, $sql, $params = array()) {
	$conn = option('db_conn');
	
	$result = null;
	
	$stmt = $conn->prepare($sql);
	if ($stmt->execute($params)) {
		$result = $stmt->fetch(PDO::FETCH_OBJ);
	}
	
	db_log_query($description, $sql, $params, $result);
	
	return $result;
}

function db_prepare_bind_value_for_insert($x) {
	return ":" . $x;
}

function db_prepare_bind_value_for_update($x) {
	return sprintf("%s = :%s", $x, $x);
}

function db_create_object_from_array($description, $object_array, $table) {
	$columns = array_keys($object_array);
	
	$columns_colons = array_map('db_prepare_bind_value_for_insert', $columns);
	
	$sql = sprintf("INSERT INTO `%s` ( %s ) VALUES ( %s )",
		$table, implode(', ', $columns), implode(', ', $columns_colons));
	
	$conn = option('db_conn');
	$stmt = $conn->prepare($sql);
	foreach ($columns as $c) {
		$stmt->bindValue(':' . $c, $object_array[$c]);
	}
	
	$result = $stmt->execute();
	
	db_log_query($description, $sql, $object_array, $result);
}

function db_update_object_from_array($description, $object_array, $table, $id_columns) {
	$columns = array_keys($object_array);
	
	if (!is_array($id_columns)) {
		$id_columns = array($id_columns);
	}
	
	$columns_assignments = array_map('db_prepare_bind_value_for_update', $columns);
	$id_columns_comparison = array_map('db_prepare_bind_value_for_update', $id_columns);
	
	$sql = sprintf("UPDATE `%s` SET %s WHERE %s",
			$table, implode(', ', $columns_assignments),
			implode(' AND ', $id_columns_comparison));
	
	$conn = option('db_conn');
	$stmt = $conn->prepare($sql);
	foreach ($columns as $c) {
		$stmt->bindValue(':' . $c, $object_array[$c]);
	}
	
	$result = $stmt->execute();
	
	db_log_query($description, $sql, $object_array, $result);
}

function db_delete_objects($description, $table, $conditions) {
	$comparisons = array_map('db_prepare_bind_value_for_update', array_keys($conditions));
	$sql = sprintf("DELETE FROM `%s` WHERE %s",
			$table, implode(' AND ', $comparisons));
	
	$conn = option('db_conn');
	$stmt = $conn->prepare($sql);
	foreach ($conditions as $key => $value) {
		$stmt->bindValue(':' . $key, $value);
	}
	
	$result = $stmt->execute();
	
	db_log_query($description, $sql, $conditions, $result);
}
