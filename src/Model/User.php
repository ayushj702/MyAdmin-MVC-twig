<?php

namespace Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

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

    #[ORM\JoinTable(name: 'users_roles')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "role_id", referencedColumnName: "id", unique: TRUE)]
    #[ORM\ManyToMany(targetEntity: "Model\Role")]
    private $roles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: "Model\Session")]
    private $sessions;
    //pivot table schema
    // Getters and Setters

    private function __construct(){
        $this->roles = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    

    public function getName(): string
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

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function setRoles(Collection $roles): void
    {
        $this->roles = $roles;
    }

    public function setRole(Role $roles): void
    {
        $this->roles[] = $roles;
    }

    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): void
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setUser($this);
        }
    }

    public function hasRole($roleName): bool
    {
        foreach ($this->roles as $role) {
            if ($role->getName() === $roleName) {
                return true;
            }
        }
        return false;
    }
}
