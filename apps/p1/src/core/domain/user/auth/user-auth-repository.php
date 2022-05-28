<?php

namespace p1\core\domain\user\auth;

interface UserAuthRepository
{
    public function findUserRoles(string $id): array;
}