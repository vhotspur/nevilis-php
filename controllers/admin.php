<?php

function page_admin_main() {
	set('title', _('Administration'));
	return html('admin/main.html.php');
}
