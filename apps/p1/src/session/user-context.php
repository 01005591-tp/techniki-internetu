<?php

namespace p1\session;

class UserContext {
  private string $userId;
  private string $userEmail;
  private string $userLang;
  private array $userRoles;

  public function __construct(string $userId, string $userEmail, string $userLang, array $userRoles) {
    $this->userId = $userId;
    $this->userEmail = $userEmail;
    $this->userLang = $userLang;
    $this->userRoles = $userRoles;
  }

  public function userId(): string {
    return $this->userId;
  }

  public function userEmail(): string {
    return $this->userEmail;
  }

  public function userLang(): string {
    return $this->userLang;
  }

  public function userRoles(): array {
    return $this->userRoles;
  }

  public function hasAnyRole(string $role, ...$roles): bool {
    $allRoles = func_get_args();
    foreach ($allRoles as $role) {
      if (in_array($role, $this->userRoles)) {
        return true;
      }
    }
    return false;
  }

  public function isEmpty(): bool {
    return empty($this->userId);
  }

  public function isDefined(): bool {
    return !$this->isEmpty();
  }

  public function __toString(): string {
    return "{userId: " . $this->userId .
      ", userEmail: " . $this->userEmail .
      ", userLang: " . $this->userLang .
      ", userRoles: " . implode(',', $this->userRoles) . "}";
  }

  public static function emptyUserContext(): UserContext {
    return new UserContext(
      "",
      "",
      "en",
      []
    );
  }
}