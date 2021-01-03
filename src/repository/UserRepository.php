<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/User.php';

class UserRepository extends Repository
{
    public function getUserDtoById(int $id): ?UserDto
    {
        $statement = $this->database->connect()->prepare('
            SELECT * FROM public.user_dto WHERE id = :id
        ');

        $statement->bindParam(':id', $id, PDO::PARAM_STR);
        $statement->execute();

        $userDto = $statement->fetch(PDO::FETCH_ASSOC);    // association array

        if ($userDto == false) {
            return null;
        }

        return new UserDto(
            $userDto['id'],
            $userDto['email'],
            $userDto['username'],
            $userDto['image'],
            $userDto['description'],
            $userDto['rank'],
            $userDto['elo']
        );
    }

    public function getUsersDtoExceptUser($id)
    {
        $statement = $this->database->connect()->prepare('
            SELECT * FROM public.user_dto WHERE id != :id
        ');
        $statement->execute([(int)$id]);

        $records = $statement->fetchAll(PDO::FETCH_ASSOC);    // association array

        if ($records == false) {
            return null;    // we should throw exception instead of return null and handle it in place where we run this fun
        }

        $users = array();
        foreach ($records as $user) {
            $users[] = new UserDto(
                $user['id'],
                $user['email'],
                $user['username'],
                $user['image'],
                $user['description'],
                $user['rank'],
                $user['elo']
            );
        }
        return $users;
    }

    public function eloRankFilteredUsersDtoExceptUser(int $userId, float $elo, int $rankId)
    {

        $statement = $this->database->connect()->prepare('
            SELECT * FROM public.user_dto u LEFT JOIN public.ranks r ON u.rank = r.rank WHERE u.id != :id_user AND r.id = :id_rank AND 
        u.elo >= :elo');

        $statement->execute([(int)$userId, (int)$rankId, (float)$elo]);

        $records = $statement->fetchAll(PDO::FETCH_ASSOC);

        if ($records == false) {
            return null;    // we should throw exception instead of return null and handle it in place where we run this fun
        }

        $users = array();
        foreach ($records as $user) {
            $users[] = new UserDto(
                $user['id'],
                $user['email'],
                $user['username'],
                $user['image'],
                $user['description'],
                $user['rank'],
                $user['elo']
            );
        }
        return $users;
    }

    public function eloFilteredUsersDtoExceptUser(int $userId, float $elo)
    {

        $statement = $this->database->connect()->prepare('
            SELECT * FROM public.user_dto u LEFT JOIN public.ranks r ON u.rank = r.rank WHERE u.id != :id_user AND 
        u.elo >= :elo');

        $statement->execute([(int)$userId, (float)$elo]);

        $records = $statement->fetchAll(PDO::FETCH_ASSOC);

        if ($records == false) {
            return null;    // we should throw exception instead of return null and handle it in place where we run this fun
        }

        $users = array();
        foreach ($records as $user) {
            $users[] = new UserDto(
                $user['id'],
                $user['email'],
                $user['username'],
                $user['image'],
                $user['description'],
                $user['rank'],
                $user['elo']
            );
        }
        return $users;
    }

    public function getUserByEmail(string $email): ?User
    {
        $statement = $this->database->connect()->prepare('
            SELECT * FROM public.users WHERE email = :email
        ');

        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);    // association array

        return $this->mapAssocArrayToUser($user);
    }

    public function getUserByUsername(string $username): ?User
    {
        $statement = $this->database->connect()->prepare('
            SELECT * FROM public.users WHERE username = :username
        ');

        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);    // association array

        return $this->mapAssocArrayToUser($user);
    }

    public function addUser(User $user): void
    {
        $statement = $this->database->connect()->prepare('
            INSERT INTO public.users (email, password, username, id_rank, id_user_details) VALUES(?, ?, ?, ?, ?)
        ');

        $statement->execute([
            $user->getEmail(),
            $user->getPassword(),
            $user->getUsername(),
            $user->getIdRank(),
            $this->addUserDetails()
        ]);
    }

    public function addUserDetails(): int {

        $statement = $this->database->connect()->prepare('
            INSERT INTO public.users_details(description) VALUES(?) RETURNING id
        ');
        $statement->execute([null]);

        return $statement->fetch(PDO::FETCH_ASSOC)['id'];
    }

    public function getUsers()
    {
        $statement = $this->database->connect()->prepare('
            SELECT * FROM public.users
        ');
        $statement->execute();

        $records = $statement->fetchAll(PDO::FETCH_ASSOC);    // association array

        if ($records == false) {
            return null;    // we should throw exception instead of return null and handle it in place where we run this fun
        }

        $users = array();
        foreach ($records as $user) {
            $users[] = $this->mapAssocArrayToUser($user);
        }
        return $users;
    }

    public function mapAssocArrayToUser($record): ?User
    {
        if ($record == false) {
            return null;    // we should throw exception instead of return null and handle it in place where we run this fun
        }

        return new User(
            $record['id'],
            $record['email'],
            $record['username'],
            $record['password'],
            $record['image'],
            $record['enable'],
            $record['created_at'],
            $record['id_rank'],
            $record['id_user_details']
        );
    }
}