<?php
// pierwszy plik uruchamiany na serverze


require 'Routing.php';

$path = trim($_SERVER['REQUEST_URI'], '/'); // usuwamy pierwszego slasha - ścieżka z przeglądarki
$path = parse_url($path, PHP_URL_PATH);
   

Routing::get('index', 'DefaultController');  // 'index' to url localhost:8080/index i nazwa funkcji
Routing::get('users', 'DefaultController');

Routing::run($path);