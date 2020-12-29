<?php

require_once 'AppController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserDto.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/RankRepository.php';
require_once __DIR__ . '/../repository/RatingRepository.php';
require_once __DIR__ . '/../util/RouteGuard.php';

class UserController extends AppController
{

    const MAX_FILE_SIZE = 1024 * 1024;
    const SUPPORTED_TYPES = ['image/png', 'image/jpeg'];
    const UPLOAD_DIRECTORY = '/../public/uploads/';

    private array $messages = [];
    private UserRepository $userRepository;
    private RankRepository $rankRepository;
    private RatingRepository $ratingRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->rankRepository = new RankRepository();
        $this->ratingRepository = new RatingRepository();
    }


    public function editAvatar() 
    {
        RouteGuard::checkAuthentication();
        if($this->isPost() && is_uploaded_file($_FILES['file']['tmp_name']) && $this->validateAvatar($_FILES['file'])) {          // 'file' to nazwa name="" ustawiona w html, a tmp_name to tak już jest..

            move_uploaded_file(
                $_FILES['file']['tmp_name'],
                dirname(__DIR__).self::UPLOAD_DIRECTORY.$_FILES['file']['name']
            );

            // TODO: add this avatar to currently logged user and delete this message image=>$fileAddress
            $fileAddress = $_FILES['file']['name'];
            return $this->render('my-profile', ['messages' => $this->messages, 'image' => $fileAddress]);
        }
        return $this->render('avatar', ['messages' => $this->messages]);
    }


    private function validateAvatar(array $file): bool{
        RouteGuard::checkAuthentication();
        if( $file['size'] > self::MAX_FILE_SIZE) {
            $this->messages[] = 'File is too large for destination system.';
            return false;
        }

        if(!isset($file['type']) || !in_array($file['type'], self::SUPPORTED_TYPES)){
            $this->messages[] = 'File type is not supported';
            return false;
        }

        return true;
    }

    public function users() {
        RouteGuard::checkAuthentication();
        $this->render('user-list', ['ranks' => $this->rankRepository->getRanks(),
                                                'users' => $this->userRepository->getUsers(),
                                                'rankRepository' => $this->rankRepository,
                                                'ratingRepository' => $this->ratingRepository]);
    }

    // session user profile
    public function myDetails() {
        RouteGuard::checkAuthentication();
        return $this->render('my-profile',  ['user' => $this->userRepository->getUserDtoById($_SESSION['id'])]);

    }

    // specific user profile
    public function profile() {
        RouteGuard::checkAuthentication();
        return $this->render('user-details',  ['messages' => $this->messages]);
    }

    public function friends() {
        RouteGuard::checkAuthentication();
        return $this->render('friends');
    }
}