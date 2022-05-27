<?php

namespace p1\view\login\signup;

class SignUpRequest
{
    private string $email;
    private string $password;
    private string $passwordRepeat;

    public function __construct(string $email, string $password, string $passwordRepeat)
    {
        $this->email = $email;
        $this->password = $password;
        $this->passwordRepeat = $passwordRepeat;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function passwordRepeat(): string
    {
        return $this->passwordRepeat;
    }
}
