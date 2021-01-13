<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/RankRepository.php';
require_once __DIR__.'/../controllers/UserController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../security/RouteGuard.php';

class SecurityController extends AppController
{
    private UserRepository $userRepository;
    private RankRepository $rankRepository;

    public function __construct()
    {
        parent::__construct();
        $this->rankRepository = new RankRepository();
        $this->userRepository = new UserRepository();
    }

    public function login()
    {
        RouteGuard::clearSession();
        if($this->isGet()){
            return $this->render('login');
        }

        $email = $_POST['email'];       // 'email' -> <input name="email" ... />
        $password = $_POST['password'];

        $user = $this->userRepository->getUserByEmail($email);

        if (!$user){
            return $this->render('login', ['messages' => ['User with this email not exist!']]);
        }

        if(!password_verify($password, $user->getPassword())) {
            return $this->render('login', ['messages'=>['Incorrect password']]);
        }

        $user = $this->userRepository->getUserByEmail($email);
        $cookieName = 'token';
        $cookieValue = password_hash($user->getEmail().$user->getUsername(), PASSWORD_DEFAULT);
        $expiration = time() + (86400 * 7);

        setcookie($cookieName, $cookieValue, $expiration);
        RouteGuard::createSession($user->getId(), $cookieValue, $expiration);

        $url = "http://$_SERVER[HTTP_HOST]";    // server address
        header("Location: {$url}/users");
    }


    public function register()
    {
        RouteGuard::clearSession();

        $ranks = $this->rankRepository->getRanks();
        if($this->isGet()){
            return $this->render('register', ['ranks'=>$ranks]);
        }
        if($_POST['password'] != $_POST['passwordConfirm']) {
            return $this->render('register', ['messages'=>['Password does not match'],'ranks'=>$ranks]);
        }
        if(strlen($_POST['password']) <= 5){
            return $this->render('register', ['messages'=>['Password must be longer than 5 characters!'],'ranks'=>$ranks]);
        }

        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $rank = $_POST['rank'];

        $user = $this->userRepository->getUserByEmail($email);
        if ($user){
            return $this->render('register', ['messages' => ['User with this email exist!'], 'ranks'=>$ranks]);
        }

        $user = $this->userRepository->getUserByUsername($username);
        if ($user){
            return $this->render('register', ['messages' => ['User with this username exist!'], 'ranks'=>$ranks]);
        }

        $this->userRepository->addUser(
            new User(
                null,
                $email,
                $username,
                $hashedPassword,
                'placeholder.png',
                null,
                $rank,
                null
            ));

        $this->render('login', ['messages' => ['Account created!']]);
    }

    public function editPassword()
    {
        $userId = $_POST['userId'];
        if($userId !== RouteGuard::getAuthenticatedUserId() && !RouteGuard::hasAdminRole()){
            return $this->render('login');
        }

        if(strlen($_POST['newPassword']) <= 5) {
            $message = 'Password must be longer than 5 characters!';
        }
        else if($_POST['newPassword'] != $_POST['newPasswordConfirm']) {
            $message = 'new Password and confirmation does not match!';
        }
        else if(!password_verify($_POST['password'], $this->userRepository->getUserById($userId)->getPassword())) {
                $message = 'wrong password!';
        } else {
            if($this->userRepository->setUserPassword($userId, password_hash($_POST['newPassword'], PASSWORD_DEFAULT))){
                $message = 'Password Changed successfully.';
            } else {
                $message = 'Could not change password.';
            }
        }
        return $this->render('edit-profile', [
            'message' => $message,
            'ranks' => $this->rankRepository->getRanks(),
            'user' => $this->userRepository->getUserDtoById($userId)]);
    }
}