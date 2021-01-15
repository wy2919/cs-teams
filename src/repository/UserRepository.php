<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../mappers/UserMapper.php';

class UserRepository extends Repository
{
    private UserMapper $userMapper;

    public function __construct()
    {
        parent::__construct();
        $this->userMapper = new UserMapper();
    }

    public function getUserDetailsId(int $userId)
    {
        $statement = $this->database->connect()->prepare('
            SELECT ud.id 
            FROM public.users_details ud
                LEFT JOIN public.users u 
                    ON ud.id = u.id_user_details
            WHERE u.id = :id
        ');

        $statement->execute([$userId]);
        return $statement->fetch(PDO::FETCH_ASSOC)['id'];
    }

    public function getUserDtoById(int $id): ?UserDto
    {
        $statement = $this->database->connect()->prepare('
            SELECT * 
            FROM public.user_dto 
            WHERE id = :id
        ');
        $statement->execute([$id]);
        $record = $statement->fetch(PDO::FETCH_ASSOC);    // association array

        return $this->userMapper->mapAssocToDto($record);
    }

    public function getUsersDtoExceptUser($id)
    {
        $statement = $this->database->connect()->prepare('
            SELECT * 
            FROM public.user_dto 
            WHERE id != :id
        ');
        $statement->execute([(int)$id]);
        $records = $statement->fetchAll(PDO::FETCH_ASSOC);    // association array

        return $this->userMapper->mapMultipleAssocToDto($records);
    }

    public function eloRankFilteredUsersDtoExceptUser(int $userId, float $elo, int $rankId)
    {

        $statement = $this->database->connect()->prepare('
            SELECT
                u.id, u.email, u.username, u.image, u.description, u.role, r.rank, u.elo
            FROM public.user_dto u 
                LEFT JOIN public.ranks r 
                    ON u.rank = r.rank 
            WHERE u.id != :id_user 
              AND r.id = :id_rank 
              AND u.elo >= :elo
              ');
        $statement->execute([(int)$userId, (int)$rankId, (float)$elo]);
        //json
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eloFilteredUsersDtoExceptUser(int $userId, float $elo)
    {

        $statement = $this->database->connect()->prepare('
            SELECT
                u.id, u.email, u.username, u.image, u.description, u.role, r.rank, u.elo
            FROM public.user_dto u 
                LEFT JOIN public.ranks r 
                    ON u.rank = r.rank 
            WHERE u.id != :id_user 
              AND u.elo >= :elo
        ');

        $statement->execute([(int)$userId, (float)$elo]);

        // json
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($userId)
    {
        $statement = $this->database->connect()->prepare('
            SELECT * 
            FROM public.users 
            WHERE id = :id
        ');
        $statement->execute([$userId]);

        $user = $statement->fetch(PDO::FETCH_ASSOC);    // association array

        return $this->userMapper->mapAssocArrayToUser($user);
    }

    public function getUserByEmail(string $email): ?User
    {
        $statement = $this->database->connect()->prepare('
            SELECT * 
            FROM public.users 
            WHERE email = :email
        ');
        $statement->execute([$email]);

        $user = $statement->fetch(PDO::FETCH_ASSOC);    // association array

        return $this->userMapper->mapAssocArrayToUser($user);
    }

    public function getUserByUsername(string $username): ?User
    {
        $statement = $this->database->connect()->prepare('
            SELECT * 
            FROM public.users 
            WHERE username = :username
        ');

        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->execute();

        $record = $statement->fetch(PDO::FETCH_ASSOC);    // association array

        return $this->userMapper->mapAssocArrayToUser($record);
    }

    public function addUser(User $user): void
    {
        $statement = $this->database->connect()->prepare('
            INSERT INTO public.users 
                (email, password, username, id_rank, id_user_details) 
                VALUES(?, ?, ?, ?, ?)
        ');

        $statement->execute([
            $user->getEmail(),
            $user->getPassword(),
            $user->getUsername(),
            $user->getIdRank(),
            $this->addUserDetails()
        ]);
    }

    public function addUserDetails(): int
    {

        $statement = $this->database->connect()->prepare('
            INSERT INTO 
                public.users_details(description) 
                VALUES(?) 
                RETURNING id
        ');
        $statement->execute([null]);

        return $statement->fetch(PDO::FETCH_ASSOC)['id'];
    }

    public function setUserRank(int $userId, int $rankId)
    {
        $statement = $this->database->connect()->prepare('
            UPDATE public.users 
                SET id_rank = :id_rank
            WHERE id = :id_user
        ');

        return $statement->execute([$rankId, $userId]);
    }

    public function setUserDescription($userDetailsId, $description)
    {
        $statement = $this->database->connect()->prepare('
            UPDATE public.users_details 
                SET description = :description
            WHERE id = :id
        ');

        return $statement->execute([$description, $userDetailsId]);
    }

    public function setUserImage($userId, $image)
    {
        $statement = $this->database->connect()->prepare('
            UPDATE public.users 
                SET image = :image
            WHERE id = :id_user
        ');

        return $statement->execute([$image, $userId]);
    }

    public function setUserPassword($userId, $newPassword)
    {
        $statement = $this->database->connect()->prepare('
            UPDATE public.users 
                SET password = :password
            WHERE id = :id_user
        ');

        return $statement->execute([$newPassword, $userId]);
    }
}