<?php

namespace p1\core\database\book;

require_once "core/database/book/find-authors-by-book-name-id-query.php";
require_once "core/database/book/find-book-by-name-id-query.php";
require_once "core/database/book/find-book-pieces-by-book-name-id-query.php";
require_once "core/database/book/find-book-tags-by-book-name-id-query.php";
require_once "core/database/book/find-publisher-by-book-name-id-query.php";
require_once "core/database/book/find-book-by-id-query.php";
require_once "core/database/book/insert-book-statement.php";
require_once "core/database/book/update-book-statement.php";

require_once "core/database/transaction/transaction.php";
require_once "core/database/transaction/transaction-manager.php";

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

use L;
use p1\core\database\transaction\Transaction;
use p1\core\database\transaction\TransactionManager;
use p1\core\domain\book\Book;
use p1\core\domain\book\BookDetails;
use p1\core\domain\book\BookDetailsRepository;
use p1\core\domain\book\BookPieces;
use p1\core\domain\book\edit\SaveBookCommand;
use p1\core\domain\book\GetBookDetailsCommand;
use p1\core\domain\book\Publisher;
use p1\core\domain\Failure;
use p1\core\function\Either;
use p1\core\function\FunctionUtils;
use p1\core\function\Option;

class BookDetailsDbRepository implements BookDetailsRepository {
  private FindBookByNameIdQuery $findBookByNameIdQuery;
  private FindPublisherByBookNameIdQuery $findPublisherByBookNameIdQuery;
  private FindAuthorsByBookNameIdQuery $findAuthorsByBookNameIdQuery;
  private FindBookPiecesByBookNameIdQuery $findBookPiecesByBookNameIdQuery;
  private FindBookTagsByBookNameIdQuery $findBookTagsByBookNameIdQuery;

  private FindBookByIdQuery $findBookByIdQuery;
  private InsertBookStatement $insertBookStatement;
  private UpdateBookStatement $updateBookStatement;
  private UpdateBookTagsStatement $updateBookTagsStatement;

  private TransactionManager $transactionManager;

  public function __construct(FindBookByNameIdQuery           $findBookByNameIdQuery,
                              FindPublisherByBookNameIdQuery  $findPublisherByBookNameIdQuery,
                              FindAuthorsByBookNameIdQuery    $findAuthorsByBookNameIdQuery,
                              FindBookPiecesByBookNameIdQuery $findBookPiecesByBookNameIdQuery,
                              FindBookTagsByBookNameIdQuery   $findBookTagsByBookNameIdQuery,
                              FindBookByIdQuery               $findBookByIdQuery,
                              InsertBookStatement             $insertBookStatement,
                              UpdateBookStatement             $updateBookStatement,
                              UpdateBookTagsStatement         $updateBookTagsStatement,
                              TransactionManager              $transactionManager) {
    $this->findBookByNameIdQuery = $findBookByNameIdQuery;
    $this->findPublisherByBookNameIdQuery = $findPublisherByBookNameIdQuery;
    $this->findAuthorsByBookNameIdQuery = $findAuthorsByBookNameIdQuery;
    $this->findBookPiecesByBookNameIdQuery = $findBookPiecesByBookNameIdQuery;
    $this->findBookTagsByBookNameIdQuery = $findBookTagsByBookNameIdQuery;
    $this->findBookByIdQuery = $findBookByIdQuery;
    $this->insertBookStatement = $insertBookStatement;
    $this->updateBookStatement = $updateBookStatement;
    $this->updateBookTagsStatement = $updateBookTagsStatement;
    $this->transactionManager = $transactionManager;
  }

