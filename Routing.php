<?php

class Routing {

    public static $routes; // url i ścieżka do dopowiedniego controllera

    public static function get($url, $controller) {
        self::$routes[$url] = $controller;
    }

    public static function run($url) {
        $action = explode("/", $url)[0]; // dzieli plik wejściowy względem separatora (pobieramy pierwszą część url)

        if(!array_key_exists($action, self::routes)){
            die("Wong url!"); // zatrzymuje działanie interpretera
        }

        // TODO call controller method
    }

}
