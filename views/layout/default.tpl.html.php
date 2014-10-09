<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>{$title/h}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="css/main.css" rel="stylesheet" type="text/css" media="screen,projection,handheld">
		</head>
<body>
<div id="container">
<h1>{$title/h}</h1>

%if auth_is_user_logged_in()%
<div id="nav">
<h2>%_Menu_%</h2>
<ul>
	<li><a href="%url('/')%">%_Home_%</a>
	%foreach $glob_user_courses $c%
	<li><a href="%url($c->cid)%">{$c->name}</a></li>
	%endforeach%
	%if auth_is_user_admin()%
	<li><a href="%url('admin')%">%_Administration_%</a><ul>
		<li><a href="%url('admin', 'users')%">%_Users_%</a></li>
		<li><a href="%url('admin', 'courses')%">%_Courses_%</a></li>
		<li><a href="%url('admin', 'enroll')%">%_Enroll_%</a></li>
		<li><a href="%url('admin', 'assignments')%">%_Assignments_%</a></li>
		<li><a href="%url('admin', 'assign')%">%_Assign_%</a></li>
		<li><a href="%url('admin', 'grade')%">%_Grade_%</a></li>
		<li><a href="%url('admin', 'download')%">%_Mass download_%</a></li>
	</ul></li>
	%endif%
	<li><a href="%url('change-password')%">%_Change password_%</a>
	<li><a href="%url('logout')%">%_Log out_%</a></li>
</ul>
</div>
%endif%

<div id="content">
%if $glob_flash != ""%
<div id="flashes">
{$glob_flash}
</div>
%endif%

{$content}
    
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
</div>
</div>
</body>
</html>
