<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__ . '/../models/User.php';

class SecurityController extends AppController
{

    public function login()
    {
        $userRepository = new UserRepository();

        if($this->isGet()){
            return $this->render('login');
        }

        $email = $_POST['email'];       // 'email' -> <input name="email" ... />
        $password = $_POST['password'];

        $user = $userRepository->getUser($email);

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
}