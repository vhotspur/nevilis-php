<?php

$GLOBALS['FILE_VALIDATORS'] = array(
	"jpeg" => array(
		"name" => "JPEG image",
		"error" => "not a JPEG image",
		"validator" => "file_validate_jpeg"
	),
	"png" => array(
		"name" => "PNG image",
		"error" => "not a PNG image",
		"validator" => "file_validate_png"
	),
);

function file_validation_get_ids() {
	global $FILE_VALIDATORS;
	
	return array_keys($FILE_VALIDATORS);
}

function file_validate($filename, $validators) {
	global $FILE_VALIDATORS;
	
	$errors = array();
	
	foreach ($validators as $validator) {
		if (!isset($FILE_VALIDATORS[$validator])) {
			$errors[] = sprintf("unknown file validator %s", $validator);
			continue;
		}
		
		$ok = $FILE_VALIDATORS[$validator]["validator"]($filename);
		if (!$ok) {
			$errors[] = $FILE_VALIDATORS[$validator]["error"];
		}
	}
	
	if (count($errors) == 0) {
		return true;
	} else {
		return $errors;
	}
}

function file_validate_first_bytes($filename, $first_bytes) {
	$fd = @fopen($filename, "rb");
	if ($fd === false) {
		return false;
	}
	
	$bytes_to_read = count($first_bytes);
	
	$data = fread($fd, $bytes_to_read);
	if ($data === false) {
		return false;
	}
	$bytes = str_split($data);
	for ($i = 0; $i < $bytes_to_read; $i++) {
		if ($first_bytes[$i] === -1) {
			continue;
		}
		if ($first_bytes[$i] != $bytes[$i]) {
			return false;
		}
	}
	
	return true;
}

function file_validate_jpeg($filename) {
	$jpeg_header = array(-1, -1, -1, -1, -1, -1, 'J', 'F', 'I', 'F');
	if (!file_validate_first_bytes($filename, $jpeg_header)) {
		return false;
	}
	
	$im = @imagecreatefromjpeg($filename);
	if ($im === false) {
		return false;
	}
	imagedestroy($im);
	
	return true;
}

function file_validate_png($filename) {
	$png_header = array(-1, 'P', 'N', 'G');
	if (!file_validate_first_bytes($filename, $png_header)) {
		return false;
	}

	$im = @imagecreatefrompng($filename);
	if ($im === false) {
		return false;
	}
	imagedestroy($im);

	return true;
}

