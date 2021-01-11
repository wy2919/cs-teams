<?php


require_once __DIR__ . '/../repository/SessionRepository.php';
require_once __DIR__ . '/../repository/UserRepository.php';
class RouteGuard
{
    const ADMIN_ROLE = 1;
    const TOKEN_NAME = 'token';

    public static function checkAuthentication()
    {
        $sessionRepository = new SessionRepository();

        if(isset($_COOKIE[self::TOKEN_NAME])){
            return $sessionRepository->isSessionValid($_COOKIE[self::TOKEN_NAME]);
        }
        $url = "http://$_SERVER[HTTP_HOST]";    // server address
        header("Location: {$url}/login");
        return false;
    }


    public static function getAuthenticatedUserId() {
        // no need to checkAuth because it's checked in Routing.php before each routing
            $sessionRepository = new SessionRepository();
            return $sessionRepository->getSessionUserId($_COOKIE[self::TOKEN_NAME]);
    }

    public static function hasAdminRole() {
        $sessionRepository = new SessionRepository();
        $userRepository = new UserRepository();

        $userId = $sessionRepository->getSessionUserId($_COOKIE[self::TOKEN_NAME]);
        return $userRepository->getUserDtoById($userId)->getRole() === self::ADMIN_ROLE;
    }


    public static function clearSession() {
        $token = $_COOKIE[self::TOKEN_NAME];
        if(isset($token)){
            setcookie(self::TOKEN_NAME, '', time() - 3600);
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