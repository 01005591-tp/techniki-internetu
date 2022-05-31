<?php

namespace p1\core\database\book;

require_once "core/database/book/book-list-entry-view.php";

use mysqli;

class FindDefaultBookListQuery
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function query(int $offset, int $pageSize): array
    {
        $stmt = $this->connection->prepare("SELECT 
            b.ID
            ,b.ISBN_13
            ,b.TITLE
            ,b.IMAGE_URI
            ,CASE 
                WHEN LENGTH(b.DESCRIPTION) > 500 THEN
                    CONCAT(SUBSTR(b.DESCRIPTION, 1, 497), '...')
                ELSE b.DESCRIPTION
            END AS DESCRIPTION
            ,b.STATE
            ,b.LANGUAGE
        FROM BOOKS b 
        WHERE b.STATE != 'UNAVAILABLE' 
        ORDER BY ID DESC 
        LIMIT ?,?");
        $stmt->bind_param("ii", $offset, $pageSize);
        $stmt->execute();
        $result = $stmt->get_result();
        $books = array();
        while ($row = $result->fetch_assoc()) {
            $books[] = new BookListEntryView(
                $row['ID'],
                $row['ISBN_13'],
                $row['TITLE'],
                $row['IMAGE_URI'],
                $row['DESCRIPTION'],
                $row['STATE'],
                $row['LANGUAGE']
            );
        }
        return $books;
    }
}