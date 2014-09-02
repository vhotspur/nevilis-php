<?php

if (count($courses) == 0) {
	echo "<p>No courses assigned yet.</p>";
} else {
	echo "<ul>";
	foreach ($courses as $course) {
		echo "<li>" . link_to($course->name, '/', $course->cid) . "</li>";
	}
	echo "</ul>";
}
