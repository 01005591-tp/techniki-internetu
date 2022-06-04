<?php

namespace p1\core\database\book;

require_once "core/domain/audit/auditable-object.php";
require_once "core/domain/book/book-piece.php";

use mysqli;
use p1\core\domain\audit\AuditableObject;
use p1\core\domain\book\BookPiece;
use p1\core\domain\book\BookPieceState;

class FindBookPiecesByBookNameIdQuery {
  private mysqli $connection;

  public function __construct(mysqli $connection) {
    $this->connection = $connection;
  }

  public function query(string $nameId): array {
    $stmt = $this->connection->prepare(
      "SELECT
                    BP.ID AS BP_ID
                    ,BP.BOOK_ID AS BP_BOOK_ID
                    ,BP.STATE AS BP_STATE
                    ,UNIX_TIMESTAMP(BP.CREATION_DATE) AS BP_CREATION_DATE
                    ,UNIX_TIMESTAMP(BP.UPDATE_DATE) AS BP_UPDATE_DATE
                    ,BP.UPDATED_BY AS BP_UPDATED_BY
                    ,BP.VERSION AS BP_VERSION
                FROM
                    BOOK_PIECES BP
                    JOIN BOOKS B ON
                        BP.BOOK_ID = B.ID
                WHERE
                    B.NAME_ID = ?");
    $escapedNameId = $this->connection->real_escape_string($nameId);
    $stmt->bind_param("s", $escapedNameId);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookPieces = array();
    while ($row = $result->fetch_assoc()) {
      $bookPieces[] = new BookPiece(
        $row['BP_ID'],
        $row['BP_BOOK_ID'],
        BookPieceState::of($row['BP_STATE'])->orElse(BookPieceState::UNAVAILABLE),
        new AuditableObject(
          $row['BP_CREATION_DATE'],
          $row['BP_UPDATE_DATE'],
          $row['BP_UPDATED_BY']
        ),
        $row['BP_VERSION']
      );
    }
    return $bookPieces;
  }
}