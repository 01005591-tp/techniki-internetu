<?php

namespace p1\core\database\book;

require_once "core/database/book/book-list-entry-view.php";
require_once "core/database/book/find-book-list-query-builder.php";

require_once "core/domain/book/get-book-list-command.php";

use mysqli;
use p1\core\domain\book\GetBookListCommand;

class FindBookListQuery {
  private mysqli $connection;
  private FindBookListQueryBuilder $findBookListQueryBuilder;

  public function __construct(mysqli                   $connection,
                              FindBookListQueryBuilder $findBookListQueryBuilder) {
    $this->connection = $connection;
    $this->findBookListQueryBuilder = $findBookListQueryBuilder;
  }

  public function query(GetBookListCommand $command): BookListView {
    $sqlQuery = $this->findBookListQueryBuilder->buildQuery($command);
    $stmt = $this->connection->prepare($sqlQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = array();
    $booksCount = 0;
    while ($row = $result->fetch_assoc()) {
      $books[] = new BookListEntryView(
        $row['ID'],
        $row['NAME_ID'],
        $row['ISBN'],
        $row['TITLE'],
        $row['IMAGE_URI'],
        $row['DESCRIPTION'],
        $row['STATE'],
        $row['LANGUAGE']
      );
      if ($booksCount === 0) {
        $booksCount = $row['BOOKS_COUNT'];
      }
    }
    return new BookListView($books, $booksCount);
  }
}