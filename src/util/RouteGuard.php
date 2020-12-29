<?php


class RouteGuard
{
    public static function checkAuthentication() {
        session_start();
        if(isset($_SESSION['id'])){
            return true;
        }
        $url = "http://$_SERVER[HTTP_HOST]";    // server address
        header("Location: {$url}/login");
        return false;
    }

    public static function clearSession() {
        session_start();
        if(isset($_SESSION['id'])){
            session_destroy();
        }
    }
}