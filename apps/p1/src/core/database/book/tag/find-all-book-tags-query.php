<?php

namespace p1\core\database\book\tag;

require_once "core/domain/book/book-tag.php";

use mysqli;
use p1\core\domain\book\BookTag;

class FindAllBookTagsQuery
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function query(): array
    {
        $stmt = $this->connection->prepare("SELECT bt.CODE FROM BOOKS_TAGS bt ORDER BY bt.CODE");
        $stmt->execute();
        $result = $stmt->get_result();
        $tags = [];
        while ($row = $result->fetch_assoc()) {
            $tags[] = new BookTag($row['CODE']);
        }
        return $tags;
    }
}