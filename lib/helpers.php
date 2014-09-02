<?php

function v_post($name, $default = null) {
	if (isset($_POST[$name])) {
		return $_POST[$name];
	} else {
		return $default;
	}
}

function flash_format_all() {
	$settings = array(
		array("error", "#800"),
		array("info", "#080")
	);
	
	$result = "";
	
	foreach ($settings as $s) {
		if (flash_now($s[0]) != null) {
			$result .= sprintf("<p style=\"color: %s\">%s</p>\n",
				$s[1], flash_now($s[0]));
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
