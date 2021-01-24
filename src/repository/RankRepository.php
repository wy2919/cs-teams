<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/Rank.php';
require_once __DIR__.'/../mappers/RankMapper.php';

class RankRepository extends Repository
{
    private RankMapper $rankMapper;

    public function __construct()
    {
        parent::__construct();
        $this->rankMapper = new RankMapper();
    }

    public function getRanks()
    {
        $statement = $this->database->connect()->prepare('
            SELECT *
            FROM public.ranks
            ORDER BY id'
        );
        $statement->execute();
        $records = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $this->rankMapper->mapMultipleAssocToRank($records);
    }
}