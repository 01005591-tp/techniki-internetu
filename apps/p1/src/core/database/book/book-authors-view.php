<?php

namespace p1\core\database\book;

class BookAuthorsView
{
    private array $authors;
    private array $bookAuthors;

    public function __construct(array $authors, array $bookAuthors)
    {
        $this->authors = $authors;
        $this->bookAuthors = $bookAuthors;
    }

    public function authors(): array
    {
        return $this->authors;
    }

    public function bookAuthors(): array
    {
        return $this->bookAuthors;
    }
}