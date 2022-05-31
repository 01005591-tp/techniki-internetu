<?php

namespace p1\core\app;

require_once "core/database/database-configuration.php";
require_once "core/database/database-connection.php";

require_once "core/database/book/book-list-db-repository.php";
require_once "core/database/book/find-default-book-list-query.php";

require_once "core/domain/book/book-list-repository.php";
require_once "core/domain/book/get-book-list-command-handler.php";

use p1\core\database\book\BookListDbRepository;
use p1\core\database\book\FindDefaultBookListQuery;
use p1\core\database\DatabaseConfiguration;
use p1\core\database\DatabaseConnection;
use p1\core\domain\book\BookListRepository;
use p1\core\domain\book\GetBookListCommandHandler;

class BookConfiguration
{
    private DatabaseConnection $databaseConnection;
    private FindDefaultBookListQuery $findDefaultBookListQuery;
    private BookListRepository $bookListRepository;
    private GetBookListCommandHandler $getBookListCommandHandler;

    public function __construct(DatabaseConfiguration $databaseConfiguration)
    {
        $this->databaseConnection = $databaseConfiguration->databaseConnection();
        $this->findDefaultBookListQuery = new FindDefaultBookListQuery($this->databaseConnection->connection());
        $this->bookListRepository = new BookListDbRepository($this->findDefaultBookListQuery);
        $this->getBookListCommandHandler = new GetBookListCommandHandler($this->bookListRepository);
    }

    public function getBookListCommandHandler(): GetBookListCommandHandler
    {
        return $this->getBookListCommandHandler;
    }
}