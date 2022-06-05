<?php

namespace p1\core\database\book;

require_once "core/database/book/find-authors-by-book-name-id-query.php";
require_once "core/database/book/find-book-by-name-id-query.php";
require_once "core/database/book/find-book-pieces-by-book-name-id-query.php";
require_once "core/database/book/find-book-tags-by-book-name-id-query.php";
require_once "core/database/book/find-publisher-by-book-name-id-query.php";

require_once "core/domain/book/author.php";
require_once "core/domain/book/book.php";
require_once "core/domain/book/book-author.php";
require_once "core/domain/book/book-details.php";
require_once "core/domain/book/book-details-repository.php";
require_once "core/domain/book/book-piece.php";
require_once "core/domain/book/book-pieces.php";
require_once "core/domain/book/book-state.php";
require_once "core/domain/book/book-tag.php";
require_once "core/domain/book/publisher.php";
require_once "core/domain/book/get-book-details-command.php";
require_once "core/function/either.php";
require_once "core/function/function.php";

use p1\core\domain\book\Book;
use p1\core\domain\book\BookDetails;
use p1\core\domain\book\BookDetailsRepository;
use p1\core\domain\book\BookPieces;
use p1\core\domain\book\GetBookDetailsCommand;
use p1\core\domain\book\Publisher;
use p1\core\function\Function2;
use p1\core\function\Option;
use p1\core\function\Supplier;

class BookDetailsDbRepository implements BookDetailsRepository {
  private FindBookByNameIdQuery $findBookByNameIdQuery;
  private FindPublisherByBookNameIdQuery $findPublisherByBookNameIdQuery;
  private FindAuthorsByBookNameIdQuery $findAuthorsByBookNameIdQuery;
  private FindBookPiecesByBookNameIdQuery $findBookPiecesByBookNameIdQuery;
  private FindBookTagsByBookNameIdQuery $findBookTagsByBookNameIdQuery;

  public function __construct(FindBookByNameIdQuery           $findBookByNameIdQuery,
                              FindPublisherByBookNameIdQuery  $findPublisherByBookNameIdQuery,
                              FindAuthorsByBookNameIdQuery    $findAuthorsByBookNameIdQuery,
                              FindBookPiecesByBookNameIdQuery $findBookPiecesByBookNameIdQuery,
                              FindBookTagsByBookNameIdQuery   $findBookTagsByBookNameIdQuery) {
    $this->findBookByNameIdQuery = $findBookByNameIdQuery;
    $this->findPublisherByBookNameIdQuery = $findPublisherByBookNameIdQuery;
    $this->findAuthorsByBookNameIdQuery = $findAuthorsByBookNameIdQuery;
    $this->findBookPiecesByBookNameIdQuery = $findBookPiecesByBookNameIdQuery;
    $this->findBookTagsByBookNameIdQuery = $findBookTagsByBookNameIdQuery;
  }

  function findBookDetails(GetBookDetailsCommand $command): Option {
    return $this->findBookByNameIdQuery->query($command->nameId())
      ->map(new class implements Function2 {
        function apply($value): BookDetailsBuilderBook {
          return new BookDetailsBuilderBook($value);
        }
      })
      // TODO: queries bellow could be run simultaneously
      ->map(new BookDetailsFindPublisherFunction(
        $command,
        $this->findPublisherByBookNameIdQuery
      ))
      ->map(new BookDetailsFindAuthorsFunction(
        $command,
        $this->findAuthorsByBookNameIdQuery
      ))
      ->map(new BookDetailsFindBookPiecesFunction(
        $command,
        $this->findBookPiecesByBookNameIdQuery
      ))
      ->map(new BookDetailsFindTagsFunction(
        $command,
        $this->findBookTagsByBookNameIdQuery
      ))
      ->fold(new class implements Supplier {
        function supply(): Option {
          return Option::none();
        }
      }, new class implements Function2 {
        function apply($value) {
          return Option::of($value);
        }
      });
  }
}

class BookDetailsFindPublisherFunction implements Function2 {

  private GetBookDetailsCommand $command;
  private FindPublisherByBookNameIdQuery $findPublisherByBookNameIdQuery;

  public function __construct(GetBookDetailsCommand          $command,
                              FindPublisherByBookNameIdQuery $findPublisherByBookNameIdQuery) {
    $this->command = $command;
    $this->findPublisherByBookNameIdQuery = $findPublisherByBookNameIdQuery;
  }

  function apply($value): BookDetailsBuilderPublisher {
    return $this->findPublisherByBookNameIdQuery->query($this->command->nameId())
      ->fold(
        new BookDetailsBuilderBookNoPublisherSupplier($value),
        new BookDetailsBuilderBookWithPublisherFunction($value)
      );
  }
}

class BookDetailsBuilderBookNoPublisherSupplier implements Supplier {
  private BookDetailsBuilderBook $builder;

  public function __construct(BookDetailsBuilderBook $builder) {
    $this->builder = $builder;
  }

