<?php

namespace p1\core\database\book;

require_once "core/domain/book/get-book-list-command.php";

use mysqli;
use p1\core\domain\book\GetBookListCommand;

class FindBookListQueryBuilder {

  private const SELECT_PART = "SELECT 
            b.ID
            ,b.NAME_ID
            ,b.ISBN
            ,b.TITLE
            ,b.IMAGE_URI
            ,CASE 
                WHEN LENGTH(b.DESCRIPTION) > 500 THEN
                    CONCAT(SUBSTR(b.DESCRIPTION, 1, 497), '...')
                ELSE b.DESCRIPTION
            END AS DESCRIPTION
            ,b.STATE
            ,b.LANGUAGE
            ,COUNT(b.ID) OVER (PARTITION BY NULL) AS BOOKS_COUNT
            FROM BOOKS b";

  private mysqli $mysqli;

  public function __construct(mysqli $mysqli) {
    $this->mysqli = $mysqli;
  }

  public function buildQuery(GetBookListCommand $command): string {
    $sql = self::SELECT_PART;
    // join BOOK_TAGS
    if (!empty($command->tags())) {
      $sql = $sql . $this->tagsQueryPart($command->tags());
    }
    // join AUTHORS and BOOK_AUTHORS
    if (!empty($command->author())) {
      $sql = $sql . $this->authorQueryPart($command->author());
    }

    $sql = $sql . ' WHERE 1=1 ';

    // like TITLE
    if (!empty($command->title())) {
      $sql = $sql . $this->likeClauseParam('b.TITLE', $command->title());
    }

    // like DESCRIPTION
    if (!empty($command->description())) {
      $sql = $sql . $this->likeClauseParam('b.DESCRIPTION', $command->description());
    }

    // equal ISBN
    if (!empty($command->isbn())) {
      $sql = $sql . $this->equalParam('b.ISBN', $command->isbn());
    }

    // order by
    $sql = $sql . " ORDER BY CASE STATE WHEN 'AVAILABLE_SOON' THEN 1 WHEN 'AVAILABLE' THEN 2 ELSE 0 END DESC,  
    ID DESC";
    // limit pagination part
    $offset = ($command->page() - 1) * $command->pageSize();
    return $sql . " LIMIT " . $offset . "," . $command->pageSize();
  }

  private function tagsQueryPart(array $tags): string {
    return ' JOIN BOOK_TAGS bt ON 
        bt.BOOK_ID = b.ID
        AND bt.TAG_CODE IN (' . $this->inClauseParams($tags) . ')';
  }

  private function authorQueryPart(string $author): string {
    $escapedAuthor = $this->mysqli->real_escape_string($author);
    return "
      JOIN BOOK_AUTHORS ba ON 
        ba.BOOK_ID = b.ID
      JOIN AUTHORS a ON
        a.ID = ba.AUTHOR_ID
        AND (
          LOWER(a.FIRST_NAME) LIKE CONCAT('%', LOWER('" . $escapedAuthor . "'), '%') 
          OR LOWER(a.LAST_NAME) LIKE CONCAT('%', LOWER('" . $escapedAuthor . "'), '%')
        )
    ";
  }

  private function likeClauseParam(string $columnName, string $paramValue): string {
    $escapedParam = $this->mysqli->real_escape_string($paramValue);
    return " and LOWER(" . $columnName . ") LIKE
    CONCAT('%', LOWER('" . $escapedParam . "'), '%')";
  }

  private function equalParam(string $columnName, string $paramValue): string {
    $escapedParam = $this->mysqli->real_escape_string($paramValue);
    return " and LOWER(" . $columnName . ") = LOWER('" . $escapedParam . "')";
  }

  private function inClauseParams(array $inClauseParams): string {
    $paramString = '';
    $array = array_values($inClauseParams);
    for ($idx = 0; $idx < count($inClauseParams); $idx++) {
      $param = $array[$idx];
      $escapedParam = $this->mysqli->real_escape_string($param);
      $paramString = $paramString . ",'" . $escapedParam . "'";
    }
    if (str_starts_with($paramString, ',')) {
      $paramString = substr($paramString, 1);
    }
    return $paramString;
  }
}