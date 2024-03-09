<?php

namespace Model;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Yaml\Yaml;

#[ORM\Entity]
#[ORM\Table(name: 'permissions')]
class Permission extends BasePermission
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string')]
    private $name;

    #[ORM\ManyToOne(targetEntity: "Model\Role", inversedBy: "permissions")]
    #[ORM\JoinColumn(name: "role_id", referencedColumnName: "id")]
    private $role;


    public function applyRole(mixed $value): bool
    {
        $requiredRole = $this->getRequiredRole($value);

        // If required role is not specified, allow access
        if (!$requiredRole) {
            return true;
        }
        
        $userRoles = $this->getUserRoles(); 

        return $this->hasRole($userRoles, $requiredRole);
    }

    public function applyAuth(mixed $value): bool
    {
        $authRequired = $this->isAuthRequired($value);

        if (!$authRequired) {
            return true;
        }
       
        $userRoles = $this->getUserRoles(); 
        return !empty($userRoles);
    }

    private function getUserRoles(): array
    {
        return $_SESSION['user_roles'];
    }


    private function isAuthRequired(string $routeName): bool
    {
        $routes = Yaml::parseFile(__DIR__ . '/../Config/Routes.yml');

        return isset($routes['web'][$routeName]['permission']['auth']) 
            && $routes['web'][$routeName]['permission']['auth'] === true;
    }

    private function getRequiredRole(string $routeName): ?string
    {
        $routes = Yaml::parseFile(__DIR__ . '/../Config/Routes.yml');

        return $routes['web'][$routeName]['permission']['role'] ?? null;
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


    public function canCreateUser($userRoles): bool
    {
        return $this->hasRole($userRoles, 'administrator');
    }

    public function canReadUser(): bool
    {
        return true;
    }

    public function canUpdateUser($userRoles): bool
    {
        return $this->hasRole($userRoles, 'administrator');
    }

    public function canDeleteUser($userRoles): bool
    {
        return $this->hasRole($userRoles, 'administrator');
    }

    public function canCreateRole($userRoles): bool
    {
        return $this->hasRole($userRoles, 'administrator');
    }

    public function canReadRole(): bool
    {
        return true;
    }

    public function canUpdateRole($userRoles): bool
    {
        return $this->hasRole($userRoles, 'administrator');
    }

    public function canDeleteRole($userRoles): bool
    {
        return $this->hasRole($userRoles, 'administrator');
    }

    public function hasRole($userRoles, $roleName): bool
    {
        return in_array($roleName, $userRoles);
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;
        return $this;
    }
    
}
