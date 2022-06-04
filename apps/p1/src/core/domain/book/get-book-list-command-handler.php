<?php

namespace p1\core\domain\book;

require_once "core/function/either.php";
require_once "core/domain/book/book-list-repository.php";
require_once "core/domain/book/get-book-list-command.php";

use p1\core\function\Either;

class GetBookListCommandHandler {
  private BookListRepository $bookListRepository;

  public function __construct(BookListRepository $bookListRepository) {
    $this->bookListRepository = $bookListRepository;
  }

  public function handle(GetBookListCommand $command): Either {
    return $this->bookListRepository->findDefaultBookList($command);
  }
}