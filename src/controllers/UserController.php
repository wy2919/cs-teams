<?php

require_once 'AppController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserDto.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/ConversationRepository.php';
require_once __DIR__ . '/../repository/RankRepository.php';
require_once __DIR__ . '/../repository/RatingRepository.php';
require_once __DIR__ . '/../security/RouteGuard.php';

class UserController extends AppController
{

    const MAX_FILE_SIZE = 1024 * 1024;
    const SUPPORTED_TYPES = ['image/png', 'image/jpeg'];
    const UPLOAD_DIRECTORY = '/../public/uploads/';

    private string $message = '';
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

    public function editProfile()
    {
        $userId = RouteGuard::getAuthenticatedUserId($_COOKIE['token']);
        return $this->render('edit-profile', [
            'message' => $this->message,
            'ranks' => $this->rankRepository->getRanks(),
            'user' => $this->userRepository->getUserDtoById($userId)]);
    }

    public function editDetails()
    {
        $userId = RouteGuard::getAuthenticatedUserId($_COOKIE['token']);

        if(isset($_POST['rank'])){
            if ($this->userRepository->setUserRank($userId, $_POST['rank'])) {
                $this->message = 'Rank Changed successfully.';
            } else {
                $this->message = 'Could not change rank.';
            }
        }
        else if(isset($_POST['description'])){
            $userDetailsId = $this->userRepository->getUserDetailsId($userId);
            if ($this->userRepository->setUserDescription($userDetailsId, $_POST['description'])) {
                $this->message = 'Description Changed successfully.';
            } else {
                $this->message = 'Could not change description.';
            }
        }
        return $this->editProfile();
    }

    public function editAvatar()
    {
        $userId = RouteGuard::getAuthenticatedUserId($_COOKIE['token']);
        if ($this->isPost() && is_uploaded_file($_FILES['file']['tmp_name']) && $this->validateAvatar($_FILES['file'])) {          // 'file' to nazwa name="" ustawiona w html, a tmp_name to tak już jest..

            move_uploaded_file(
                $_FILES['file']['tmp_name'],
                dirname(__DIR__) . self::UPLOAD_DIRECTORY . $_FILES['file']['name']
            );

            if($this->userRepository->setUserImage($userId, $_FILES['file']['name'])) {
                $this->message = 'Image Changed successfully.';
            } else {
                $this->message = 'Could not change Image.';
            }
        }
        return $this->editProfile();
    }

    // specific user profile
    public function profile()
    {
        return $this->render('user-details', ['user' => $this->userRepository->getUserDtoById($_POST['userId'])]);
    }

    public function users()
    {
        $userId = RouteGuard::getAuthenticatedUserId($_COOKIE['token']);
        return $this->render('user-list', ['ranks' => $this->rankRepository->getRanks(),
            'users' => $this->userRepository->getUsersDtoExceptUser($userId)]);
    }

    public function filter()
    {
        $currentUserId = RouteGuard::getAuthenticatedUserId($_COOKIE['token']);

        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);
            $rank = (int)$decoded['rank'];
            $elo = (int)$decoded['elo'];

            header('Content-type: application/json');
            http_response_code(200);

            if($rank < 0) {
               echo json_encode($this->userRepository->eloFilteredUsersDtoExceptUser($currentUserId, $elo));
            } else {
                echo json_encode($this->userRepository->eloRankFilteredUsersDtoExceptUser($currentUserId, $elo, $rank));
            }
        }
    }

    // session user profile
    public function myDetails()
    {
        $userId = RouteGuard::getAuthenticatedUserId($_COOKIE['token']);
        return $this->render('my-profile', ['user' => $this->userRepository->getUserDtoById($userId)]);

    }

    public function conversation()
    {
        $currentUserId = RouteGuard::getAuthenticatedUserId($_COOKIE['token']);

        $selected = null;
        $messages = null;

        // just display conversation (no specific user-chat selected)
        if (!$_POST['userId']) {
            $conversations = $this->conversationRepository->getUserConversations($currentUserId);
            if (count($conversations) > 0) {
                $selected = $conversations[0];
                $messages = $this->conversationRepository->getConversationMessages($selected->getId());
            }
        } else {
            // creates conversation/return id of existing one if there is
            $conversationId = $this->conversationRepository->newConversation($currentUserId, $_POST['userId']);
            $conversations = $this->conversationRepository->getUserConversations($currentUserId);

            $filtered = array_filter($conversations, function ($conv) use ($conversationId) {
                if ($conv->getId() === $conversationId) {
                    return true;
                }
                return false;
            });

            $selected = reset($filtered);
            $messages = $selected ? $this->conversationRepository->getConversationMessages($conversationId) : null;
        }

        return $this->render('conversation', ['user' => $this->userRepository->getUserDtoById($currentUserId),
            'conversations' => $conversations,
            'selected' => $selected,
            'messages' => $messages]);
    }

    public function message()
    {
        $userId = RouteGuard::getAuthenticatedUserId($_COOKIE['token']);

        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);
            $message = $decoded['message'];
            $conversationId = $decoded['conversationId'];

            header('Content-type: application/json');
            http_response_code(200);

            $this->conversationRepository->newMessage($conversationId, $userId, $message);
            echo json_encode($this->conversationRepository->getConversationMessagesAssoc($conversationId));
        }
    }

    public function rateUser()
    {
        $userId = RouteGuard::getAuthenticatedUserId($_COOKIE['token']);

        $wasNotAlreadyRated = $this->ratingRepository->addRating(new Rating(
            null,
            $_POST['userId'],
            $userId,
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

    private function validateAvatar(array $file): bool
    {
        if ($file['size'] > self::MAX_FILE_SIZE) {
            $this->message = 'File is too large for destination system.';
            return false;
        }

        if (!isset($file['type']) || !in_array($file['type'], self::SUPPORTED_TYPES)) {
            $this->message = 'File type is not supported';
            return false;
        }
        return true;
    }
}