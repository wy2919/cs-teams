<?php


class Rank
{
    private int $id;
    private string $rank;
    private string $img;

    public function __construct(int $id, string $rank, string $img)
    {
        $this->id = $id;
        $this->rank = $rank;
        $this->img = $img;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getRank(): string
    {
        return $this->rank;
    }

    public function setRank(string $rank): void
    {
        $this->rank = $rank;
    }

    public function getImg(): string
    {
        return $this->img;
    }

    public function setImg(string $img): void
    {
        $this->img = $img;
    }




}