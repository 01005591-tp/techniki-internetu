<?php

namespace p1\core\domain\audit;

class AuditableObject {
  private int $creationDate;
  private int $updateDate;
  private string $updatedBy;

  public function __construct(int    $creationDate,
                              int    $updateDate,
                              string $updatedBy) {
    $this->creationDate = $creationDate;
    $this->updateDate = $updateDate;
    $this->updatedBy = $updatedBy;
  }

  public function creationDate(): int {
    return $this->creationDate;
  }

  public function updateDate(): int {
    return $this->updateDate;
  }

  public function updatedBy(): string {
    return $this->updatedBy;
  }
}