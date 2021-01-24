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

        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = $this->userRepository->getUserByEmail($email);

        if (!$user){
            return $this->render('login', ['message' => 'User with this email not exist!']);
        }

        if(!password_verify($password, $user->getPassword())) {
            return $this->render('login', ['message'=>'Incorrect password']);
        }

        $user = $this->userRepository->getUserByEmail($email);
        $cookieName = 'token';
        $cookieValue = password_hash($user->getEmail().$user->getUsername(), PASSWORD_DEFAULT);
        $expiration = time() + (86400 * 7);

        setcookie($cookieName, $cookieValue, $expiration);
        RouteGuard::createSession($user->getId(), $cookieValue, $expiration);

        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/users");
    }


    public function register()
    {
        RouteGuard::clearSession();

        if($this->isGet()){
            return $this->renderRegisterWithMessage('');
        }
        if($_POST['password'] != $_POST['passwordConfirm']) {
            return $this->renderRegisterWithMessage('Password does not match');
        }
        if(strlen($_POST['password']) <= 5){
            return $this->renderRegisterWithMessage('Password must be longer than 5 characters!');
        }

        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $rank = $_POST['rank'];

        $this->validateUserNotExists($email, $username);
        $this->userRepository->createUser(
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

        $this->render('login', ['message' => 'Account created!']);
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
        }
        else {
            $isSuccessful = $this->userRepository->setUserPassword(
                $userId, password_hash($_POST['newPassword'], PASSWORD_DEFAULT));
            $message = $isSuccessful ? 'Password Changed successfully.' : 'Could not change password.';
        }
        try {
            return $this->render('edit-profile', [
                'message' => $message,
                'ranks' => $this->rankRepository->getRanks(),
                'user' => $this->userRepository->getUserDtoById($userId)]);
        } catch (UnexpectedValueException $e){
            return $this->handleException($e);
        }
    }

    private function validateUserNotExists($email, $username) {
        $user = $this->userRepository->getUserByEmail($email);
        if ($user){
            return $this->renderRegisterWithMessage('User with this email exist!');
        }

        $user = $this->userRepository->getUserByUsername($username);
        if ($user){
            return $this->renderRegisterWithMessage('User with this username exist!');
        }
    }

    private function renderRegisterWithMessage($message){
        $ranks = $this->rankRepository->getRanks();
        return $this->render('register', ['message'=>$message,'ranks'=>$ranks]);
    }
}