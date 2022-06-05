<?php

namespace p1\core\domain\book;

class BookPieces {
  private array $pieces;
  private array $stateCounts;

  public function __construct(array $pieces, array $stateCounts) {
    $this->pieces = $pieces;
    $this->stateCounts = $stateCounts;
  }

  public function pieces(): array {
    return $this->pieces;
  }

  public function stateCounts(): array {
    return $this->stateCounts;
  }
}