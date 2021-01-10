<?php


require_once __DIR__ . '/../repository/SessionRepository.php';
class RouteGuard
{

    public static function checkAuthentication()
    {
        $sessionRepository = new SessionRepository();

        if(isset($_COOKIE['token'])){
            return $sessionRepository->isSessionValid($_COOKIE['token']);
        }
        $url = "http://$_SERVER[HTTP_HOST]";    // server address
        header("Location: {$url}/login");
        return false;
    }


    public static function getAuthenticatedUserId($token) {
        // no need to checkAuth because it's checked in Routing.php before each routing
            $sessionRepository = new SessionRepository();
            return $sessionRepository->getSessionUserId($token);
    }

    public static function clearSession() {
        $token = $_COOKIE['token'];
        if(isset($token)){
            setcookie('token', '', time() - 3600);
            $sessionRepository = new SessionRepository();
            $sessionRepository->deleteToken($token);
        }
    }

    public static function createSession($userId, $token, $expiration)
    {
        $sessionRepository = new SessionRepository();
        return $sessionRepository->createSession($userId, $token, $expiration);
    }
}