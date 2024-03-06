<?php

namespace Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sessions')]
class Session
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 128)]
    private $id;

    #[ORM\Column(type: 'text')]
    private $data;

    #[ORM\Column(type: 'integer')]
    private $userId;

    // Reference to the User entity
    #[ORM\ManyToOne(targetEntity: "User")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private $user;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
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

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}

