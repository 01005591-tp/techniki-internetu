<?php

namespace p1\core\domain\book;

class BookListEntry
{
    private string $id;
    private string $isbn;
    private string $title;
    private ?string $imageUri;
    private ?string $description;
    private string $state;
    private ?string $language;

    public function __construct(string  $id,
                                string  $isbn,
                                string  $title,
                                ?string $imageUri,
                                ?string $description,
                                string  $state,
                                ?string $language)
    {
        $this->id = $id;
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

    public function isbn(): string
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

    public function __construct(array $books)
    {
        $this->books = $books;
    }

    public function books(): array
    {
        return $this->books;
    }

    public static function emptyBookList(): BookList
    {
        return new BookList(array());
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

    public static function defaultBookListPage(): BookListPage
    {
        return new BookListPage(1, 25);
    }
}