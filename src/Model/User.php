<?php

namespace Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private $id; // Updated property name

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
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")] // Updated join
    #[ORM\InverseJoinColumn(name: "role_id", referencedColumnName: "id", unique: TRUE)]
    #[ORM\ManyToMany(targetEntity: "Model\Role")]
    private $roles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: "Model\Session")]
    private $sessions;

    protected $entityManager;

    // Getters and Setters

    public function getId(): int // Updated getter method name
    {
        return $this->id;
    }

    public function setId(string $id): void // Updated setter method name
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

    public function createUser($name, $email, $age, $password, $profilePhoto = null): User
    {
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setAge($age);
        $user->setPassword($password);
        $user->setProfilePhoto($profilePhoto);
        $user->
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function readUser($id): User
    {
        // Retrieve the user by ID from the database
        return $this->entityManager->getRepository(User::class)->find($id);
    }

    public function updateUser($id, $name, $email, $age, $password, $profilePhoto = null): User
    {
        // Retrieve the user by ID
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw new \Exception("User not found");
        }

        // Update
        $user->setName($name);
        $user->setEmail($email);
        $user->setAge($age);
        $user->setPassword($password);
        $user->setProfilePhoto($profilePhoto);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser($id): void
    {
        // Retrieve the user by ID
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw new \Exception("User not found");
        }

        // Remove
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }


}
