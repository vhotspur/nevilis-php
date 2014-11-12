<?php

$GLOBALS['FILE_VALIDATORS'] = array(
	"jpeg" => array(
		"name" => _('JPEG image'),
		"error" => _('not a JPEG image'),
		"extension" => '.jpg',
		"mime" => 'image/jpeg',
		"validator" => "file_validate_jpeg"
	),
	"png" => array(
		"name" => _('PNG image'),
		"error" => _('not a PNG image'),
		"extension" => '.png',
		"mime" => 'image/png',
		"validator" => "file_validate_png"
	),
	"pdf" => array(
		"name" => _('PDF file'),
		"error" => _('not a PDF file'),
		"extension" => '.pdf',
		"mime" => 'application/pdf',
		"validator" => "file_validate_pdf"
	),
	"msaccess2007" => array(
		"name" => _('Microsoft Office Access 2007'),
		"error" => _('not a MS Access 2007 file'),
		"extension" => '.accdb',
		"mime" => 'application/msaccess',
		"validator" => "file_validate_msaccess2007"
	),
);

function file_validation_get_ids() {
	global $FILE_VALIDATORS;
	
	return array_keys($FILE_VALIDATORS);
}

function file_validate($filename, $validators) {
	global $FILE_VALIDATORS;
	
	$result = array(
		'errors' => array(),
		'extension' => '',
		'mime' => ''
	);
	
	foreach ($validators as $validator) {
		if ($validator == "") {
			continue;
		}
		if (!isset($FILE_VALIDATORS[$validator])) {
			$result['errors'][] = sprintf(_('unknown file validator %s'), $validator);
			continue;
		}
		
		$ok = $FILE_VALIDATORS[$validator]["validator"]($filename);
		if (!$ok) {
			$result['errors'][] = $FILE_VALIDATORS[$validator]["error"];
		} else {
			$result['extension'] .= $FILE_VALIDATORS[$validator]["extension"];
			$result['mime'] = $FILE_VALIDATORS[$validator]["mime"];
		}
	}
	
	$result['ok'] = count($result['errors']) == 0;
	
	return $result;
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
	$bytes = array_values(unpack('C*', $data));
	
	for ($i = 0; $i < $bytes_to_read; $i++) {
		if ($first_bytes[$i] === -1) {
			continue;
		}
		if (is_string($first_bytes[$i])) {
			$first_bytes[$i] = ord($first_bytes[$i]);
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

function file_validate_pdf($filename) {
	$pdf_header = array('%', 'P', 'D', 'F', '-');
	return file_validate_first_bytes($filename, $pdf_header);
}

function file_validate_msaccess2007($filename) {
	$header = array(-1, -1, -1, -1,
		'S', 't', 'a', 'n', 'd', 'a', 'r', 'd', ' ',
		'A', 'C', 'E', ' ', 'D', 'B');
	return file_validate_first_bytes($filename, $header);
}
