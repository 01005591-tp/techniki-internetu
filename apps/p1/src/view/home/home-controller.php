<?php

namespace p1\view\home;

require_once "core/domain/book/book-list.php";
require_once "core/domain/book/get-book-list-command-handler.php";
require_once "core/function/function.php";
require_once "core/function/option.php";
require_once "session/session-manager.php";
require_once "view/home/pagination.php";

use p1\core\domain\book\BookList;
use p1\core\domain\book\BookListPage;
use p1\core\domain\book\GetBookListCommand;
use p1\core\domain\book\GetBookListCommandHandler;
use p1\core\function\Function2;
use p1\core\function\FunctionIdentity;
use p1\core\function\Option;
use p1\core\function\Supplier;
use p1\view\session\SessionConstants;
use p1\view\session\SessionManager;

class HomeController
{
    private GetBookListCommandHandler $getBookListCommandHandler;
    private SessionManager $sessionManager;
    private BookList $bookList;

    public function __construct(GetBookListCommandHandler $getBookListCommandHandler,
                                SessionManager            $sessionManager)
    {
        $this->getBookListCommandHandler = $getBookListCommandHandler;
        $this->sessionManager = $sessionManager;
        $this->bookList = BookList::emptyBookList();
    }

    public function getDefaultBookList(): BookList
    {
        $bookListPage = $this->currentPageData();
        $command = new GetBookListCommand($bookListPage->page(), $bookListPage->pageSize());
        $this->bookList = $this->getBookListCommandHandler->handle($command)
            ->fold(
                new GetDefaultBookListFailedFunction($this->bookList),
                FunctionIdentity::instance()
            );
        return $this->bookList;
    }

    public function paginationData(): PaginationData
    {
        if ($this->bookList->booksCount() < 1) {
            return PaginationData::emptyPaginationData();
        }

        $currentPageData = $this->currentPageData();
        $pagesCount = ceil($this->bookList->booksCount() / $currentPageData->pageSize());
        $currentPage = $currentPageData->page();
        $firstPage = 1;
        $lastPage = $pagesCount;

        $pageCurrent = Option::of(PaginationPage::active($currentPage));
        $pageFirst = $currentPage === $firstPage ? Option::none() : Option::of(PaginationPage::available($firstPage));
        $pageLast = $currentPage === $lastPage ? Option::none() : Option::of(PaginationPage::available($lastPage));
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
        return new PaginationData($paginationPages);
    }

    private function currentPageData(): BookListPage
    {
        return $this->sessionManager->get(SessionConstants::CURRENT_BOOK_LIST_PAGE)
            ->orElseGet(new class implements Supplier {
                function supply(): BookListPage
                {
                    return BookListPage::defaultBookListPage();
                }
            });
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

class GetDefaultBookListFailedFunction implements Function2
{
    private BookList $currentBookList;

    public function __construct(BookList $currentBookList)
    {
        $this->currentBookList = $currentBookList;
    }

    function apply($value): BookList
    {
        error_log('Default book list not found: ' . $value->message());
        return $this->currentBookList;
    }
}