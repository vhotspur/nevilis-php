<?php
if (flash_now('reset_passwords') != null) {
	$passwords = flash_now('reset_passwords');
	?><textarea cols="60" rows="5"><?php
	foreach ($passwords as $p) {
		printf("%s (%s) %s\n", $p["name"], $p["uid"], $p["password"]);
	}
	?></textarea>
	<?php
} 
?>

<p>
	<a href="%url('admin', 'users', 'reset-password')%">%_Reset passwords_%</a>
</p>

<ul>
<li><a href="%url('admin', 'users', 'add')%">Add new</a></li>
%foreach $users $u%
<li><a href="%url('admin', 'users', 'edit', $u->uid)%">{$u->name/h} [{$u->uid/h}]</a></li>
%endforeach%
</ul>
