<?php


class RankMapper
{
    public function mapAssocToRank($record)
    {
        if ($record == false) {
            return null;
        }

        return new Rank(
            $record['id'],
            $record['rank']
        );
    }

    public function mapMultipleAssocToRank($records)
    {
        if ($records == false) {
            return null;
        }

        $ranks = array();
        foreach ($records as $record) {
            $ranks[] = $this->mapAssocToRank($record);
        }
        return $ranks;
    }
}