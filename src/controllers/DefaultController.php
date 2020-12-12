<?php

require_once 'AppController.php';

class DefaultController extends AppController {

    public function users() {
        $this->render('user-list');
    }
}