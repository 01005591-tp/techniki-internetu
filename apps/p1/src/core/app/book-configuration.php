<?php

namespace p1\core\app;

require_once "core/database/database-configuration.php";
require_once "core/database/database-connection.php";

require_once "core/database/book/book-list-db-repository.php";
require_once "core/database/book/find-default-book-list-query.php";

require_once "core/database/book/book-details-db-repository.php";
require_once "core/database/book/find-authors-by-book-name-id-query.php";
require_once "core/database/book/find-book-by-name-id-query.php";
require_once "core/database/book/find-book-pieces-by-book-name-id-query.php";
require_once "core/database/book/find-book-tags-by-book-name-id-query.php";
require_once "core/database/book/find-publisher-by-book-name-id-query.php";

require_once "core/database/book/tag/book-tags-db-repository.php";
require_once "core/database/book/tag/find-all-book-tags-query.php";

require_once "core/domain/book/book-list-repository.php";
require_once "core/domain/book/get-book-list-command-handler.php";

require_once "core/domain/book/book-details-repository.php";
require_once "core/domain/book/get-book-details-command-handler.php";

require_once "core/domain/book/tag/book-tags-repository.php";
require_once "core/domain/book/tag/get-all-book-tags-use-case.php";

use p1\core\database\book\BookDetailsDbRepository;
use p1\core\database\book\BookListDbRepository;
use p1\core\database\book\FindAuthorsByBookNameIdQuery;
use p1\core\database\book\FindBookByNameIdQuery;
use p1\core\database\book\FindBookPiecesByBookNameIdQuery;
use p1\core\database\book\FindBookTagsByBookNameIdQuery;
use p1\core\database\book\FindDefaultBookListQuery;
use p1\core\database\book\FindPublisherByBookNameIdQuery;
use p1\core\database\book\tag\BookTagsDbRepository;
use p1\core\database\book\tag\FindAllBookTagsQuery;
use p1\core\database\DatabaseConfiguration;
use p1\core\database\DatabaseConnection;
use p1\core\domain\book\BookDetailsRepository;
use p1\core\domain\book\BookListRepository;
use p1\core\domain\book\GetBookDetailsCommandHandler;
use p1\core\domain\book\GetBookListCommandHandler;
use p1\core\domain\book\tag\BookTagsRepository;
use p1\core\domain\book\tag\GetAllBookTagsUseCase;

class BookConfiguration {
  private DatabaseConnection $databaseConnection;
  private FindDefaultBookListQuery $findDefaultBookListQuery;
  private BookListRepository $bookListRepository;
  private GetBookListCommandHandler $getBookListCommandHandler;

  private FindBookByNameIdQuery $findBookByNameIdQuery;
  private FindPublisherByBookNameIdQuery $findPublisherByBookNameIdQuery;
  private FindAuthorsByBookNameIdQuery $findAuthorsByBookNameIdQuery;
  private FindBookPiecesByBookNameIdQuery $findBookPiecesByBookNameIdQuery;
  private FindBookTagsByBookNameIdQuery $findBookTagsByBookNameIdQuery;
  private BookDetailsRepository $bookDetailsRepository;
  private GetBookDetailsCommandHandler $getBookDetailsCommandHandler;

  private FindAllBookTagsQuery $findAllBookTagsQuery;
  private BookTagsRepository $bookTagsRepository;
  private GetAllBookTagsUseCase $getAllBookTagsUseCase;

  public function __construct(DatabaseConfiguration $databaseConfiguration) {
    $this->databaseConnection = $databaseConfiguration->databaseConnection();
    $this->findDefaultBookListQuery = new FindDefaultBookListQuery($this->databaseConnection->connection());
    $this->bookListRepository = new BookListDbRepository($this->findDefaultBookListQuery);
    $this->getBookListCommandHandler = new GetBookListCommandHandler($this->bookListRepository);

    $this->findBookByNameIdQuery = new FindBookByNameIdQuery($this->databaseConnection->connection());
    $this->findPublisherByBookNameIdQuery = new FindPublisherByBookNameIdQuery($this->databaseConnection->connection());
    $this->findAuthorsByBookNameIdQuery = new FindAuthorsByBookNameIdQuery($this->databaseConnection->connection());
    $this->findBookPiecesByBookNameIdQuery = new FindBookPiecesByBookNameIdQuery($this->databaseConnection->connection());
    $this->findBookTagsByBookNameIdQuery = new FindBookTagsByBookNameIdQuery($this->databaseConnection->connection());

    $this->bookDetailsRepository = new BookDetailsDbRepository(
      $this->findBookByNameIdQuery,
      $this->findPublisherByBookNameIdQuery,
      $this->findAuthorsByBookNameIdQuery,
      $this->findBookPiecesByBookNameIdQuery,
      $this->findBookTagsByBookNameIdQuery
    );
    $this->getBookDetailsCommandHandler = new GetBookDetailsCommandHandler($this->bookDetailsRepository);

    $this->findAllBookTagsQuery = new FindAllBookTagsQuery($this->databaseConnection->connection());
    $this->bookTagsRepository = new BookTagsDbRepository($this->findAllBookTagsQuery);
    $this->getAllBookTagsUseCase = new GetAllBookTagsUseCase($this->bookTagsRepository);
  }

  public function getBookListCommandHandler(): GetBookListCommandHandler {
    return $this->getBookListCommandHandler;
  }

  public function getBookDetailsCommandHandler(): GetBookDetailsCommandHandler {
    return $this->getBookDetailsCommandHandler;
  }

  public function getAllBookTagsUseCase(): GetAllBookTagsUseCase {
    return $this->getAllBookTagsUseCase;
  }
}