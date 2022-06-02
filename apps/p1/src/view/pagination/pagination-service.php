<?php

namespace p1\view\home;

require_once "core/function/function.php";
require_once "core/function/option.php";
require_once "view/pagination/pagination.php";

use p1\core\function\Function2;
use p1\core\function\Option;

class PaginationService
{
    public function resolvePagination(ResolvePaginationParams $params): Option
    {
        if ($params->itemsCount() < 1) {
            return Option::none();
        }

        $pagesCount = ceil($params->itemsCount() / $params->pageSize());
        $firstPage = 1;
        $lastPage = $pagesCount;
        $currentPage = max(min($params->currentPage(), $lastPage), $firstPage);

        $pageCurrent = Option::of(PaginationPage::active($currentPage));
        $pageFirst = $currentPage == $firstPage ? Option::none() : Option::of(PaginationPage::available($firstPage));
        $pageLast = $currentPage == $lastPage ? Option::none() : Option::of(PaginationPage::available($lastPage));
        $pageBeforeCurrent = $this->resolvePageBeforeCurrent($firstPage, $currentPage);
        $pageAfterCurrent = $this->resolvePageAfterCurrent($lastPage, $currentPage);
        $pageAfterFirst = $this->resolvePageAfterFirst($firstPage, $currentPage, $pageBeforeCurrent);
        $pageBeforeLast = $this->resolvePageBeforeLast($lastPage, $currentPage, $pageAfterCurrent);

        $paginationPagesOption = [
            $pageCurrent,
            $pageFirst,
            $pageLast,
            $pageBeforeCurrent,
            $pageAfterCurrent,
            $pageAfterFirst,
            $pageBeforeLast
        ];

        $paginationPages = array();
        foreach ($paginationPagesOption as $option) {
            if ($option->isDefined()) {
                $paginationPage = $option->get();
                $paginationPages[$paginationPage->index()] = $paginationPage;
            }
        }
        ksort($paginationPages);
        $getPaginationPageIndexFunction = new GetPaginationPageIndexFunction();
        return Option::of(new PaginationData(
            $params->uri(),
            $params->queryParamName(),
            $paginationPages,
            $pageBeforeCurrent->map($getPaginationPageIndexFunction)->orElse($firstPage),
            $pageAfterCurrent->map($getPaginationPageIndexFunction)->orElse($lastPage)
        ));
    }

    private function resolvePageBeforeCurrent(int $firstPage, int $currentPage): Option
    {
        return $currentPage < $firstPage + 2
            ? Option::none()
            : Option::of(PaginationPage::available($currentPage - 1));
    }

    private function resolvePageAfterCurrent(int $lastPage, int $currentPage): Option
    {
        return $currentPage > $lastPage - 2
            ? Option::none()
            : Option::of(PaginationPage::available($currentPage + 1));
    }

    private function resolvePageAfterFirst(int $firstPage, int $currentPage, Option $pageBeforeCurrent): Option
    {
        return $pageBeforeCurrent->flatMap(new class($firstPage, $currentPage) implements Function2 {
            private int $firstPage;
            private int $currentPage;

            public function __construct(int $firstPage, int $currentPage)
            {
                $this->firstPage = $firstPage;
                $this->currentPage = $currentPage;
            }

            function apply($value): Option
            {
                return $this->currentPage > $this->firstPage + 2
                    ? Option::of(PaginationPage::disabled($this->currentPage - 2))
                    : Option::none();
            }
        });
    }

    private function resolvePageBeforeLast(int $lastPage, int $currentPage, Option $pageAfterCurrent): Option
    {
        return $pageAfterCurrent->flatMap(new class($lastPage, $currentPage) implements Function2 {
            private int $lastPage;
            private int $currentPage;

            public function __construct(int $lastPage, int $currentPage)
            {
                $this->lastPage = $lastPage;
                $this->currentPage = $currentPage;
            }

            function apply($value): Option
            {
                return $this->currentPage < $this->lastPage - 2
                    ? Option::of(PaginationPage::disabled($this->currentPage + 2))
                    : Option::none();
            }
        });
    }
}

class GetPaginationPageIndexFunction implements Function2
{
    function apply($value)
    {
        $paginationPage = $value;
        return $paginationPage->index();
    }
}

class ResolvePaginationParams
{
    private string $uri;
    private string $queryParamName;
    private int $currentPage;
    private int $itemsCount;
    private int $pageSize;

    public function __construct(string $uri,
                                string $queryParamName,
                                int    $currentPage,
                                int    $itemsCount,
                                int    $pageSize)
    {
        $this->uri = $uri;
        $this->queryParamName = $queryParamName;
        $this->currentPage = $currentPage;
        $this->itemsCount = $itemsCount;
        $this->pageSize = $pageSize;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function queryParamName(): string
    {
        return $this->queryParamName;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function itemsCount(): int
    {
        return $this->itemsCount;
    }

    public function pageSize(): int
    {
        return $this->pageSize;
    }
}