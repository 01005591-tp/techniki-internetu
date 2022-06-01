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

    public function query(int $offset, int $pageSize): BookListView
    {
        $stmt = $this->connection->prepare("SELECT 
            b.ID
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
        FROM BOOKS b 
        WHERE b.STATE != 'UNAVAILABLE' 
        ORDER BY ID DESC
        LIMIT ?,?");
        $stmt->bind_param("ii", $offset, $pageSize);
        $stmt->execute();
        $result = $stmt->get_result();
        $books = array();
        $booksCount = 0;
        while ($row = $result->fetch_assoc()) {
            $books[] = new BookListEntryView(
                $row['ID'],
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