<?php

require_once __DIR__ . '/../models/Rank.php';

class UserDto
{
    private ?int $id;
    private string $email;
    private string $username;
    private ?string $image;
    private ?string $description;
    private int $role;
    private string $rank;
    private ?float $elo;

    public function __construct(?int $id, string $email, string $username, ?string $image, ?string $description, $role, string $rank, ?float $elo)
    {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->description = $description;
        $this->role = $role;
        $this->image = $image;
        $this->rank = $rank;
        $this->elo = $elo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getDescription()
    {
        if($this->description == null){
            return "description not specified.";
        }
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getRole(): int
    {
        return $this->role;
    }

    public function setRole(int $role): void
    {
        $this->role = $role;
    }

    public function getRank(): string
    {
        return $this->rank;
    }

    public function setRank(string $rank): void
    {
        $this->rank = $rank;
    }

    public function getElo()
    {
        if($this->elo == null){
            return "Unknown";
        }
        return $this->elo;
    }

    public function setElo(?float $elo): void
    {
        $this->elo = $elo;
    }


}