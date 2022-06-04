<?php

namespace p1\core\domain\user;

class CreateUserCommand {
  private string $email;
  private string $password;

  public function __construct(string $email, string $password) {
    $this->email = $email;
    $this->password = $password;
  }


  public function email(): string {
    return $this->email;
  }

  public function password(): string {
    return $this->password;
  }

  public function withPassword(string $password): CreateUserCommand {
    return new CreateUserCommand($this->email, $password);
  }
}