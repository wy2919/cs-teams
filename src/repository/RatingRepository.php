<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/Rating.php';

class RatingRepository extends Repository
{

    public function addRating(Rating $rating): void
    {

        $statement = $this->database->connect()->prepare(
            'INSERT INTO public.ratings (id_user, id_user_rating, skills, friendliness, communication)
                        Values (?, ?, ?, ?, ?)'
        );

        $statement->execute([
            $rating->getUserId(),
            $rating->getUserRatingId(), // it can be read from session
            $rating->getSkills(),
            $rating->getFriendliness(),
            $rating->getCommunication()
        ]);
    }
}