<?php


require_once __DIR__ . '/../models/Message.php';
class Conversation
{
    private int $id;
    private int $userId;
    private string $username;
    private string $image;

    public function __construct(int $id, int $userId, string $username, string $image)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->username = $username;
        $this->image = $image;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
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

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }
}
