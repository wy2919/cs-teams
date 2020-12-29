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

        // salt generating TODO
//        $existStatement = $this->database->connect()->prepare('
//            SELECT id FROM public.users_details WHERE salt = :salt
//        ');
//
//        // until it generates unique salt
//        do {
//            $salt = random_bytes(32);
//            $existStatement->execute([$salt]);
//        } while($existStatement->rowCount() > 0);
//
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