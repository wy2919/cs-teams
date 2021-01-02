<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/Rating.php';

class RatingRepository extends Repository
{

    public function addRating(Rating $rating): bool
    {
        $existsStatement = $this->database->connect()->prepare(
            'SELECT 1 FROM public.ratings WHERE id_user = :id_user AND id_user_rating = :id_user_rating'
        );

        $existsStatement->execute([$rating->getUserId(), $rating->getUserRatingId()]);

        $rows = $existsStatement->fetchAll();
        if(count($rows) != 0) {
            return false;
        }

        $statement = $this->database->connect()->prepare(
            'INSERT INTO public.ratings (id_user, id_user_rating, skills, friendliness, communication)
                        Values (?, ?, ?, ?, ?)'
        );

        return $statement->execute([
            $rating->getUserId(),
            $rating->getUserRatingId(), // it can be read from session
            $rating->getSkills(),
            $rating->getFriendliness(),
            $rating->getCommunication()
        ]);
    }

    public function  getUserElo(int $id) {
        $statement = $this->database->connect()->prepare(
            'SELECT skills, friendliness, communication FROM public.ratings WHERE id_user = :id_user'
        );
        $statement->bindParam(':id_user', $id, PDO::PARAM_STR);
        $statement->execute();
        $rows = $statement->fetchAll();

        if (count($rows) == 0) {
            return 'Unknown';
        }

        $skills = 0;
        $friendliness = 0;
        $communication = 0;

        foreach ($rows as $row){
            $skills += $row['skills'];
            $friendliness += $row['friendliness'];
            $communication += $row['communication'];
        }
        return (0.33 * $skills + 0.37 * $friendliness + 0.3 * $communication)/count($rows);
    }
}