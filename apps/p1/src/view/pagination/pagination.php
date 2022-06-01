<?php

namespace p1\view\home;

class PaginationData
{
    private array $pages;
    private int $previousPage;
    private int $nextPage;

    public function __construct(array $pages, int $previousPage, int $nextPage)
    {
        $this->pages = $pages;
        $this->previousPage = $previousPage;
        $this->nextPage = $nextPage;
    }

    public function pages(): array
    {
        return $this->pages;
    }

    public function previousPage(): int
    {
        return $this->previousPage;
    }

    public function nextPage(): int
    {
        return $this->nextPage;
    }


    public function __toString(): string
    {
        $string = '';
        foreach ($this->pages as $page) {
            $string = $string . ',' . $page;
        }
        return substr($string, 1);
    }


    public static function emptyPaginationData(): PaginationData
    {
        return new PaginationData(array());
    }
}

class PaginationPage
{
    private int $index; // 1-based index
    private string $style; // active, disabled, '' (empty string)

    public function __construct(int $index, string $style)
    {
        $this->index = $index;
        $this->style = $style;
    }

    public function index(): int
    {
        return $this->index;
    }

    public function indexDisplay(): string
    {
        return $this->style === 'disabled'
            ? '...'
            : strval($this->index);
    }

    public function style(): string
    {
        return $this->style;
    }

    public static function active(int $index): PaginationPage
    {
        return new PaginationPage($index, 'active');
    }

    public static function disabled(int $index): PaginationPage
    {
        return new PaginationPage($index, 'disabled');
    }

    public static function available(int $index): PaginationPage
    {
        return new PaginationPage($index, '');
    }

    public function __toString(): string
    {
        return 'PaginationPage(index=' . $this->index . ', style=' . $this->style . ')';
    }


}