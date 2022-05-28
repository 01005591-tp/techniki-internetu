<?php

namespace p1\core\domain\user\auth;

class AuthenticateUserResult
{
    private string $userId;
    private string $userEmail;
    private array $userRoles;

    public function __construct(string $userId, string $userEmail, array $userRoles)
    {
        $this->userId = $userId;
        $this->userEmail = $userEmail;
        $this->userRoles = $userRoles;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function userEmail(): string
    {
        return $this->userEmail;
    }

    public function userRoles(): array
    {
        return $this->userRoles;
    }
}