<?php

require_once 'Repository.php';

class SessionRepository extends Repository
{

    public function isSessionValid($token)
    {
        $statement = $this->database->connect()->prepare('
            SELECT COUNT(*)
            FROM public.sessions 
            WHERE 
                  token = :token
                AND
                  expiration > now();
        ');
        $statement->execute([$token]);
        $count = $statement->fetch()['count'];
        return $count !== 0;
    }

    public function getSessionUserId($token)
    {
        $statement = $this->database->connect()->prepare('
            SELECT id_user
            FROM public.sessions 
            WHERE 
                  token = :token
        ');
        $statement->execute([$token]);
        return $statement->fetch()['id_user'];
    }

    public function deleteToken($token)
    {
        $statement = $this->database->connect()->prepare('
            DELETE
            FROM public.sessions 
            WHERE token = :token;
        ');
        $statement->bindParam(':token', $token, PDO::PARAM_STR);
        $statement->execute();
    }

    public function createSession($userId, $token, $expiration)
    {
        $statement = $this->database->connect()->prepare('
        INSERT INTO public.sessions
            (id_user, token, expiration)
            VALUES (:id_user, :token, :expiration)'
        );

        $expiration = date("Y-m-d H:i:s", $expiration);
        $statement->bindParam(':id_user', $userId, PDO::PARAM_INT);
        $statement->bindParam(':token', $token, PDO::PARAM_STR);
        $statement->bindParam(':expiration', $expiration);

        return $statement->execute();
    }
}
