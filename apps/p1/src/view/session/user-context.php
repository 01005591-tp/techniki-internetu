<?php

namespace p1\view\session;

class UserContext
{
    private string $userId;
    private string $userEmail;
    private string $userLang;
    private array $userRoles;

    public function __construct(string $userId, string $userEmail, string $userLang, array $userRoles)
    {
        $this->userId = $userId;
        $this->userEmail = $userEmail;
        $this->userLang = $userLang;
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

    public function userLang(): string
    {
        return $this->userLang;
    }

    public function userRoles(): array
    {
        return $this->userRoles;
    }
}