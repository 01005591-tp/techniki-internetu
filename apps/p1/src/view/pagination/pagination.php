<?php

namespace p1\view\home;

class PaginationData
{
    private string $uri;
    private string $queryParamName;
    private array $pages;
    private int $previousPage;
    private int $nextPage;

    public function __construct(string $uri,
                                string $queryParamName,
                                array  $pages,
                                int    $previousPage,
                                int    $nextPage)
    {
        $this->uri = $uri;
        $this->queryParamName = $queryParamName;
        $this->pages = $pages;
        $this->previousPage = $previousPage;
        $this->nextPage = $nextPage;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function queryParamName(): string
    {
        return $this->queryParamName;
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
        return 'PaginationData(uri=' . $this->uri . ', queryParamName=' . $this->queryParamName
            . ', previousPage=' . $this->previousPage . ', nextPage=' . $this->nextPage
            . ', pages=(' . substr($string, 1) . '))';
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