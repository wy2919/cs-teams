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

    private int $currentUserId;
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
        if(RouteGuard::checkAuthentication()) {
            $this->currentUserId = RouteGuard::getAuthenticatedUserId();
        }
    }

    public function editProfile()
    {
        $userId = $_POST['userId'];
        $rankId = $_POST['rankId'];
        $description = $_POST['description'];
        $this->validateAuthorizationToModifyUser($userId);

        if(isset($rankId)){
            $this->setRank($userId, $rankId);
        }
        else if(isset($description)){
            $this->setDescription($userId, $description);
        }
        else if(isset($_FILES['file'])) {
            $this->setAvatar();
        }
        return $this->loadEditProfile($userId);
    }

    public function myDetails()
    {
        try {
            return $this->render('my-profile', [
                'user' => $this->userRepository->getUserDtoById($this->currentUserId)
            ]);
        } catch (UnexpectedValueException $e){
            return $this->handleException($e);
        }
    }

    public function profile($username)
    {
        try {
            return $this->render('user-details', [
                'user' => $this->userRepository->getUserDtoByUsername($username),
                'message' => $this->message,
                'isAdmin' => RouteGuard::hasAdminRole()
            ]);
        } catch (UnexpectedValueException $e){
            return $this->handleException($e);
        }
    }

    public function users()
    {
        return $this->render('user-list', [
            'ranks' => $this->rankRepository->getRanks(),
            'users' => $this->userRepository->getUsersDtoExceptUser($this->currentUserId)
        ]);
    }

    public function filter()
    {
        $decoded = $this->decodeJsonRequest();
        if(!$decoded) {
            return null;
        }
        $rank = (int)$decoded['rank'];
        $elo = (int)$decoded['elo'];

        if($rank < 0) {
           echo json_encode($this->userRepository->eloFilteredUsersDtoExceptUser($this->currentUserId, $elo));
        } else {
            echo json_encode($this->userRepository->eloAndRankFilteredUsersDtoExceptUser($this->currentUserId, $elo, $rank));
        }
    }

    public function rateUser()
    {
        try {
            $userToBeRated = $this->userRepository->getUserDtoByUsername($_POST['username']);
        } catch (UnexpectedValueException $e){
            return $this->handleException($e);
        }

        if($userToBeRated->getId() === $this->currentUserId) {
            $this->message = "Unfortunately You can't rate Yourself ;)";
            return $this->profile($userToBeRated->getUsername());
        }

        $wasNotAlreadyRated = $this->ratingRepository->attemptToCreateRating(new Rating(
            null,
            $userToBeRated->getId(),
            $this->currentUserId,
            $_POST['skills'],
            $_POST['friendliness'],
            $_POST['communication']
        ));
        $this->message = $wasNotAlreadyRated ? 'You successfully rated player.' : 'You already rated this player!';

        return $this->profile($userToBeRated->getUsername());
    }

    private function setRank($userId, $rankId) {
        $isSuccessful = $this->userRepository->setUserRank($userId, $rankId);
        $this->message = $isSuccessful ? 'Rank Changed successfully.' : 'Could not change rank.';
    }

    private function setDescription($userId, $description) {
        try {
            $userDetailsId = $this->userRepository->getUserDetailsId($userId);
        } catch (UnexpectedValueException $e){
            return $this->handleException($e);
        }
        $isSuccessful = $this->userRepository->setUserDescription($userDetailsId, $description);
        $this->message = $isSuccessful ? 'Description Changed successfully.' : 'Could not change description.';
    }

    private function setAvatar()
    {
        $userId = $_POST['userId'];

        if ($this->isPost() && is_uploaded_file($_FILES['file']['tmp_name']) && $this->validateAvatar($_FILES['file'])) {          // 'file' to nazwa name="" ustawiona w html, a tmp_name to tak już jest..

            move_uploaded_file(
                $_FILES['file']['tmp_name'],
                dirname(__DIR__) . self::UPLOAD_DIRECTORY . $_FILES['file']['name']
            );
            $isSuccessful = $this->userRepository->setUserImage($userId, $_FILES['file']['name']);
            $this->message = $isSuccessful ? 'Image Changed successfully.' : 'Could not change Image.';
        }
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

    private function loadEditProfile($userId) {
        try {
            return $this->render('edit-profile', [
                'message' => $this->message,
                'ranks' => $this->rankRepository->getRanks(),
                'user' => $this->userRepository->getUserDtoById($userId)
            ]);
        } catch (UnexpectedValueException $e){
            return $this->handleException($e);
        }
    }

    private function validateAuthorizationToModifyUser($userId){
        if($userId != $this->currentUserId && !RouteGuard::hasAdminRole()){
            $url = "http://$_SERVER[HTTP_HOST]";    // server address
            header("Location: {$url}/login");
        }
    }
}
