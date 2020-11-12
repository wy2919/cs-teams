<?php

require 'Routing.php';

$path = trim($_SERVER['REQUEST_URI'], '/'); // usuwamy pierwszego slasha
$path = parse_url($path, PHP_URL_PARH);
   
?>