<?php

namespace p1\core\app;

require_once "core/database/database-configuration.php";
require_once "core/database/database-connection.php";

require_once "core/database/book/book-list-db-repository.php";
require_once "core/database/book/find-book-list-query.php";
require_once "core/database/book/find-book-list-query-builder.php";

require_once "core/database/book/book-details-db-repository.php";
require_once "core/database/book/find-authors-by-book-name-id-query.php";
require_once "core/database/book/find-book-by-id-query.php";
require_once "core/database/book/find-book-by-name-id-query.php";
require_once "core/database/book/find-book-pieces-by-book-name-id-query.php";
require_once "core/database/book/find-book-tags-by-book-name-id-query.php";
require_once "core/database/book/find-publisher-by-book-name-id-query.php";
require_once "core/database/book/insert-book-statement.php";
require_once "core/database/book/update-book-statement.php";
require_once "core/database/book/update-book-tags-statement.php";

require_once "core/database/book/tag/book-tags-db-repository.php";
require_once "core/database/book/tag/find-all-book-tags-query.php";

require_once "core/domain/book/book-list-repository.php";
require_once "core/domain/book/get-book-list-command-handler.php";

require_once "core/domain/book/book-details-repository.php";
require_once "core/domain/book/get-book-details-command-handler.php";

require_once "core/domain/book/edit/save-book-command-handler.php";

require_once "core/domain/book/tag/book-tags-repository.php";
require_once "core/domain/book/tag/get-all-book-tags-use-case.php";

use p1\core\database\book\BookDetailsDbRepository;
use p1\core\database\book\BookListDbRepository;
use p1\core\database\book\FindAuthorsByBookNameIdQuery;
use p1\core\database\book\FindBookByIdQuery;
use p1\core\database\book\FindBookByNameIdQuery;
use p1\core\database\book\FindBookListQuery;
use p1\core\database\book\FindBookListQueryBuilder;
use p1\core\database\book\FindBookPiecesByBookNameIdQuery;
use p1\core\database\book\FindBookTagsByBookNameIdQuery;
use p1\core\database\book\FindPublisherByBookNameIdQuery;
use p1\core\database\book\InsertBookStatement;
use p1\core\database\book\tag\BookTagsDbRepository;
use p1\core\database\book\tag\FindAllBookTagsQuery;
use p1\core\database\book\UpdateBookStatement;
use p1\core\database\book\UpdateBookTagsStatement;
use p1\core\database\DatabaseConfiguration;
use p1\core\database\DatabaseConnection;
use p1\core\domain\book\BookDetailsRepository;
use p1\core\domain\book\BookListRepository;
use p1\core\domain\book\edit\SaveBookCommandHandler;
use p1\core\domain\book\GetBookDetailsCommandHandler;
use p1\core\domain\book\GetBookListCommandHandler;
use p1\core\domain\book\tag\BookTagsRepository;
use p1\core\domain\book\tag\GetAllBookTagsUseCase;

class BookConfiguration {
  private DatabaseConnection $databaseConnection;
  private BookListRepository $bookListRepository;
  private GetBookListCommandHandler $getBookListCommandHandler;

  private BookDetailsRepository $bookDetailsRepository;
  private GetBookDetailsCommandHandler $getBookDetailsCommandHandler;
  private SaveBookCommandHandler $saveBookCommandHandler;

  private BookTagsRepository $bookTagsRepository;
  private GetAllBookTagsUseCase $getAllBookTagsUseCase;

  public function __construct(DatabaseConfiguration $databaseConfiguration) {
    $this->databaseConnection = $databaseConfiguration->databaseConnection();
    $findBookListQueryBuilder = new FindBookListQueryBuilder($this->databaseConnection->connection());
    $findDefaultBookListQuery = new FindBookListQuery(
      $this->databaseConnection->connection(),
      $findBookListQueryBuilder
    );
    $this->bookListRepository = new BookListDbRepository($findDefaultBookListQuery);
    $this->getBookListCommandHandler = new GetBookListCommandHandler($this->bookListRepository);

    $this->bookDetailsRepository = new BookDetailsDbRepository(
      new FindBookByNameIdQuery($this->databaseConnection->connection()),
      new FindPublisherByBookNameIdQuery($this->databaseConnection->connection()),
      new FindAuthorsByBookNameIdQuery($this->databaseConnection->connection()),
      new FindBookPiecesByBookNameIdQuery($this->databaseConnection->connection()),
      new FindBookTagsByBookNameIdQuery($this->databaseConnection->connection()),
      new FindBookByIdQuery($this->databaseConnection->connection()),
      new InsertBookStatement($this->databaseConnection->connection()),
      new UpdateBookStatement($this->databaseConnection->connection()),
      new UpdateBookTagsStatement($this->databaseConnection->connection()),
      $databaseConfiguration->transactionManager()
    );
    $this->getBookDetailsCommandHandler = new GetBookDetailsCommandHandler($this->bookDetailsRepository);

    $findAllBookTagsQuery = new FindAllBookTagsQuery($this->databaseConnection->connection());
    $this->bookTagsRepository = new BookTagsDbRepository($findAllBookTagsQuery);
    $this->getAllBookTagsUseCase = new GetAllBookTagsUseCase($this->bookTagsRepository);

    $this->saveBookCommandHandler = new SaveBookCommandHandler($this->bookDetailsRepository);
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

  public function saveBookCommandHandler(): SaveBookCommandHandler {
    return $this->saveBookCommandHandler;
  }
}