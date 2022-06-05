<?php

namespace p1\core\database\book;

require_once "core/domain/audit/auditable-object.php";
require_once "core/domain/book/author.php";
require_once "core/domain/book/book-author.php";
require_once "core/database/book/book-authors-view.php";

use mysqli;
use p1\core\domain\audit\AuditableObject;
use p1\core\domain\book\Author;
use p1\core\domain\book\BookAuthor;

class FindAuthorsByBookNameIdQuery {
  private mysqli $connection;

  public function __construct(mysqli $connection) {
    $this->connection = $connection;
  }

  public function query(string $nameId): BookAuthorsView {
    $stmt = $this->connection->prepare(
      "SELECT
                    A.ID AS A_ID
                    ,A.FIRST_NAME AS A_FIRST_NAME
                    ,A.LAST_NAME AS A_LAST_NAME
                    ,A.BIOGRAPHY_NOTE AS A_BIOGRAPHY_NOTE
                    ,A.BIRTH_YEAR AS A_BIRTH_YEAR
                    ,A.BIRTH_MONTH AS A_BIRTH_MONTH
                    ,A.BIRTH_DAY AS A_BIRTH_DAY
                    ,UNIX_TIMESTAMP(A.CREATION_DATE) AS A_CREATION_DATE
                    ,UNIX_TIMESTAMP(A.UPDATE_DATE) AS A_UPDATE_DATE
                    ,A.UPDATED_BY AS A_UPDATED_BY
                    ,A.VERSION AS A_VERSION
                    ,BA.BOOK_ID AS BA_BOOK_ID
                    ,BA.PRIORITY AS BA_PRIORITY
                    ,UNIX_TIMESTAMP(BA.CREATION_DATE) AS BA_CREATION_DATE
                    ,UNIX_TIMESTAMP(BA.UPDATE_DATE) AS BA_UPDATE_DATE
                    ,BA.UPDATED_BY AS BA_UPDATED_BY
                    ,BA.VERSION AS BA_VERSION
                FROM
                    AUTHORS A
                    JOIN BOOK_AUTHORS BA ON
                        A.ID = BA.AUTHOR_ID
                    JOIN BOOKS B ON
                        B.ID = BA.BOOK_ID
                WHERE
                    B.NAME_ID = ?
                ORDER BY
                    BA.PRIORITY
                    ,A.FIRST_NAME
                    ,A.LAST_NAME");
    $escapedNameId = $this->connection->real_escape_string($nameId);
    $stmt->bind_param("s", $escapedNameId);
    $stmt->execute();
    $result = $stmt->get_result();
    $authors = array();
    $bookAuthors = array();
    while ($row = $result->fetch_assoc()) {
      $authors[] = new Author(
        $row['A_ID'],
        $row['A_FIRST_NAME'],
        $row['A_LAST_NAME'],
        $row['A_BIOGRAPHY_NOTE'],
        $row['A_BIRTH_YEAR'],
        $row['A_BIRTH_MONTH'],
        $row['A_BIRTH_DAY'],
        $row['BA_PRIORITY'],
        new AuditableObject(
          $row['A_CREATION_DATE'],
          $row['A_UPDATE_DATE'],
          $row['A_UPDATED_BY']
        ),
        $row['A_VERSION']
      );
      $bookAuthors[] = new BookAuthor(
        $row['BA_BOOK_ID'],
        $row['A_ID'],
        $row['BA_PRIORITY'],
        new AuditableObject(
          $row['BA_CREATION_DATE'],
          $row['BA_UPDATE_DATE'],
          $row['BA_UPDATED_BY']
        ),
        $row['BA_VERSION']
      );
    }
    return new BookAuthorsView($authors, $bookAuthors);
  }
}