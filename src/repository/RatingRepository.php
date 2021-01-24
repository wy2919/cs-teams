<?php

require_once 'Repository.php';
require_once __DIR__ . '/../models/Rating.php';

class RatingRepository extends Repository
{

    public function addRating(Rating $rating): bool
    {
        $existsStatement = $this->database->connect()->prepare('
            SELECT 1 
            FROM public.ratings 
            WHERE id_user = :id_user 
              AND id_user_rating = :id_user_rating'
        );

        $existsStatement->execute([$rating->getUserId(), $rating->getUserRatingId()]);

        $rows = $existsStatement->fetchAll();
        if (count($rows) != 0) {
            return false;
        }

        $statement = $this->database->connect()->prepare('
            INSERT INTO 
                public.ratings (id_user, id_user_rating, skills, friendliness, communication)
                        Values (?, ?, ?, ?, ?)'
        );

        return $statement->execute([
            $rating->getUserId(),
            $rating->getUserRatingId(),
            $rating->getSkills(),
            $rating->getFriendliness(),
            $rating->getCommunication()
        ]);
    }
}