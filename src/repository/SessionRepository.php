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
        $record = $statement->fetch();

        return count($record) !== 0;
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

    public function createSession($userId, $token, $expiration)
    {
        if( $this->isSessionCreated($userId)) {
            $statement = $this->database->connect()->prepare('
            UPDATE public.sessions 
                SET 
                    token = :token,
                    expiration = :expiration
            WHERE id_user = :id_user
        ');
        } else {
            $statement = $this->database->connect()->prepare('
            INSERT INTO public.sessions
                (id_user, token, expiration)
                VALUES (:id_user, :token, :expiration)
                ');
        }
        $expiration = date("Y-m-d H:i:s", $expiration);
        $statement->bindParam(':id_user', $userId, PDO::PARAM_INT);
        $statement->bindParam(':token', $token, PDO::PARAM_STR);
        $statement->bindParam(':expiration', $expiration);

        return $statement->execute();
    }

    private function isSessionCreated($userId)
    {
        $statement = $this->database->connect()->prepare('
            SELECT id
            FROM public.sessions 
            WHERE id_user = :id;
        ');

        $statement->execute([$userId]);
        $rows = $statement->fetchAll();
        return count($rows) !== 0;
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
}