<?php

require_once 'emptyglass.php';

$template = file_get_contents("php://stdin");

$emptyglass = new emptyglass_transformer($template);
$emptyglass->set_url_function('url_for');

$result = $emptyglass->make_php();

printf("%s\n", $result);
