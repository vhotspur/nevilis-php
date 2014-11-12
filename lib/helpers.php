<?php

function v_post($name, $default = null) {
	if (isset($_POST[$name])) {
		return $_POST[$name];
	} else {
		return $default;
	}
}

function flash_format_all() {
	$names = array("error", "info");
	
	$result = "";
	
	foreach ($names as $a) {
		$fl = flash_now($a);
		if ($fl != null) {
			$result .= sprintf("<div class=\"%s\">%s</div>\n",
				$a, $fl);
		}
	}
	
	return $result;
}

function link_to($params = null) {
    $params = func_get_args();
    $name = array_shift($params);
    $url = call_user_func_array('url_for', $params);

    return "<a href=\"$url\">$name</a>";
}

function make_object($array) {
	$obj = new stdClass();
	
	foreach ($array as $key => $value) {
		$obj->$key = $value;
	}
	
	return $obj;
}

function get_file_upload_error_string($error) {
	switch ($error) {
		case UPLOAD_ERR_OK:
			return _('no error');
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			return _('the file is too big');
		case UPLOAD_ERR_PARTIAL:
			return _('file was not fully uploaded, retry please');
		case UPLOAD_ERR_NO_FILE:
			return _('no file provided');
		case UPLOAD_ERR_NO_TMP_DIR:
			return _('internal error, missing temporary directory');
		case UPLOAD_ERR_CANT_WRITE:
			return _('internal error, cannot write to the disk');
		default:
			return sprintf(_('unspecified internal error %s'), $error);
	}
}
