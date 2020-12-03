<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/User.php';

class UserRepository extends Repository
{
    public function getUser(string $email): ?User
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
          $user['email'],
          $user['password'],
          $user['name'],
          $user['surname'],
          $user['image']
        );
    }
}