<?php

namespace p1\core\database\book;

require_once "core/function/either.php";
require_once "core/domain/book/book-list-repository.php";
require_once "core/domain/book/book-list.php";
require_once "core/domain/book/get-book-list-command.php";

use L;
use p1\core\domain\book\BookList;
use p1\core\domain\book\BookListEntry;
use p1\core\domain\book\BookListRepository;
use p1\core\domain\book\GetBookListCommand;
use p1\core\domain\Failure;
use p1\core\function\Either;

class BookListDbRepository implements BookListRepository {
  private FindDefaultBookListQuery $findDefaultBookListQuery;

  public function __construct(FindDefaultBookListQuery $findDefaultBookListQuery) {
    $this->findDefaultBookListQuery = $findDefaultBookListQuery;
  }

  function findDefaultBookList(GetBookListCommand $command): Either {
    $offset = ($command->page() - 1) * $command->pageSize();
    $books = $this->findDefaultBookListQuery->query($offset, $command->pageSize());
    if ($books->booksCount() === 0) {
      return Either::ofLeft(Failure::of(L::main_home_book_list_get_empty_result));
    } else {
      $bookEntries = array();
      foreach ($books->books() as $book) {
        $bookEntries[] = new BookListEntry($book->id(),
          $book->nameId(),
          $book->isbn(),
          $book->title(),
          $book->imageUri(),
          $book->description(),
          $book->state(),
          $book->language()
        );
      }
      return Either::ofRight(new BookList($bookEntries, $books->booksCount()));
    }
  }
}