<?php

require 'Router.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

Router::get('', 'UserController');
Router::get('login', 'SecurityController');
Router::get('register', 'SecurityController');
Router::get('users', 'UserController');
Router::get('profile', 'UserController');
Router::get('myDetails', 'UserController');
Router::get('editProfile', 'UserController');

Router::post('filter', 'UserController');
Router::post('conversation', 'UserController');
Router::post('message', 'UserController');
Router::post('rateUser', 'UserController');
Router::post('login', 'SecurityController');
Router::post('register', 'SecurityController');
Router::post('editAvatar', 'UserController');
Router::post('editDetails', 'UserController');
Router::post('editPassword', 'SecurityController');

Router::run($path);