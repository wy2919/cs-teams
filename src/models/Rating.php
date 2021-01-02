<?php


class Rating
{
    private ?int $id;
    private int $userId;
    private int $userRatingId;
    private float $skills;
    private float $friendliness;
    private float $communication;

    public function __construct(?int $id, int $userId, int $userRatingId, float $skills, float $friendliness, float $communication)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->userRatingId = $userRatingId;
        $this->skills = $skills;
        $this->friendliness = $friendliness;
        $this->communication = $communication;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserRatingId(): int
    {
        return $this->userRatingId;
    }

    public function setUserRatingId(int $userRatingId): void
    {
        $this->userRatingId = $userRatingId;
    }

    public function getSkills(): float
    {
        return $this->skills;
    }

    public function setSkills(float $skills): void
    {
        $this->skills = $skills;
    }

    public function getFriendliness(): float
    {
        return $this->friendliness;
    }

    public function setFriendliness(float $friendliness): void
    {
        $this->friendliness = $friendliness;
    }

    public function getCommunication(): float
    {
        return $this->communication;
    }

    public function setCommunication(float $communication): void
    {
        $this->communication = $communication;
    }
}