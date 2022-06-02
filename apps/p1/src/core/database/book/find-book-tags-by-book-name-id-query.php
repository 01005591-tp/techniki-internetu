<?php

namespace p1\core\database\book;

require_once "core/domain/book/book-tag.php";

use mysqli;
use p1\core\domain\book\BookTag;

class FindBookTagsByBookNameIdQuery
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function query(string $nameId): array
    {
        $stmt = $this->connection->prepare(
            "SELECT
                        T.CODE AS T_CODE
                    FROM
                        BOOK_TAGS BT
                        JOIN BOOKS_TAGS T ON
                            T.CODE = BT.TAG_CODE
                        JOIN BOOKS B ON
                            BT.BOOK_ID = B.ID
                    WHERE
                        B.NAME_ID = ?");
        $escapedNameId = $this->connection->real_escape_string($nameId);
        $stmt->bind_param("s", $escapedNameId);
        $stmt->execute();
        $result = $stmt->get_result();
        $tags = array();
        while ($row = $result->fetch_assoc()) {
            $tags[] = new BookTag(
                $row['T_CODE'],
            );
        }
        return $tags;
    }
}