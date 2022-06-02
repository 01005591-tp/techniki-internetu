<?php

namespace p1\view\home;

require_once "core/domain/book/book-list.php";
require_once "core/domain/book/get-book-list-command-handler.php";

require_once "core/function/function.php";
require_once "core/function/option.php";

require_once "session/session-manager.php";

require_once "view/pagination/pagination.php";
require_once "view/pagination/pagination-service.php";

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
    private PaginationService $paginationService;
    private BookList $bookList;
    private Option $paginationData;

    public function __construct(GetBookListCommandHandler $getBookListCommandHandler,
                                SessionManager            $sessionManager,
                                PaginationService         $paginationService)
    {
        $this->getBookListCommandHandler = $getBookListCommandHandler;
        $this->sessionManager = $sessionManager;
        $this->paginationService = $paginationService;
        $this->bookList = BookList::emptyBookList();
        $this->paginationData = Option::none();
    }

    public function getDefaultBookList(): BookList
    {
        $bookListPage = $this->resolveQueryParams();
        return $this->getBookListPage($bookListPage);
    }

    private function getBookListPage(BookListPage $bookListPage): BookList
    {
        $this->setCurrentPageData($bookListPage);
        $command = new GetBookListCommand($bookListPage->page(), $bookListPage->pageSize());
        $this->bookList = $this->getBookListCommandHandler->handle($command)
            ->fold(
                new GetDefaultBookListFailedFunction($this->bookList),
                FunctionIdentity::instance()
            );
        $this->paginationData = $this->resolvePaginationData();
        return $this->bookList;
    }

    public function paginationData(): Option
    {
        return $this->paginationData;
    }

    private function resolvePaginationData(): Option
    {
        $currentPageData = $this->currentPageData();
        $params = new ResolvePaginationParams(
            '/book-list',
            'page',
            $currentPageData->page(),
            $this->bookList->booksCount(),
            $currentPageData->pageSize()
        );
        return $this->paginationService->resolvePagination($params);
    }

    private function currentPageData(): BookListPage
    {
        return $this->sessionManager->get(SessionConstants::BOOK_LIST_CURRENT_PAGE)
            ->orElseGet(new class implements Supplier {
                function supply(): BookListPage
                {
                    return BookListPage::defaultBookListPage();
                }
            });
    }

    private function setCurrentPageData(BookListPage $bookListPage): void
    {
        $this->sessionManager->put(SessionConstants::BOOK_LIST_CURRENT_PAGE, $bookListPage);
    }

    private function resolveQueryParams(): BookListPage
    {
        if (array_key_exists('page', $_GET)) {
            $pageQueryParam = htmlspecialchars($_GET['page']);
            if (!is_numeric($pageQueryParam) || $pageQueryParam < 1) {
                $pageQueryParam = 1;
            }
            return $this->currentPageData()->withPage($pageQueryParam);
        } else {
            return $this->currentPageData()->withPage(1);
        }
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
