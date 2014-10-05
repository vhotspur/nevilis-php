<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>{$title/h}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		</head>
<body>
<h1>{$title/h}</h1>
<?php echo flash_format_all(); ?>
%if auth_is_user_logged_in()%
<p>
	%_Currently logged in user_%:  <?php echo auth_get_current_user(); ?>
	(<a href="%url('logout')%">%_log out_%</a>).
	<a href="%url('/')%">%_Go to list of courses._%</a>
	%if auth_is_user_admin()%
		<a href="%url('admin')%">%_Go to administration_%</a>
	%endif%
	<a href="%url('change-password')%">%_Change your password._%</a>
</p>
%endif%

<div id="main">
{$content}
</div>
    
<?php
	function print_queries($title, $queries) {
		printf("<h2>%s</h2>\n<dl>", h ( $title ) );
		foreach ( $queries as $q ) {
			printf("<dt>%s</dt>\n", h($q["description"] == "" ? "<no description given>" : $q["description"]));
			printf("<dd><pre style=\"%s\">%s\n---\n%s</pre></dd>\n", "border: 1px solid #eee; margin: 1ex 0; padding: 5px;", $q ["query"], h ( var_export ( $q ["params"], true ) ) );
		}
		printf("</dl>\n");
	}
	
	if (option ( 'debug' )) {
		echo "<div style=\"border: 1px solid #ccc; margin: 1em 0; padding: 1ex;\">";
		print_queries ( "SQL queries executed for this page", isset ( $GLOBALS ['SQL_QUERIES'] ) ? $GLOBALS ['SQL_QUERIES'] : array () );
		if (isset ( $_SESSION ['PREV_SQL_QUERIES'] ) && ($_SESSION ['PREV_SQL_QUERIES'] != null)) {
			print_queries ( "SQL queries executed for previous page", $_SESSION ['PREV_SQL_QUERIES'] );
		}
		unset ( $_SESSION ['PREV_SQL_QUERIES'] );
		echo "</div>";
	}
?>
</body>
</html>
