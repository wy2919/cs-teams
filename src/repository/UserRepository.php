<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/User.php';

class UserRepository extends Repository
{
    public function getUserByEmail(string $email): ?User
    {
        $statement = $this->database->connect()->prepare('
            SELECT * FROM public.users WHERE email = :email
        ');

        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);    // association array

        if($user == false){
            return null;    // we should throw exception instead of return null and handle it in place where we run this fun
        }

        return new User(
            $user['id'],
          $user['email'],
          $user['username'],
          $user['password'],
          $user['image'],
          $user['enable'],
          $user['created_at'],
          $user['id_rank'],
          $user['id_user_details']
        );
    }

    public function getUserByUsername(string $username): ?User
    {
        $statement = $this->database->connect()->prepare('
            SELECT * FROM public.users WHERE username = :username
        ');

        $statement->bindParam(':username', $username, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetch(PDO::FETCH_ASSOC);    // association array

        if($user == false){
            return null;    // we should throw exception instead of return null and handle it in place where we run this fun
        }

        return new User(
            $user['id'],
            $user['email'],
            $user['username'],
            $user['password'],
            $user['image'],
            $user['enable'],
            $user['created_at'],
            $user['id_rank'],
            $user['id_user_details']
        );
    }

    public function addUser(User $user): void
    {
        $statement = $this->database->connect()->prepare('
            INSERT INTO public.users (email, password, username, id_rank) VALUES(?, ?, ?, ?)
        ');

        $statement->execute([
            $user->getEmail(),
            $user->getPassword(),
            $user->getUsername(),
            $user->getIdRank(),
        ]);
    }

    public function getUsers()
    {
        $statement = $this->database->connect()->prepare('
            SELECT * FROM public.users
        ');
        $statement->execute();

        $records = $statement->fetchAll(PDO::FETCH_ASSOC);    // association array

        if($records == false){
            return null;    // we should throw exception instead of return null and handle it in place where we run this fun
        }

        $users = array();
        foreach($records as $user){
            $users[] = new User(
                $user['id'],
                $user['email'],
                $user['username'],
                null,
                $user['image'],
                $user['enable'],
                $user['created_at'],
                $user['id_rank'],
                $user['id_user_details']
            );
        }
        return $users;
    }
}