<?php

namespace p1\core\database\user;

class UserEntity
{
    private ?string $id;
    private string $email;
    private string $password;

    private function __construct(?string $id, string $email, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
    }

    public function id(): ?string
    {
        return $this->id;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public static function newUser(string $email, string $password): UserEntity
    {
        return new UserEntity(null, $email, $password);
    }

    public static function existingUser(string $id, string $email, string $password): UserEntity
    {
        return new UserEntity($id, $email, $password);
    }
}