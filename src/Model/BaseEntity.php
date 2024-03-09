<?php

namespace Model;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Model\User;
use Model\Role;
use Model\Permission;


abstract class BaseEntity {

    protected $entityManager;
    protected $role;
    protected $permission;


    public function __construct(){

        global $entityManager;
        $this->entityManager = $entityManager;
        
        $this->role = new Role();
        $this->permission = new Permission();
    }

    abstract public function getId();


    // Create/update operation
    public function save() {
        if ($this->isNew()) {
            $this->preCreate();
            // Permission check for entity creation
            $userRoles = $_SESSION['user_roles'];
            if(!$this->permission->canCreateUser($userRoles)) {
                throw new \Exception('Insufficient permissions for entity creation.');
            }
        } else {
            $this->preUpdate();
            // Permission check for entity update
            $userRoles = $_SESSION['user_roles'];
            if(!$this->permission->canUpdateUser($userRoles)) {
                throw new \Exception('Insufficient permissions for entity creation.');
            }
        }
        
        $this->entityManager->persist($this);
        $this->entityManager->flush();
        $this->postSave();
    }

    // Delete operation
    public function delete() {
        $this->preDelete();
        // Permission check for entity deletion
        $userRoles = $_SESSION['user_roles'];
        if(!$this->permission->canDeleteUser($userRoles)) {
            throw new \Exception('Insufficient permissions for entity creation.');
        }
        
        $this->entityManager->remove($this);
        $this->entityManager->flush();
        return $this;
    }

    public function isNew(): bool {
        $id = $this->getId();
        return !isset($id);
    }

    protected function preCreate(): void {
        // Implementation before creating the entity
        // To perform permission checks
        $permissionCount = $this->entityManager->getRepository(Permission::class)
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.uid = :uid')
            ->setParameter('id', 'create_user')
            ->setParameter('uid', $this->getId())
            ->getQuery()
            ->getSingleScalarResult();

        if ($permissionCount === 0) {
            throw new \Exception('Insufficient permissions for entity creation.');
        }
    }

    public function preUpdate() {
        $permissionCount = $this->entityManager->getRepository(Permission::class)
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.uid = :uid')
            ->setParameter('id', 'update_user')
            ->setParameter('uid', $this->getId())
            ->getQuery()
            ->getSingleScalarResult();

        if ($permissionCount === 0) {
            throw new \Exception('Insufficient permissions for entity creation.');
        }
    }

    protected function postSave(): void {
        // Default post-save actions
        //parent::postSave();
    
        // Post saving, by default adding the role of "std-user" to every user. (standard user)
        $role = $this->entityManager->getRepository(Role::class)->findOneBy(['name' => 'std-user']);
    
        if (!$role) {
            throw new \Exception('Role "std-user" not found.');
        }
    
        // Assign the role
        $this->permission->setRole($role);
    
        // Grant specific permissions to the user
        $this->grantPermissions();
    }

    protected function preDelete(): void {
        $permissionCount = $this->entityManager->getRepository(Permission::class)
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.uid = :uid')
            ->setParameter('id', 'delete_user') 
            ->setParameter('uid', $this->getId())
            ->getQuery()
            ->getSingleScalarResult();
    
        if ($permissionCount === 0) {
            throw new \Exception('Insufficient permissions for entity deletion.');
        }
    }

    protected function grantPermissions(): void {
        $permissions = $this->permission->getRole()->getPermissions();
        $userRoles = $_SESSION['user_roles'];
        // Grant specific permissions
        foreach ($permissions as $permission) {
            switch ($permission->getName()) {
                case 'create_user':
                    if ($permission->canCreateUser($userRoles)) {
                        $permission = new Permission();
                        $permission->setName('create_user');
                        $this->role->addPermission($permission);
                    }
                    break;
                case 'update_user':
                    if ($permission->canUpdateUser($userRoles)) {
                        $permission = new Permission();
                        $permission->setName('update_user');
                        $this->role->addPermission($permission);
                    }
                    break;
                case 'delete_user':
                    if ($permission->canDeleteUser($userRoles)) {
                        $permission = new Permission();
                        $permission->setName('delete_user');
                        $this->role->addPermission($permission);
                    }
                    break;
                case 'create_role':
                    if ($permission->canCreateRole($userRoles)) {
                        $permission = new Permission();
                        $permission->setName('create_role');
                        $this->role->addPermission($permission);
                    }
                    break;
                case 'update_role':
                    if ($permission->canUpdateRole($userRoles)) {
                        $permission = new Permission();
                        $permission->setName('update_role');
                        $this->role->addPermission($permission);
                    }
                    break;
                case 'delete_role':
                    if ($permission->canDeleteRole($userRoles)) {
                        $permission = new Permission();
                        $permission->setName('delete_role');
                        $this->role->addPermission($permission);
                    }
                    break;
                default:
                    break;
            }
        }
    }

}


?>