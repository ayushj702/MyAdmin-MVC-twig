<?php

namespace Model;

abstract class BasePermission
{
    protected $currentUser;

    public function __construct()
    {
        $this->currentUser = new User();
    }

    abstract public function applyRole(mixed $value): bool;
    abstract public function applyAuth(mixed $value): bool;

}
