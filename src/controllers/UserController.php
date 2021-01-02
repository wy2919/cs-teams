<?php

require_once 'AppController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserDto.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/ConversationRepository.php';
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
    private ConversationRepository $conversationRepository;
    private RankRepository $rankRepository;
    private RatingRepository $ratingRepository;

    public function __construct()
    {
        parent::__construct();
        $this->userRepository = new UserRepository();
        $this->conversationRepository = new ConversationRepository();
        $this->rankRepository = new RankRepository();
        $this->ratingRepository = new RatingRepository();
    }


    public function editAvatar()
    {
        RouteGuard::checkAuthentication();
        if ($this->isPost() && is_uploaded_file($_FILES['file']['tmp_name']) && $this->validateAvatar($_FILES['file'])) {          // 'file' to nazwa name="" ustawiona w html, a tmp_name to tak już jest..

            move_uploaded_file(
                $_FILES['file']['tmp_name'],
                dirname(__DIR__) . self::UPLOAD_DIRECTORY . $_FILES['file']['name']
            );

            // TODO: add this avatar to currently logged user and delete this message image=>$fileAddress
            $fileAddress = $_FILES['file']['name'];
            return $this->render('my-profile', ['messages' => $this->messages, 'image' => $fileAddress]);
        }
        return $this->render('avatar', ['messages' => $this->messages]);
    }


    private function validateAvatar(array $file): bool
    {
        RouteGuard::checkAuthentication();
        if ($file['size'] > self::MAX_FILE_SIZE) {
            $this->messages[] = 'File is too large for destination system.';
            return false;
        }

        if (!isset($file['type']) || !in_array($file['type'], self::SUPPORTED_TYPES)) {
            $this->messages[] = 'File type is not supported';
            return false;
        }

        return true;
    }

    // specific user profile
    public function profile()
    {
        RouteGuard::checkAuthentication();
        return $this->render('user-details', ['user' => $this->userRepository->getUserDtoById($_POST['userId'])]);
    }

    public function users()
    {
        RouteGuard::checkAuthentication();
        $this->render('user-list', ['ranks' => $this->rankRepository->getRanks(),
            'users' => $this->userRepository->getUsersDto()]);
    }

    // session user profile
    public function myDetails()
    {
        RouteGuard::checkAuthentication();
        return $this->render('my-profile', ['user' => $this->userRepository->getUserDtoById($_SESSION['id'])]);

    }

    public function conversation()
    {
        RouteGuard::checkAuthentication();

        $selected = null;
        $messages = null;

        // just display conversation (no specific user-chat selected)
        if (!$_POST['userId']) {
            $conversations = $this->conversationRepository->getUserConversations($_SESSION['id']);
            if (count($conversations) > 0) {
                $selected = $conversations[0];
                $messages = $this->conversationRepository->getConversationMessages($selected->getId());
            }
        } else {
            // creates conversation/return id of existing one if there is
            $conversationId = $this->conversationRepository->newConversation($_SESSION['id'], $_POST['userId']);
            $conversations = $this->conversationRepository->getUserConversations($_SESSION['id']);

            $filtered = array_filter($conversations, function ($conv) use ($conversationId) {
                if ($conv->getId() === $conversationId) {
                    return true;
                }
                return false;
            });

            $selected = reset($filtered);
            $messages = $selected ? $this->conversationRepository->getConversationMessages($conversationId) : null;
        }

        return $this->render('conversation', ['user' => $this->userRepository->getUserDtoById($_SESSION['id']),
            'conversations' => $conversations,
            'selected' => $selected,
            'messages' => $messages]);
    }

    public function message()
    {
        RouteGuard::checkAuthentication();

        $this->conversationRepository->newMessage($_POST['conversationId'], $_POST['senderId'], $_POST['message']);
        $conversations = $this->conversationRepository->getUserConversations($_SESSION['id']);

        $filtered = array_filter($conversations, function ($conv) {
            if ($conv->getId() == $_POST['conversationId']) {
                return true;
            }
            return false;
        });

        $selected = reset($filtered);
        $messages = $selected ? $this->conversationRepository->getConversationMessages($_POST['conversationId']) : null;

        return $this->render('conversation', ['user' => $this->userRepository->getUserDtoById($_SESSION['id']),
            'conversations' => $conversations,
            'selected' => $selected,
            'messages' => $messages]);
    }

    public function rateUser()
    {
        // dorobić constraint że można ocenić tylko raz - ew jakaś wiadomość - oceniono/NIE MOŻESZ OCENIC
        RouteGuard::checkAuthentication();
        $wasNotAlreadyRated = $this->ratingRepository->addRating(new Rating(
            null,
            $_POST['userId'],
            $_SESSION['id'],
            $_POST['skills'],
            $_POST['friendliness'],
            $_POST['communication']
        ));
        if (!$wasNotAlreadyRated) {
            return $this->render('user-details', ['user' => $this->userRepository->getUserDtoById($_POST['userId']),
                'messages' => 'You already rated this player!']);
        }

        return $this->render('user-details', ['user' => $this->userRepository->getUserDtoById($_POST['userId']),
            'messages' => 'You successfully rated player.']);
    }
}