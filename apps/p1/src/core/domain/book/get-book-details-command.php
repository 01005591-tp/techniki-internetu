<?php

namespace p1\core\domain\book;

class GetBookDetailsCommand {
  private string $nameId;

  public function __construct(string $nameId) {
    $this->nameId = $nameId;
  }

  public function nameId(): string {
    return $this->nameId;
  }
}