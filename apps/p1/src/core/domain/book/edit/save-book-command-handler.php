<?php

namespace p1\core\domain\book\edit;

require_once "core/function/either.php";
require_once "core/domain/book/edit/save-book-command.php";
require_once "core/domain/failure.php";

use p1\core\domain\Failure;
use p1\core\function\Either;

class SaveBookCommandHandler {

  public function save(SaveBookCommand $command): Either {
    return Either::ofLeft(SaveBookError::saveBookError("Error tada", $command));
  }
}

class SaveBookError extends Failure {
  private SaveBookCommand $command;

  public function __construct(string $message, SaveBookCommand $command) {
    parent::__construct($message);
    $this->command = $command;
  }

  public function command(): SaveBookCommand {
    return $this->command;
  }

  public static function saveBookError(string $message, SaveBookCommand $command): SaveBookError {
    return new SaveBookError($message, $command);
  }
}