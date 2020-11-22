<?php

require_once 'src/controllers/DefaultController.php';
require_once 'src/controllers/SecurityController.php';

class Router {

    public static $routes; // url i ścieżka do dopowiedniego controllera


    //dodaje kolejne controllery pod określone url
    public static function get($url, $controller) {
        self::$routes[$url] = $controller;
    }

    public static function post($url, $controller) {
        self::$routes[$url] = $controller;
    }

    public static function run($url) {
        $action = explode("/", $url)[0]; // dzieli plik wejściowy względem separatora (pobieramy pierwszą część url)

        if(!array_key_exists($action, self::$routes)){
            die("Wong url!"); // zatrzymuje działanie interpretera
        }

        // call controller method
        $controller = self::$routes[$action];   // zwraca nazwę controllera
        $object = new $controller; // tworzymy nowy controller po jego nazwie

        $object->$action();
    }


}
