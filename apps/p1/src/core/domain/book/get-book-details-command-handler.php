<?php

namespace p1\core\domain\book;

require_once "core/domain/book/book-details-repository.php";
require_once "core/domain/book/get-book-details-command.php";
require_once "core/function/option.php";

use p1\core\function\Option;

class GetBookDetailsCommandHandler {
  private BookDetailsRepository $bookDetailsRepository;

  public function __construct(BookDetailsRepository $bookDetailsRepository) {
    $this->bookDetailsRepository = $bookDetailsRepository;
  }

  public function handle(GetBookDetailsCommand $command): Option {
    return $this->bookDetailsRepository->findBookDetails($command);
  }
}

