<?php

namespace p1\core\domain\book;

class BookListEntry
{
    private string $id;
    private string $nameId;
    private ?string $isbn;
    private string $title;
    private ?string $imageUri;
    private ?string $description;
    private string $state;
    private ?string $language;

    public function __construct(string  $id,
                                string  $nameId,
                                ?string $isbn,
                                string  $title,
                                ?string $imageUri,
                                ?string $description,
                                string  $state,
                                ?string $language)
    {
        $this->id = $id;
        $this->nameId = $nameId;
        $this->isbn = $isbn;
        $this->title = $title;
        $this->imageUri = $imageUri;
        $this->description = $description;
        $this->state = $state;
        $this->language = $language;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function nameId(): string
    {
        return $this->nameId;
    }

    public function isbn(): ?string
    {
        return $this->isbn;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function imageUri(): ?string
    {
        return $this->imageUri;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function state(): string
    {
        return $this->state;
    }

    public function language(): ?string
    {
        return $this->language;
    }
}

class BookList
{
    private array $books;
    private int $booksCount;

    public function __construct(array $books,
                                int   $booksCount)
    {
        $this->books = $books;
        $this->booksCount = $booksCount;
    }

    public function books(): array
    {
        return $this->books;
    }

    public function booksCount(): int
    {
        return $this->booksCount;
    }

    public static function emptyBookList(): BookList
    {
        return new BookList(array(), 0);
    }
}

class BookListPage
{
    private int $page;
    private int $pageSize;

    public function __construct(int $page, int $pageSize)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    public function page(): int
    {
        return $this->page;
    }

    public function pageSize(): int
    {
        return $this->pageSize;
    }

    public function withPage(int $page): BookListPage
    {
        return new BookListPage($page, $this->pageSize);
    }

    public static function defaultBookListPage(): BookListPage
    {
        return new BookListPage(1, 3);
    }
}