<?php

namespace p1\core\domain\book\edit;

require_once "core/function/either.php";
require_once "core/domain/book/edit/save-book-command.php";
require_once "core/domain/book/book-details-repository.php";
require_once "core/domain/failure.php";

use p1\core\domain\book\BookDetailsRepository;
use p1\core\domain\Failure;
use p1\core\function\Either;
use p1\core\function\FunctionUtils;

class SaveBookCommandHandler {
  private BookDetailsRepository $bookDetailsRepository;

  public function __construct(BookDetailsRepository $bookDetailsRepository) {
    $this->bookDetailsRepository = $bookDetailsRepository;
  }

  public function save(SaveBookCommand $command): Either {
    return $this->bookDetailsRepository->save($command)
      ->mapLeft(FunctionUtils::function2OfClosure(
        fn($failure) => SaveBookError::saveBookError($failure->message(), $command, $failure)
      ));
  }
}

class SaveBookError extends Failure {
  private SaveBookCommand $command;
  private ?Failure $cause;

  public function __construct(string $message, SaveBookCommand $command, ?Failure $cause = null) {
    parent::__construct($message);
    $this->command = $command;
    $this->cause = $cause;
  }

  public function command(): SaveBookCommand {
    return $this->command;
  }

  public function cause(): ?Failure {
    return $this->cause;
  }

  public static function saveBookError(string $message, SaveBookCommand $command, ?Failure $cause = null): SaveBookError {
    return new SaveBookError($message, $command, $cause);
  }
}