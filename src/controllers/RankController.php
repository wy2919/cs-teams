<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/RankRepository.php';

class RankController extends AppController
{
    private RankRepository $rankRepository;

    public function __construct()
    {
        parent::__construct();
        $this->rankRepository = new RankRepository();
    }

}