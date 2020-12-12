<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/Rank.php';

class RankRepository extends Repository
{
    public function getRank(int $id): ?Rank
    {
        $statement = $this->database->connect()->prepare('
            SELECT * FROM public.ranks WHERE id = :id;
        ');

        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $rank = $statement->fetch(PDO::FETCH_ASSOC);    // association array

        if($rank == false){
            return null;    // we should throw exception instead of return null and handle it in place where we run this fun
        }

        return new Rank(
            $rank['id'],
            $rank['rank'],
            $rank['img'],
        );
    }

    public function getRanks()
    {
        $statement = $this->database->connect()->prepare(
            'SELECT * FROM public.ranks'
        );
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($result == false){
            return null;
        }

        $ranks = array();
        foreach($result as $rank){
           $ranks[] = new Rank(
                $rank['id'],
                $rank['rank'],
                $rank['img'],
            );
        }
        return $ranks;
    }
}