<?php
// pierwszy plik uruchamiany na serverze


require 'Router.php';

$path = trim($_SERVER['REQUEST_URI'], '/'); // usuwamy pierwszego slasha - ścieżka z przeglądarki
$path = parse_url($path, PHP_URL_PATH);

Router::get('', 'SecurityController');
Router::get('login', 'SecurityController');  // 'login' to url localhost:8080/index i nazwa funkcji
Router::get('register', 'SecurityController');
Router::get('users', 'DefaultController');
Router::get('profile', 'UserController');


Router::post('login', 'SecurityController');
Router::post('register', 'SecurityController');
Router::post('editAvatar', 'UserController');

Router::run($path);