  function findBookDetails(GetBookDetailsCommand $command): Option {
    return $this->findBookByNameIdQuery->query($command->nameId())
      ->map(FunctionUtils::function2OfClosure(
        fn($value) => new BookDetailsBuilderBook($value)
      ))
      // TODO: queries bellow could be run simultaneously
      ->map(FunctionUtils::function2OfClosure(
        fn($builder) => $this->findPublisherByBookNameIdQuery->query($command->nameId())
          ->fold(
            FunctionUtils::supplierOfClosure(fn() => $builder->noPublisher()),
            FunctionUtils::function2OfClosure(
              fn($publisher) => $builder->publisher($publisher)
            )
          )
      ))
      ->map(FunctionUtils::function2OfClosure(
        fn($builder) => $this->findAuthors($builder, $command)
      ))
      ->map(FunctionUtils::function2OfClosure(
        fn($builder) => $builder->bookPieces(
          $this->findBookPiecesByBookNameIdQuery->query($command->nameId())
        )
      ))
      ->map(FunctionUtils::function2OfClosure(
        fn($builder) => $builder->bookTags(
          $this->findBookTagsByBookNameIdQuery->query($command->nameId())
        )
      ))
      ->fold(
        FunctionUtils::supplierOfClosure(fn() => Option::none()),
        FunctionUtils::function2OfClosure(fn($value) => Option::of($value))
      );
  }

  private function findAuthors(BookDetailsBuilderPublisher $builder,
                               GetBookDetailsCommand       $command): BookDetailsBuilderAuthors {
    $bookAuthorsView = $this->findAuthorsByBookNameIdQuery->query($command->nameId());
    return $builder->authors($bookAuthorsView->authors(), $bookAuthorsView->bookAuthors());
  }

  function save(SaveBookCommand $command): Either {
    return $this->insertOrUpdate($command)
      ->execute($this->transactionManager)
      ->transform(FunctionUtils::function2OfClosure(
        fn($cause) => $this->wrapThrowableError($cause)
      ))
      ->flatMapRight(FunctionUtils::function2OfClosure(
        fn($success) => $this->findBookDetails(new GetBookDetailsCommand($command->nameId()))
          ->fold(FunctionUtils::supplierOfClosure(
            fn() => Either::ofLeft(Failure::of(L::main_errors_global_global_error_message))
          ),
            FunctionUtils::function2OfClosure(
              fn($details) => Either::ofRight($details)
            )
          )
      ));
  }

  private function insertOrUpdate(SaveBookCommand $command): Transaction {
    if (empty($command->id())) {
      return Transaction::of(
        fn() => $this->insertBook($command)
          ->flatMapRight(FunctionUtils::function2OfClosure(
            fn($result) => $this->updateBookTagsStatement->execute($command)
          ))
      );
    } else {
      return Transaction::of(fn() => $this->findBookByIdQuery->query($command->id())
        ->fold(
          FunctionUtils::supplierOfClosure(fn() => $this->insertBook($command)),
          FunctionUtils::function2OfClosure(fn($book) => $this->updateBook($command))
        )
        ->flatMapRight(FunctionUtils::function2OfClosure(
          fn($result) => $this->updateBookTagsStatement->execute($command)
        ))
      );
    }
  }

  private function insertBook(SaveBookCommand $command): Either {
    return $this->insertBookStatement->execute($command)
      ->flatMapRight(FunctionUtils::function2OfClosure(
        fn($result) => $this->findBookDetails(new GetBookDetailsCommand($command->nameId()))
          ->fold(
            FunctionUtils::supplierOfClosure(fn() => Either::ofLeft(Failure::of(L::main_errors_global_global_error_message))),
            FunctionUtils::function2OfClosure(fn($details) => Either::ofRight($details))
          )
      ));
  }

  private function updateBook(SaveBookCommand $command): Either {
    return $this->updateBookStatement->execute($command);
  }

  private function wrapThrowableError(Either $eitherWithThrowable): Either {
    return $eitherWithThrowable
      ->peekLeft(FunctionUtils::consumerOfClosure(
        fn($cause) => error_log(strval($cause))
      ))
      ->mapLeft(FunctionUtils::function2OfClosure(
        fn($cause) => Failure::of($cause->getMessage())
      ));
  }
}

// Fluent Builders
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
