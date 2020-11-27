<?php

require_once 'AppController.php';
require_once __DIR__.'/../moduls/User.php';

class UserController extends AppController
{

    private $messages = [];

    public function editAvatar() 
    {
        if($this->isPost()) {
            // TODO
            return $this->render('user-details', ['messages' => $this->messages]);
        }
        return $this->render('avatar', ['messages' => $this->messages]);
    }


    public function profile() {
        return $this->render('user-details',  ['messages' => $this->messages]);
    }
}