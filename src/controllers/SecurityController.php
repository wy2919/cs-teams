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

        if($user->getPassword() != $password) {
            return $this->render('login', ['messages'=>['Incorrect password']]);
        }

        $_SESSION['id'] = $user->getId();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION['username'] = $user->getUsername();


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
        $rank   = $_POST['rank'];

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
                $password,
                'placeholder.png',
                false,
                null,
                $rank,
                null
            ));

        $user = $this->userRepository->getUserByEmail($email);
        $_SESSION['id'] = $user->getId();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION['username'] = $user->getUsername();

        $url = "http://$_SERVER[HTTP_HOST]";    // server address
        header("Location: {$url}/users");
    }

    public function editPassword()
    {
        RouteGuard::checkAuthentication();

        if(strlen($_POST['newPassword']) <= 5) {
            $message = 'Password must be longer than 5 characters!';
        }
        else if($_POST['newPassword'] != $_POST['newPasswordConfirm']) {
            $message = 'new Password and confirmation does not match!';
        }
        else if($_POST['password'] != $this->userRepository->getUserByEmail($_SESSION['email'])->getPassword()) {
                $message = 'wrong password!';
        } else {
            if($this->userRepository->setUserPassword($_SESSION['id'], $_POST['newPassword'])){
                $message = 'Password Changed successfully.';
            } else {
                $message = 'Could not change password.';
            }
        }
        return $this->render('edit-profile', [
            'message' => $message,
            'ranks' => $this->rankRepository->getRanks(),
            'user' => $this->userRepository->getUserDtoById($_SESSION['id'])]);
    }
}