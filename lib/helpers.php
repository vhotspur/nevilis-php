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
			$result .= sprintf("<p class=\"%s\">%s</p>\n",
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
