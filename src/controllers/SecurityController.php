<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/RankRepository.php';
require_once __DIR__.'/../controllers/UserController.php';
require_once __DIR__ . '/../models/User.php';

class SecurityController extends AppController
{
    private UserRepository $userRepository;
    private RankRepository $rankRepository;
    private UserController $userController;

    public function __construct()
    {
        parent::__construct();
        $this->rankRepository = new RankRepository();
        $this->userRepository = new UserRepository();
        $this->userController = new UserController();
    }

    public function login()
    {
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

//        return $this->render('user-list');

        $url = "http://$_SERVER[HTTP_HOST]";    // odczytujemy adres serwera
        header("Location: {$url}/users");
    }


    public function register() {
        $ranks = $this->rankRepository->getRanks();
        if($this->isGet()){
            var_dump($ranks[0]->getRank());
            return $this->render('register', ['ranks'=>$ranks]);
        }
        if($_POST['password1'] != $_POST['password2']) {
            return $this->render('register', ['messages'=>['password does not match'],'ranks'=>$ranks]);
        }

        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password1'];
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
                null,
                false,
                null,
                $rank,
                null
            ));

        $this->userController->users();
    }
}