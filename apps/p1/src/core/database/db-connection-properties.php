<?php

namespace p1\core\database;
class DbConnectionProperties {
  private string $url;
  private string $name;
  private string $user;
  private string $pass;

  public function __construct() {
    $this->url = getenv("DATABASE_URL");
    $this->name = getenv("DATABASE_NAME");
    $this->user = getenv("DATABASE_USER");
    $this->pass = getenv("DATABASE_PASS");
  }

  public function url(): string {
    return $this->url;
  }

  public function name(): string {
    return $this->name;
  }

  public function user(): string {
    return $this->user;
  }

  public function pass(): string {
    return $this->pass;
  }
}