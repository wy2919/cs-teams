<?php


class User
{
    private ?int $id;
    private string $email;
    private string $username;
    private ?string $password;
    private ?string $image;
    private ?DateTime $createdAt;
    private int $idRank;
    private ?int $idUserDetails;

    public function __construct(?int $id, string $email, string $username, ?string $password, ?string $image, ?string $createdAt, int $idRank, ?int $idUserDetails)
    {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->image = $image;
        $this->createdAt = $createdAt ? new DateTime($createdAt) : null;
        $this->idRank = $idRank;
        $this->idUserDetails = $idUserDetails;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage($image): void
    {
        $this->image = $image;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getIdRank(): int
    {
        return $this->idRank;
    }

    public function setIdRank(int $idRank): void
    {
        $this->idRank = $idRank;
    }

    public function getIdUserDetails(): ?int
    {
        return $this->idUserDetails;
    }

    public function setIdUserDetails(int $idUserDetails): void
    {
        $this->idUserDetails = $idUserDetails;
    }



}