  function supply(): BookDetailsBuilderPublisher {
    return $this->builder->noPublisher();
  }
}

class BookDetailsBuilderBookWithPublisherFunction implements Function2 {
  private BookDetailsBuilderBook $builder;

  public function __construct(BookDetailsBuilderBook $builder) {
    $this->builder = $builder;
  }

  function apply($value): BookDetailsBuilderPublisher {
    return $this->builder->publisher($value);
  }
}

class BookDetailsFindAuthorsFunction implements Function2 {
  private GetBookDetailsCommand $command;
  private FindAuthorsByBookNameIdQuery $findAuthorsByBookNameIdQuery;

  public function __construct(GetBookDetailsCommand        $command,
                              FindAuthorsByBookNameIdQuery $findAuthorsByBookNameIdQuery) {
    $this->command = $command;
    $this->findAuthorsByBookNameIdQuery = $findAuthorsByBookNameIdQuery;
  }

  function apply($value): BookDetailsBuilderAuthors {
    $bookAuthorsView = $this->findAuthorsByBookNameIdQuery->query($this->command->nameId());
    return $value->authors($bookAuthorsView->authors(), $bookAuthorsView->bookAuthors());
  }
}

class BookDetailsFindBookPiecesFunction implements Function2 {
  private GetBookDetailsCommand $command;
  private FindBookPiecesByBookNameIdQuery $findBookPiecesByBookNameIdQuery;

  public function __construct(GetBookDetailsCommand           $command,
                              FindBookPiecesByBookNameIdQuery $findBookPiecesByBookNameIdQuery) {
    $this->command = $command;
    $this->findBookPiecesByBookNameIdQuery = $findBookPiecesByBookNameIdQuery;
  }

  function apply($value): BookDetailsBuilderPieces {
    $bookPieces = $this->findBookPiecesByBookNameIdQuery->query($this->command->nameId());
    return $value->bookPieces($bookPieces);
  }
}

class BookDetailsFindTagsFunction implements Function2 {
  private GetBookDetailsCommand $command;
  private FindBookTagsByBookNameIdQuery $findBookTagsByBookNameIdQuery;

  public function __construct(GetBookDetailsCommand         $command,
                              FindBookTagsByBookNameIdQuery $findBookTagsByBookNameIdQuery) {
    $this->command = $command;
    $this->findBookTagsByBookNameIdQuery = $findBookTagsByBookNameIdQuery;
  }

  function apply($value) {
    $tags = $this->findBookTagsByBookNameIdQuery->query($this->command->nameId());
    return $value->bookTags($tags);
  }
}

class BookDetailsBuilderBook {
  private Book $book;

  public function __construct(Book $book) {
    $this->book = $book;
  }

  public function publisher(Publisher $publisher): BookDetailsBuilderPublisher {
    return new BookDetailsBuilderPublisher(
      $this->book,
      $publisher
    );
  }

  public function noPublisher(): BookDetailsBuilderPublisher {
    return new BookDetailsBuilderPublisher(
      $this->book,
      null
    );
  }
}

class BookDetailsBuilderPublisher {
  private Book $book;
  private ?Publisher $publisher;

  public function __construct(Book       $book,
                              ?Publisher $publisher) {
    $this->book = $book;
    $this->publisher = $publisher;
  }

  public function authors(array $authors, array $bookAuthors): BookDetailsBuilderAuthors {
    return new BookDetailsBuilderAuthors(
      $this->book,
      $this->publisher,
      $authors,
      $bookAuthors
    );
  }
}

class BookDetailsBuilderAuthors {
  private Book $book;
  private ?Publisher $publisher;
  private array $authors;
  private array $bookAuthors;

  public function __construct(Book       $book,
                              ?Publisher $publisher,
                              array      $authors,
                              array      $bookAuthors) {
    $this->book = $book;
    $this->publisher = $publisher;
    $this->authors = $authors;
    $this->bookAuthors = $bookAuthors;
  }

  public function bookPieces(BookPieces $bookPieces): BookDetailsBuilderPieces {
    return new BookDetailsBuilderPieces(
      $this->book,
      $this->publisher,
      $this->authors,
      $this->bookAuthors,
      $bookPieces
    );
  }
}

class BookDetailsBuilderPieces {
  private Book $book;
  private ?Publisher $publisher;
  private array $authors;
  private array $bookAuthors;
  private BookPieces $bookPieces;

  public function __construct(Book       $book,
                              ?Publisher $publisher,
                              array      $authors,
                              array      $bookAuthors,
                              BookPieces $bookPieces) {
    $this->book = $book;
    $this->publisher = $publisher;
    $this->authors = $authors;
    $this->bookAuthors = $bookAuthors;
    $this->bookPieces = $bookPieces;
  }

  public function bookTags(array $bookTags): BookDetails {
    return new BookDetails(
      $this->book,
      $this->publisher,
      $this->authors,
      $this->bookPieces,
      $bookTags,
      $this->bookAuthors
    );
  }
}
