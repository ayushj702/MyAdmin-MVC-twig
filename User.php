<?php

namespace Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string')]
    private $name;

    #[ORM\Column(type: 'string', unique: true)]
    private $email;

    #[ORM\Column(type: 'integer')]
    private $age;

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', nullable: true)]
    private $profilePhoto;

     #[ORM\OneToMany(targetEntity: "Role", mappedBy: "User")]
     #[ORM\JoinColumn(name: "role_id", referencedColumnName: "id")]
    private $roles;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = md5($password);

        return $this;
    }

    public function getProfilePhoto(): ?string
    {
        return $this->profilePhoto;
    }

    public function setProfilePhoto(?string $profilePhoto): self
    {
        $this->profilePhoto = $profilePhoto;

        return $this;
    }

    public function getRoles(): Role
    {
        return $this->roles;
    }

    public function setRoles(Role $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
