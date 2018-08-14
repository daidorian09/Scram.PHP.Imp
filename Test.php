<?php

require_once __DIR__ . './CSRFToken.php';
$x = CSRFToken::generate_token();
var_dump($x." "."yeeee");

?>