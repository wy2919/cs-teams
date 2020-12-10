<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/RatingRepository.php';

class RatingController extends AppController
{
    private RatingRepository $ratingRepository;

    public function __construct()
    {
        parent::__construct();
        $this->ratingRepository = new RatingRepository();
    }

}