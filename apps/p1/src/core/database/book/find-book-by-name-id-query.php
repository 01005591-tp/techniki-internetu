<?php

namespace p1\core\database\book;

require_once "core/domain/audit/auditable-object.php";
require_once "core/domain/book/book.php";
require_once "core/domain/language/language.php";

require_once "core/function/option.php";

use mysqli;
use p1\core\domain\audit\AuditableObject;
use p1\core\domain\book\Book;
use p1\core\domain\language\Language;
use p1\core\function\Option;

class FindBookByNameIdQuery
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function query(string $nameId): Option
    {
        $stmt = $this->connection->prepare(
            "SELECT
                    B.ID AS B_ID
                    ,B.NAME_ID AS B_NAME_ID
                    ,B.ISBN AS B_ISBN
                    ,B.TITLE AS B_TITLE
                    ,B.DESCRIPTION AS B_DESCRIPTION
                    ,B.LANGUAGE AS B_LANGUAGE
                    ,UNIX_TIMESTAMP(B.PUBLISHED_AT) AS B_PUBLISHED_AT
                    ,B.PUBLISHER_ID AS B_PUBLISHER_ID
                    ,B.PAGES AS B_PAGES
                    ,B.STATE AS B_STATE
                    ,B.IMAGE_URI AS B_IMAGE_URI
                    ,UNIX_TIMESTAMP(B.CREATION_DATE) AS B_CREATION_DATE
                    ,UNIX_TIMESTAMP(B.UPDATE_DATE) AS B_UPDATE_DATE
                    ,B.UPDATED_BY AS B_UPDATED_BY
                    ,B.VERSION AS B_VERSION
                FROM
                    BOOKS B
                WHERE
                    B.NAME_ID = ?");
        $escapedNameId = $this->connection->real_escape_string($nameId);
        $stmt->bind_param("s", $escapedNameId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if (!$row) {
            return Option::none();
        } else {
            return Option::of(
                new Book(
                    $row['B_ID'],
                    $row['B_NAME_ID'],
                    $row['B_ISBN'],
                    $row['B_TITLE'],
                    $row['B_DESCRIPTION'],
                    Language::ofOrUnknown($row['B_LANGUAGE']),
                    $row['B_PUBLISHED_AT'],
                    $row['B_PUBLISHER_ID'],
                    $row['B_PAGES'],
                    $row['B_STATE'],
                    $row['B_IMAGE_URI'],
                    new AuditableObject(
                        $row['B_CREATION_DATE'],
                        $row['B_UPDATE_DATE'],
                        $row['B_UPDATED_BY']
                    ),
                    $row['B_VERSION']
                )
            );
        }
    }
}