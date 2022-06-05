<?php

namespace p1\core\domain\book;

require_once "core/domain/audit/auditable-object.php";

use p1\core\domain\audit\AuditableObject;

class Author {
  private string $id;
  private string $firstName;
  private string $lastName;
  private ?string $biographyNote;
  private ?int $birthYear;
  private ?int $birthMonth;
  private ?int $birthDay;
  private int $priority;
  private ?AuditableObject $auditableObject;
  private ?int $version;

  public function __construct(string           $id,
                              string           $firstName,
                              string           $lastName,
                              ?string          $biographyNote,
                              ?int             $birthYear,
                              ?int             $birthMonth,
                              ?int             $birthDay,
                              int              $priority,
                              ?AuditableObject $auditableObject,
                              ?int             $version) {
    $this->id = $id;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->biographyNote = $biographyNote;
    $this->birthYear = $birthYear;
    $this->birthMonth = $birthMonth;
    $this->birthDay = $birthDay;
    $this->priority = $priority;
    $this->auditableObject = $auditableObject;
    $this->version = $version;
  }

  public function id(): string {
    return $this->id;
  }

  public function firstName(): string {
    return $this->firstName;
  }

  public function lastName(): string {
    return $this->lastName;
  }

  public function biographyNote(): ?string {
    return $this->biographyNote;
  }

  public function birthYear(): ?int {
    return $this->birthYear;
  }

  public function birthMonth(): ?int {
    return $this->birthMonth;
  }

  public function birthDay(): ?int {
    return $this->birthDay;
  }

  public function priority(): int {
    return $this->priority;
  }

  public function auditableObject(): ?AuditableObject {
    return $this->auditableObject;
  }

  public function version(): ?int {
    return $this->version;
  }
}