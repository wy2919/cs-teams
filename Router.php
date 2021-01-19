<?php

require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/UserController.php';

class Router {

    public static $routes;

    public static function get($url, $controller) {
        self::$routes[$url] = $controller;
    }

    public static function post($url, $controller) {
        self::$routes[$url] = $controller;
    }

    public static function run($url) {
        $urlParts = explode("/", $url);
        $action = $urlParts[0]; // first split of url

        if(!array_key_exists($action, self::$routes)){
            die("Wong url!");
        }

        $controller = self::$routes[$action];   // search controller by function name
        $object = new $controller; // create new controller by name
        $action = $action ?: 'users';   // default page

        if($action != 'login' && $action != 'register'){
            RouteGuard::checkAuthentication();
        }

        $pathVariable = $urlParts[1] ?? '';

        $object->$action($pathVariable);
    }


}
