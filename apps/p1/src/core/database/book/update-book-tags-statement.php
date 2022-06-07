<?php

namespace p1\core\database\book;

require_once "core/function/either.php";
require_once "core/function/function.php";
require_once "core/function/option.php";

use mysqli;
use p1\core\domain\book\edit\SaveBookCommand;
use p1\core\function\Either;
use RuntimeException;

class UpdateBookTagsStatement {
  private mysqli $connection;

  public function __construct(mysqli $connection) {
    $this->connection = $connection;
  }

  public function execute(SaveBookCommand $command): Either {
    $safeNameId = $this->connection->real_escape_string($command->nameId());
    $this->deleteBookTags($safeNameId);
    if (empty($command->tags())) {
      return Either::ofRight(null);
    }
    $safeTags = array_map(
      fn($tag) => $this->connection->real_escape_string($tag), $command->tags()
    );
    $this->insertMissingTags($safeTags);
    $this->insertBookTags($safeTags, $safeNameId);
    return Either::ofRight(null);
  }

  private function insertBookTags(array $safeTags, string $safeNameId): void {
    $allTagsSqlPart = join(" ", array_map(
      fn($code) => "UNION ALL SELECT '" . $code . "'", $safeTags
    ));

    $insertTagsSql = "
      INSERT INTO BOOK_TAGS (TAG_CODE, BOOK_ID) 
      WITH ALL_TAGS AS (
        SELECT NULL AS CODE
        " . $allTagsSqlPart . "
      ), TAGS_BOOK AS (
      SELECT
          B.ID AS BOOK_ID,
          ALL_TAGS.CODE AS TAG_CODE
      FROM
          BOOKS B
          CROSS JOIN ALL_TAGS
      WHERE
          B.NAME_ID = ?
          AND ALL_TAGS.CODE IS NOT NULL
      ) SELECT TAGS_BOOK.TAG_CODE, TAGS_BOOK.BOOK_ID FROM TAGS_BOOK";
    $stmt = $this->connection->prepare($insertTagsSql);
    $stmt->bind_param("s", $safeNameId);
    $stmt->execute();
    if ($stmt->affected_rows <= 0) {
      throw new RuntimeException(L::main_errors_global_global_error_message);
    }
  }

  private function deleteBookTags(string $safeNameId): void {
    $stmt = $this->connection->prepare("DELETE FROM BOOK_TAGS 
       WHERE BOOK_ID = (SELECT ID FROM BOOKS WHERE NAME_ID = ?)");
    $stmt->bind_param("s", $safeNameId);
    $stmt->execute();
  }

  private function insertMissingTags(array $safeTags): void {
    $missingTags = $this->findMissingTags($safeTags);
    if (empty($missingTags)) {
      return;
    }
    $allTagsSqlPart = join(",", array_map(
      fn($code) => "('" . $code . "')", $missingTags
    ));

    $insertMissingTagsSql = "INSERT INTO BOOKS_TAGS (CODE) VALUES " . $allTagsSqlPart;
    $stmt = $this->connection->prepare($insertMissingTagsSql);
    $stmt->execute();
    if ($stmt->affected_rows <= 0) {
      throw new RuntimeException(L::main_errors_global_global_error_message);
    }
  }

  private function findMissingTags(array $safeTags): array {
    $allTagsSqlPart = join(" ", array_map(
      fn($code) => "UNION ALL SELECT '" . $code . "'", $safeTags
    ));
    $findNonExistingTagsSql = "
      WITH ALL_TAGS AS (
        SELECT NULL AS CODE
        " . $allTagsSqlPart . "
      )
      SELECT
        ALL_TAGS.CODE AS AT_CODE
      FROM
        ALL_TAGS
        LEFT JOIN BOOKS_TAGS BT ON 
          ALL_TAGS.CODE = BT.CODE
      WHERE
        ALL_TAGS.CODE IS NOT NULL
        AND BT.CODE IS NULL";
    $stmt = $this->connection->prepare($findNonExistingTagsSql);
    $stmt->execute();
    $result = $stmt->get_result();
    $missingTags = [];
    while ($row = $result->fetch_assoc()) {
      $missingTags[] = $row['AT_CODE'];
    }
    return $missingTags;
  }
}