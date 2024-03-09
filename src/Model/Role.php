<?php

namespace Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;


#[ORM\Entity]
#[ORM\Table(name: 'roles')]
class Role
{

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string')]
    private $name;

    private $user;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: "Model\Permission")]
    private $permissions;

    protected $entityManager;

    public function __construct() {
        $this->permissions = new ArrayCollection();
        global $entityManager;
        $this->entityManager = $entityManager;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void{
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

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
            $permission->setRole($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->contains($permission)) {
            $this->permissions->removeElement($permission);
            // set the owning side to null (unless already changed)
            if ($permission->getRole() === $this) {
                $permission->setRole(null);
            }
        }

        return $this;
    }

    public function createRole($name): Role
    {
        $role = new Role();
        $role->setName($name);
        $this->entityManager->persist($role);
        $this->entityManager->flush();

        return $role;
    }

    public function readRole($id): ?Role
    {
        return $this->entityManager->getRepository(Role::class)->find($id);
    }

    public function updateRole($id, $name): Role
    {
        // Retrieve the role by ID
        $role = $this->entityManager->getRepository(Role::class)->find($id);
        if (!$role) {
            throw new \Exception("Role not found");
        }

        // Update
        $role->setName($name);
        $this->entityManager->persist($role);
        $this->entityManager->flush();

        return $role;
    }

    public function deleteRole($id): void
    {
        // Retrieve the role by ID
        $role = $this->entityManager->getRepository(Role::class)->find($id);
        if (!$role) {
            throw new \Exception("Role not found");
        }
        $this->entityManager->remove($role);
        $this->entityManager->flush();
    }

}
