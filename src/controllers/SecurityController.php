<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/RankRepository.php';
require_once __DIR__.'/../controllers/UserController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../util/RouteGuard.php';

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
            var_dump($ranks[0]->getRank());
            return $this->render('register', ['ranks'=>$ranks]);
        }
        if($_POST['password'] != $_POST['passwordConfirm']) {
            return $this->render('register', ['messages'=>['password does not match'],'ranks'=>$ranks]);
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
}